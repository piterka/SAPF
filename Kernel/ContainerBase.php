<?php

namespace SAPF\Kernel;

class ContainerBase extends \SAPF\DI\Container
{

    /**
     * Set trusted proxies
     * If $trustedIps is false use ips: 127.0.0.1 and $_SERVER['REMOTE_ADDR']
     * @param array $trustedIps
     */
    public function setTrustedProxies($trustedIps = false)
    {
        // setTrustedProxies if vhost uses loadBalancer or proxy 
        \Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedIps ? : array('127.0.0.1', $this->request->server->get('REMOTE_ADDR')));
    }

    // core
    protected function _request()
    {
        $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        return $this->request;
    }

    protected function _session()
    {
        $this->session = new \Symfony\Component\HttpFoundation\Session\Session();

        return $this->session;
    }

    protected function _httpKernel()
    {
        return $this->httpKernel = new \SAPF\Kernel\HttpKernel($this->eventDispatcher, $this->controllerResolver);
    }

    protected function _eventDispatcher()
    {
        return $this->eventDispatcher = new \SAPF\Event\EventDispatcher();
    }

    protected function _controllerResolver()
    {
        return $this->controllerResolver = new \SAPF\Kernel\ControllerResolver();
    }

}
