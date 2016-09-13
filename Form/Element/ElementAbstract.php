<?php

namespace SAPF\Form\Element;

abstract class ElementAbstract
{

    use \SAPF\DataBucket\DataBucketTrait;

    // form
    protected $_form;
    // name, value
    protected $_name;
    protected $_value;
    protected $_required            = false;
    // validators
    protected $_validatorChain;
    // filters
    protected $_filterChain;
    // decors
    protected $_decorElement;
    protected $_decorChain;
    // dummy errors
    protected $_dummyError          = [];
    //
    protected $_validatePostFilters = true;

    /**
     * Sets form for element
     * @param \SAPF\Form\Form $form
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function setForm(\SAPF\Form\Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Gets form for element
     * @return type
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Gets validator chain
     * @return \SAPF\Validator\ValidatorChain
     */
    public function getValidatorChain()
    {
        if (!$this->_validatorChain) {
            $this->_validatorChain = new \SAPF\Validator\ValidatorChain();
        }
        return $this->_validatorChain;
    }

    /**
     * Returns TRUE if form element is valid
     * @return boolean
     */
    public function isValid()
    {
        $val = $this->getValue($this->_validatePostFilters);

        // zero digit or nested empty arrays are treated as value
        if (!$this->_required && !$val && $val !== 0 && (!is_array($val) || count($val) === 0)) {
            return count($this->_dummyError) === 0;
        }

        $x1 = $this->_validatorChain ? $this->_validatorChain->validate($this->getValue($this->_validatePostFilters))->isValid() : true;
        if (!$x1) {
            return false;
        }

        return count($this->_dummyError) === 0;
    }

    /**
     * Returns dummy errors
     * @return array
     */
    public function getDummyErrors()
    {
        return $this->_dummyError;
    }

    /**
     * Clears dummy errors
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function clearDummyErrors()
    {
        $this->_dummyError = [];
        return $this;
    }

    /**
     * Removes dummy error with specified key
     * @param string $errorKey
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function removeDummyError($errorKey)
    {
        unset($this->_dummyError[$errorKey]);
        return $this;
    }

    /**
     * Adds dummy error to element
     * @param string $errorKey
     * @param string $errorMsg
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function addDummyError($errorKey, $errorMsg = false)
    {
        $this->_dummyError[$errorKey] = $errorMsg ? : $errorKey;
        return $this;
    }

    /**
     * Gets errors generated by validators
     * @return array
     */
    public function getValidatorErrors()
    {
        return $this->_validatorChain ? $this->_validatorChain->getErrors() : [];
    }

    public function getErrors()
    {
        return $this->getDummyErrors() + $this->getValidatorErrors();
    }

    /**
     * Sets if form element is required
     * @param boolean $required
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function setRequired($required = false)
    {
        $this->_required = $required;
        return $this;
    }

    /**
     * Is form element required?
     * @return boolean
     */
    public function isRequired()
    {
        return $this->_required;
    }

    /**
     * Gets filter chain, that will be used for filter element's value
     * @return \SAPF\Filter\FilterChain
     */
    public function getFilterChain()
    {
        if (!$this->_filterChain) {
            $this->_filterChain = new \SAPF\Filter\FilterChain();
        }
        return $this->_filterChain;
    }

    /**
     * Gets element name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets element name
     * @param string $name
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Gets validate post filters flag
     * If TRUE validators will validate value filtered by filterChain
     * @return boolean
     */
    public function isValidatePostFilters()
    {
        return $this->_validatePostFilters;
    }

    /**
     * Sets validate post filters flag
     * @param boolean $validatePostFilters If TRUE validators will validate value filtered by filterChain
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function setValidatePostFilters($validatePostFilters)
    {
        $this->_validatePostFilters = $validatePostFilters;
        return $this;
    }

    /**
     * Gets element value
     * @param boolean $filtered If true value is filtered
     * @return mixed Value
     */
    public function getValue($filtered = true)
    {
        if ($filtered && $this->_filterChain) {
            return $this->_filterChain->filter($this->_value);
        }
        return $this->_value;
    }

    /**
     * Sets element value
     * @param mixed $value
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Gets element outer decor chain
     * @return \SAPF\Decor\DecorChain Decor chain
     */
    public function getDecorChain()
    {
        if (!$this->_decorChain) {
            $this->_decorChain = new \SAPF\Decor\DecorChain();
        }
        return $this->_decorChain;
    }

    /**
     * Get element decor
     * @return \SAPF\Decor\DecorInterface
     */
    public function getElementDecor()
    {
        if (!$this->_decorElement) {
            $this->_decorElement = $this->_getDecorElement();
            $this->_injectElementToDecor($this->_decorElement);
        }
        return $this->_decorElement;
    }

    /**
     * Renders element and decor it with decorChain
     * @return string
     */
    public function render()
    {
        $element = $this->_renderDecorElement();
        if ($this->_decorChain) {
            $element = $this->_decorChain->decorate($element);
        }

        return $element;
    }

    public function __toString()
    {
        return $this->render();
    }

    // injects Element to all decors implementing \SAPF\Form\FormElementAwareInterface
    protected function _injectElementToDecor($decor)
    {
        if ($decor instanceof \SAPF\Form\FormElementAwareInterface) {
            $decor->setElement($this);
        }
        else if ($decor instanceof \SAPF\Decor\DecorChain) {
            foreach ($decor->getDecors() as $d) {
                $this->_injectElementToDecor($d);
            }
        }
    }

    // abstracts
    abstract protected function _renderDecorElement();

    abstract protected function _getDecorElement();
}