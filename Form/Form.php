<?php

namespace SAPF\Form;

class Form
{

    use \SAPF\DataBucket\DataBucketTrait;

    // METHODS
    const FORM_METHOD_POST                 = "POST";
    const FORM_METHOD_GET                  = "GET";
    // ENCTYPES
    const FORM_ENCTYPE_TEXT_PLAIN          = "text/plain";
    const FORM_ENCTYPE_MULTIPART_FORM_DATA = "multipart/form-data";
    const FORM_ENCTYPE_URL_ENCODED         = "application/x-www-form-urlencoded";

    // form decor
    protected $_formDecor;
    protected $_decor;
    // attrs
    protected $_enctype = '';
    protected $_action  = '';
    protected $_method  = self::FORM_METHOD_POST;
    // form elements
    protected $_elements;

    /**
     * Set decor on elements
     * @param \SAPF\Decor\DecorInterface $decor
     * @return \SAPF\Form\Form
     * @throws \InvalidArgumentException
     */
    public function setDecorOnElements($decor)
    {
        if (!$decor) {
            $this->_decor = $decor;
            return $this;
        }

        if (!($decor instanceof \SAPF\Decor\DecorInterface)) {
            throw new \InvalidArgumentException("setDecorOnElements() function require object implementing \SAPF\Decor\DecorInterface");
        }

        $this->_decor = $decor;
        return $this;
    }

    /**
     * Get decor on elements
     * @return \SAPF\Decor\DecorInterface
     */
    public function getDecorOnElements()
    {
        return $this->_decor;
    }

    /**
     * Get form decor 
     * @return \SAPF\Form\Decor\FormDecor
     */
    public function getFormDecor()
    {
        if (!$this->_formDecor) {
            $this->_formDecor = $this->_getDecorElement();
            $this->_formDecor->setAttr('method', $this->_method);
            $this->_formDecor->setAttr('action', $this->_action);
            $this->_formDecor->setAttr('encType', $this->_enctype);
        }

        return $this->_formDecor;
    }

    // form encType
    public function setEncType($encType)
    {
        $this->_enctype = $encType;
        if ($this->_formDecor) {
            $this->_formDecor->setAttr('encType', $encType);
        }
        return $this;
    }

    public function getEncType()
    {
        return $this->_enctype;
    }

    // form action
    public function setAction($action)
    {
        $this->_action = $action;
        if ($this->_formDecor) {
            $this->_formDecor->setAttr('action', $action);
        }
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    // form method
    public function setMethod($method)
    {
        $this->_method = $method;
        if ($this->_formDecor) {
            $this->_formDecor->setAttr('method', $method);
        }
        return $this;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    // form elements
    public function getElements()
    {
        return $this->_elements;
    }

    public function hasElement($name)
    {
        return isset($this->_elements[$name]);
    }

    /**
     * Get element
     * @param string $name Element name
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function getElement($name)
    {
        return $this->_elements[$name];
    }

    public function setElement($name, \SAPF\Form\Element\ElementAbstract $element)
    {
        $this->_elements[$name] = $element;
        $element->setForm($this);
    }

    public function addElement(\SAPF\Form\Element\ElementAbstract $element)
    {
        $this->setElement($element->getName(), $element);
        return $this;
    }

    public function removeElement($name)
    {
        unset($this->_elements[$name]);
        return $this;
    }

    // form data
    public function setData($data)
    {
        $this->_setData($data);
        return $this;
    }

    public function getData($filtered = true)
    {
        $data = array();
        foreach ($this->getElements() as $name => $element) {
            $data[$name] = $element->getValue($filtered);
        }
        return $data;
    }

    // form validation & errors
    public function isValid($validatePostFilters = false)
    {
        $ret = true;
        foreach ($this->getElements() as $element) {
            if (!$element->isValid($validatePostFilters)) {
                $ret = false;
            }
        }

        return $ret;
    }

    public function getErrors()
    {
        $ret = array();
        foreach ($this->getElements() as $name => $element) {
            if (count($element->getErrors()) > 0) {
                $ret[$name] = array();
                foreach ($element->getErrors() as $error) {
                    $ret[$name][] = $error;
                }
            }
        }

        return $ret;
    }

    public function render()
    {
        $decor = $this->getFormDecor();

        // build elements
        $formBody = "";
        foreach ($this->getElements() as $element) {
            $formBody .= $element->render();
        }

        // apply decor on elements
        if ($this->_decor) {
            $formBody = $this->_decor->decorate($formBody);
        }

        return $decor->decorate($formBody);
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Returns form decor
     * @return \SAPF\Form\Decor\FormDecor
     */
    protected function _getDecorElement()
    {
        $decor = new Decor\FormDecor();
        $decor->setForm($this);
        return $decor;
    }

    protected function _setData($data, $prefix = false)
    {
        foreach ($data as $k => $v) {
            $name = ($prefix ? $prefix . "[" . $k . "]" : $k);
            if ($this->hasElement($name)) {
                $this->getElement($name)->setValue($v);
                continue;
            }

            if (is_array($v)) {
                $this->_setData($v, $k);
            }
        }
    }

}
