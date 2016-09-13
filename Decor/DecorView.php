<?php

namespace SAPF\Decor;

class DecorView implements \SAPF\Decor\DecorInterface, \SAPF\View\ViewContainerAwareInterface
{

    use \SAPF\View\ViewContainerAwareTrait;

    protected $_variableName = "decorSubject";
    protected $_view;

    /**
     * Sets subject variable name avaible in view
     * @param string $variableName
     * @return \SAPF\Decor\DecorView
     */
    public function setVariableName($variableName)
    {
        $this->_variableName = $variableName;
        return $this;
    }

    /**
     * Gets subject variable name avaible in view
     * @return string
     */
    public function getVariableName()
    {
        return $this->_variableName;
    }

    /**
     * Sets view to render
     * @param string $view
     * @return \SAPF\Decor\DecorView
     */
    public function setView($view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Gets view to render
     * @return type
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Decorate subject with View
     * @param type $subject
     * @return type
     * @throws \SAPF\Decor\DecorException
     */
    public function decorate($subject)
    {
        if (!$this->getViewContainer()) {
            throw new \SAPF\Decor\DecorException("ViewContainer is empty! Set it with function setViewContainer(\SAPF\View\ViewContainer \$vc)");
        }
        if (!$this->_view) {
            throw new \SAPF\Decor\DecorException("View is empty! Set it with function setView(\$view)");
        }

        $this->getViewContainer()->{$this->_variableName} = $subject;
        return $this->getViewContainer()->render($this->_view, false);
    }

}
