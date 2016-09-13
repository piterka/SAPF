<?php

namespace SAPF\Form\Element;

class Select extends \SAPF\Form\Element\ElementAbstract
{

    protected $_multiple = false;
    protected $_option;

    public function setMultiple($multiple)
    {
        $this->_multiple = $multiple;
        return $this;
    }

    public function isMultiple()
    {
        return $this->_multiple;
    }

    public function setOptions($options)
    {
        $this->_option = $options;
        return $this;
    }

    public function addOption($value, $title)
    {
        $this->_option[$value] = $title;
    }

    public function removeOption($value)
    {
        unset($this->_option[$value]);
        return $this;
    }

    public function clearOptions()
    {
        $this->setOptions(array());
        return $this;
    }

    public function addOptions($options)
    {
        foreach ($options as $k => $v) {
            $this->addOption($k, $v);
        }
        return $this;
    }

    public function getOptions()
    {
        return $this->_option;
    }

    protected function _getDecorElement()
    {
        $decor = new \SAPF\Decor\DecorHtmlTag();
        $decor->setTag('select');
        $decor->setShort(false);
        return $decor;
    }

    protected function _renderDecorElement()
    {
        $decor = $this->getElementDecor();
        if ($this->_multiple) {
            $decor->setAttr('multiple', 'multiple');
            $decor->setAttr('name', $this->getName() . '[]');
        }
        else {
            $decor->setAttr('name', $this->getName());
        }

        return $decor->decorate($this->_renderOptions());
    }

    protected function _renderOptions()
    {
        $options = "";
        foreach ($this->_option as $value => $name) {
            $options .= '   <option' . (($this->_multiple ? in_array($value, $this->getValue()) : $this->getValue() == $value) ? ' selected="selected"' : '') . ' value="' . $value . '">' . $name . '</option>' . PHP_EOL;
        }
        return $options;
    }

}
