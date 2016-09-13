<?php

namespace SAPF\Form;

class FormBuilder
{

    /**
     * Create form object from options array
     * @param type $options
     * @return \SAPF\Form\Form
     */
    public static function build($options = [])
    {
        $form = new Form();

        if ($options['element']) {
            if (is_array($options['element'])) {
                foreach ($options['element'] as $e) {
                    $form->addElement($e instanceof Element\ElementAbstract ? $e : ElementBuilder::build($e));
                }
            }
            else {
                $form->addElement($e instanceof Element\ElementAbstract ? $options['element'] : ElementBuilder::build($options['element']));
            }
        }
        unset($options['element']);

        if ($options['bucketData']) {
            if (is_array($options['bucketData'])) {
                foreach ($options['bucketData'] as $k => $v) {
                    $form->bucket()->set($k, $v);
                }
            }
            else {
                $form->bucket()->set('bucketData', $options['bucketData']);
            }
        }
        unset($options['bucketData']);

        if ($options['attr']) {
            if (is_array($options['attr'])) {
                foreach ($options['attr'] as $k => $v) {
                    $form->getFormDecor()->setAttr($k, $v);
                }
            }
        }
        unset($options['attr']);

        if ($options['class']) {
            if (is_array($options['class'])) {
                $form->getFormDecor()->setClasses($options['class']);
            }
            else {
                $form->getFormDecor()->setAttr('class', $options['class']);
            }
        }
        unset($options['class']);

        if ($options['style']) {
            if (is_array($options['style'])) {
                $form->getFormDecor()->setStyles($options['style']);
            }
            else {
                $form->getFormDecor()->setAttr('style', $options['style']);
            }
        }
        unset($options['style']);

        foreach ($options as $k => $v) {
            if (method_exists($form, "set" . ucfirst($k))) {
                $form->{"set" . ucfirst($k)}($v);
            }
            if (method_exists($form, $k)) {
                $form->{$k}($v);
            }
        }

        return $form;
    }

}
