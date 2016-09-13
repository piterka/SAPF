<?php

namespace SAPF\Kernel;

interface HttpKernelInterface
{

    /**
     * Handles HTTP request
     * @param \SAPF\Request\Request $request Request
     * @param boolean $catchExceptions
     * @return \Symfony\Component\HttpFoundation\Response Response
     */
    public function handle(\Symfony\Component\HttpFoundation\Request $request, $catchExceptions = true);
}
