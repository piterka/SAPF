<?php

namespace SAPF\Form\Element;

class Input extends \SAPF\Form\Element\ElementAbstract
{

    protected $_type = "text";

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    protected function _getDecorElement()
    {
        $decor = new \SAPF\Decor\DecorHtmlTag();
        $decor->setTag('input');
        $decor->setShort(true);
        return $decor;
    }

    protected function _renderDecorElement()
    {
        $decor = $this->getElementDecor();
        $decor->setAttr('name', $this->getName());
        $decor->setAttr('type', $this->getType());

        if (in_array($this->getType(), ['checkbox', 'radio'])) {
            $decor->setAttr('value', '1');
            if ($this->getValue()) {
                $decor->setAttr('checked', 'checked');
            }
        }
        else {
            $decor->setAttr('value', $this->getValue());
        }

        return $decor->decorate("");
    }

}
