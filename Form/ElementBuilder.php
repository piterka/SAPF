<?php

namespace SAPF\Form;

class ElementBuilder
{

    public static function build($options = [])
    {
        $element = null;

        if ($options['eclass']) {
            if ($options['eclass'] instanceof \SAPF\Form\Element\ElementAbstract) {
                $element = $options['eclass'];
            }
            else {
                $c = $options['eclass'];
                if (strpos($c, "\\") !== 0) {
                    $c = "\\SAPF\\Form\\Element\\" . $c;
                }
                $element = new $c();
                if (!($element instanceof \SAPF\Form\Element\ElementAbstract)) {
                    throw new \InvalidArgumentException("Class: " . $c . " dont implement ElementAbstract");
                }
            }
        }
        else {
            $element = new Element\Input();
        }
        unset($options['eclass']);

        if ($options['valid'] && is_array($options['valid'])) {
            foreach ($options['valid'] as $v) {
                $element->getValidatorChain()->addValidator($v);
            }
        }
        unset($options['valid']);

        if ($options['filter'] && is_array($options['filter'])) {
            foreach ($options['filter'] as $v) {
                $element->getFilterChain()->addFilter($v);
            }
        }
        unset($options['filter']);

        if ($options['bucketData']) {
            if (is_array($options['bucketData'])) {
                foreach ($options['bucketData'] as $k => $v) {
                    $element->bucket()->set($k, $v);
                }
            }
            else {
                $element->bucket()->set('bucketData', $options['bucketData']);
            }
        }
        unset($options['bucketData']);

        if ($options['attr']) {
            if (is_array($options['attr'])) {
                foreach ($options['attr'] as $k => $v) {
                    $element->getElementDecor()->setAttr($k, $v);
                }
            }
        }
        unset($options['attr']);

        if ($options['class']) {
            if (is_array($element)) {
                $element->getElementDecor()->setClasses($options['class']);
            }
            else {
                $element->getElementDecor()->setAttr('class', $options['class']);
            }
        }
        unset($options['class']);

        if ($options['style']) {
            if (is_array($element)) {
                $element->getElementDecor()->setStyles($options['style']);
            }
            else {
                $element->getElementDecor()->setAttr('style', $options['style']);
            }
        }
        unset($options['style']);

        foreach ($options as $k => $v) {
            if (method_exists($element, "set" . ucfirst($k))) {
                $element->{"set" . ucfirst($k)}($v);
            }
            if (method_exists($element, $k)) {
                $element->{$k}($v);
            }
        }

        return $element;
    }

}
