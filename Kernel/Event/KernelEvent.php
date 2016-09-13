<?php

namespace SAPF\Kernel\Event;

class KernelEvent extends \SAPF\Event\Event
{

    protected $_request;
    protected $_kernel;

    public function __construct(\SAPF\Kernel\HttpKernelInterface $kernel, \Symfony\Component\HttpFoundation\Request $request)
    {
        $this->_kernel  = $kernel;
        $this->_request = $request;
    }

    public function getKernel()
    {
        return $this->_kernel;
    }

    /**
     * Returns request
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

}
