<?php

namespace SAPF\Kernel\Event;

class FilterControllerEvent extends KernelEvent
{

    const EVENT_NAME = 'KernelEvent.FilterControllerEvent';

    protected $_controller;

    public function __construct(\SAPF\Kernel\HttpKernelInterface $kernel, callable $controller, \Symfony\Component\HttpFoundation\Request $request)
    {
        parent::__construct($kernel, $request);
        $this->_controller = $controller;
    }

    /**
     * Get controller
     * @return callable
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Set controller
     * @param callable $controller
     */
    public function setController(callable $controller)
    {
        $this->_controller = $controller;
    }

}
