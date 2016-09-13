<?php

namespace SAPF\Kernel\Event;

class HandleRequestExceptionEvent extends HandleRequestEvent
{

    const EVENT_NAME = 'KernelEvent.HandleRequestExceptionEvent';

    protected $_exception;

    public function __construct(\SAPF\Kernel\HttpKernelInterface $kernel, \Symfony\Component\HttpFoundation\Request $request, \Exception $e)
    {
        parent::__construct($kernel, $request);
        $this->_exception = $e;
    }

    /**
     * Returns exception
     * @return \Exception
     */
    public function getException()
    {
        return $this->_exception;
    }

    /**
     * Set exception
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->_exception = $exception;
    }

}
