<?php

namespace SAPF\Router;

class RouterListener
{

    protected $_router;
    protected $_container;

    public function __construct(RouterInterface $router, \SAPF\Event\EventDispatcher $eventDispatcher, \SAPF\DI\ContainerInterface $container = null)
    {
        $this->_router    = $router;
        $this->_container = $container;
        $eventDispatcher->addListener(\SAPF\Kernel\Event\HandleRequestEvent::EVENT_NAME, array($this, 'onHttpKernelHandleRequestEvent'));
    }

    public function onHttpKernelHandleRequestEvent(\SAPF\Kernel\Event\HandleRequestEvent $event, $eventName)
    {
        $controller = false;
        $params     = array();

        // TODO: router

        $event->getRequest()->attributes->add($params);
        $event->getRequest()->attributes->set('_container', $this->_container);
        $event->getRequest()->attributes->set('_controller', $controller);
    }

}
