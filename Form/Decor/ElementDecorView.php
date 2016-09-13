<?php

namespace SAPF\Form\Decor;

class ElementDecorView extends \SAPF\Decor\DecorView implements \SAPF\Form\FormElementAwareInterface
{

    use \SAPF\Form\FormElementAwareTrait;

    protected $_elementVariableName = "object";

    public function setElementVariableName($elementVariableName)
    {
        $this->_elementVariableName = $elementVariableName;
        return $this;
    }

    public function getElementVariableName()
    {
        return $this->_elementVariableName;
    }

    public function decorate($subject)
    {
        if ($this->getViewContainer()) {
            $this->getViewContainer()->{$this->_elementVariableName} = $this->getElement();
        }

        return parent::decorate($subject);
    }

}
