<?php

namespace SAPF\Form;

interface FormElementAwareInterface
{

    /**
     * Sets form element
     * @param \SAPF\Form\Element\ElementAbstract $element
     */
    public function setElement(\SAPF\Form\Element\ElementAbstract $element);
}
