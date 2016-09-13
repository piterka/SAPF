<?php

namespace SAPF\Form\Element;

class Textarea extends \SAPF\Form\Element\ElementAbstract
{

    protected function _getDecorElement()
    {
        $decor = new \SAPF\Decor\DecorHtmlTag();
        $decor->setTag('textarea');
        $decor->setShort(false);
        return $decor;
    }

    protected function _renderDecorElement()
    {
        $decor = $this->getElementDecor();
        $decor->setAttr('name', $this->getName());
        return $decor->decorate($this->getValue());
    }

}
