<?php

namespace SAPF\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpKernel implements HttpKernelInterface
{

    protected $_eventDispatcher;
    protected $_controllerResolver;

    public function __construct(\SAPF\Event\EventDispatcherInterface $eventDispatcher, ControllerResolverInterface $controllerResolver)
    {
        $this->_eventDispatcher    = $eventDispatcher;
        $this->_controllerResolver = $controllerResolver;
    }

    public function setEventDispatcher(\SAPF\Event\EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
        return $this;
    }

    public function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    public function setControllerResolver(ControllerResolverInterface $resolver)
    {
        $this->_controllerResolver = $resolver;
        return $this;
    }

    public function getControllerResolver()
    {
        return $this->_controllerResolver;
    }

    public function handle(Request $request, $catchExceptions = true)
    {
        try {
            return $this->_handleRaw($request);
        } catch (\Exception $e) {
            if (!$catchExceptions) {
                $this->_finishRequest($request);
                throw $e;
            }
            return $this->_handleException($e, $request);
        }
    }

    protected function _handleRaw(Request $request)
    {
        // request event
        $event = new Event\HandleRequestEvent($this, $request);
        $this->_eventDispatcher->dispatch(Event\HandleRequestEvent::EVENT_NAME, $event);
        if ($event->hasResponse()) {
            return $this->_filterResponse($event->getResponse(), $request);
        }

        // load controller
        if (false === $controller = $this->_controllerResolver->getController($request)) {
            throw new NotFoundException(sprintf('Unable to find the controller for path "%s".', $request->getPathInfo()));
        }

        // event filter controller event
        $event      = new Event\FilterControllerEvent($this, $controller, $request);
        $this->_eventDispatcher->dispatch(Event\FilterControllerEvent::EVENT_NAME, $event);
        $controller = $event->getController();

        // controller arguments
        $arguments = $this->_controllerResolver->getArguments($request, $controller);

        // call controller
        $response = call_user_func_array($controller, $arguments);

        // view
        if (!$response instanceof Response) {
            throw new HttpKernelException('Controller must return a Response object.');
        }

        return $this->_filterResponse($response, $request);
    }

    protected function _handleException(\Exception $e, Request $request)
    {
        $event = new Event\HandleRequestExceptionEvent($this, $request, $e);
        $this->_eventDispatcher->dispatch(Event\HandleRequestExceptionEvent::EVENT_NAME, $event);

        $e = $event->getException();
        if (!$event->hasResponse()) {
            $this->_finishRequest($request);
            throw $e;
        }

        $response = $event->getResponse();

        try {
            return $this->_filterResponse($response, $request);
        } catch (\Exception $e) {
            return $response;
        }
    }

    protected function _filterResponse(Response $response, Request $request)
    {
        $event = new Event\FilterResponseEvent($this, $request, $response);
        $this->_eventDispatcher->dispatch(Event\FilterResponseEvent::EVENT_NAME, $event);
        $this->_finishRequest($request);
        return $event->getResponse();
    }

    protected function _finishRequest(Request $request)
    {
        $this->_eventDispatcher->dispatch(Event\FinishRequestEvent::EVENT_NAME, new Event\FinishRequestEvent($this, $request));
    }

}
