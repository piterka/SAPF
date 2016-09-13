<?php

namespace SAPF\Form\Element;

class View extends \SAPF\Form\Element\ElementAbstract implements \SAPF\View\ViewContainerAwareInterface
{

    use \SAPF\View\ViewContainerAwareTrait;

    protected $_view;

    function getView()
    {
        return $this->_view;
    }

    function setView($view)
    {
        $this->_view = $view;
        return $this;
    }

    protected function _getDecorElement()
    {
        return (new \SAPF\Decor\DecorHtmlTag())->setTag("div")->setShort(false);
    }

    protected function _renderDecorElement()
    {
        $decor  = (new \SAPF\Form\Decor\ElementDecorView())->setElement($this)->setViewContainer($this->getViewContainer())->setView($this->_view);
        $decor2 = $this->getElementDecor();
        return $decor2->decorate($decor->decorate($this));
    }

}
