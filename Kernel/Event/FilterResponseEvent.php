<?php

namespace SAPF\Kernel\Event;

class FilterResponseEvent extends KernelEvent
{

    const EVENT_NAME = 'KernelEvent.FilterResponseEvent';

    protected $_response;

    public function __construct(\SAPF\Kernel\HttpKernelInterface $kernel, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Response $response)
    {
        parent::__construct($kernel, $request);
        $this->_response = $response;
    }

    /**
     * Returns response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->_response = $response;
    }

}
