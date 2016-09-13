<?php

namespace SAPF\Kernel;

interface ControllerResolverInterface
{

    public function getController(\Symfony\Component\HttpFoundation\Request $request);

    public function getArguments(\Symfony\Component\HttpFoundation\Request $request, $controller);
}
