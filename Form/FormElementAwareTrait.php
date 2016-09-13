<?php

namespace SAPF\Form;

trait FormElementAwareTrait
{

    protected $_formElement;

    /**
     * Sets form element
     * @param \SAPF\Form\Element\ElementAbstract $element
     */
    public function setElement(\SAPF\Form\Element\ElementAbstract $element)
    {
        $this->_formElement = $element;
        return $this;
    }

    /**
     * Returns form element
     * @return \SAPF\Form\Element\ElementAbstract
     */
    public function getElement()
    {
        return $this->_formElement;
    }

}
