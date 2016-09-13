<?php

namespace SAPF\Kernel\Event;

class HandleRequestEvent extends KernelEvent
{

    const EVENT_NAME = 'KernelEvent.HandleRequestEvent';

    private $_response = null;

    /**
     * Returns response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Sets response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->_response = $response;
    }

    /**
     * Event has respose?
     * @return boolean
     */
    public function hasResponse()
    {
        return $this->_response != null;
    }

}
