<?php

namespace SAPF\Form\Decor;

class FormDecor extends \SAPF\Decor\DecorHtmlTag
{

    protected $_tag = 'form';
    protected $_form;

    /**
     * Set form
     * @param \SAPF\Form\Form $form
     * @return \SAPF\Form\Decor\FormDecor
     */
    public function setForm(\SAPF\Form\Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Returns form
     * @return \SAPF\Form\Form
     */
    public function getForm()
    {
        return $this->_form;
    }

}
