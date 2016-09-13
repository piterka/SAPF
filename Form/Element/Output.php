<?php

namespace SAPF\Form\Element;

class Output extends \SAPF\Form\Element\ElementAbstract
{

    protected function _getDecorElement()
    {
        $decor = new \SAPF\Decor\DecorHtmlTag();
        $decor->setTag('output');
        $decor->setShort(true);
        return $decor;
    }

    protected function _renderDecorElement()
    {
        $decor = $this->getElementDecor();
        $decor->setAttr('name', $this->getName());
        $decor->setAttr('value', $this->getValue());
        return $decor->decorate("");
    }

}
