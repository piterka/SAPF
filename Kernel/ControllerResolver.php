<?php

namespace SAPF\Kernel;

class ControllerResolver implements ControllerResolverInterface
{

    public function getController(\Symfony\Component\HttpFoundation\Request $request)
    {
        $callable = $this->_resolveCallable($request);

        if (!$callable) {
            return false;
        }

        // inject container to all ContainerAwareInterface controllers
        $container = $request->attributes->get('_container');
        if ($container && is_array($callable) && is_object($callable[0]) && $callable[0] instanceof \SAPF\DI\ContainerAwareInterface) {
            $callable[0]->setContainer($container);
        }

        return $callable;
    }

    public function getArguments(\Symfony\Component\HttpFoundation\Request $request, $controller)
    {
        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        }
        elseif (is_object($controller) && !$controller instanceof \Closure) {
            $r = new \ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        }
        else {
            $r = new \ReflectionFunction($controller);
        }

        return $this->_doGetArguments($request, $controller, $r->getParameters());
    }

    protected function _resolveCallable($request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            return false;
        }

        if (is_array($controller)) {
            return $controller;
        }

        if (is_object($controller)) {
            if (method_exists($controller, '__invoke')) {
                return array($controller, '__invoke');
            }

            throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not callable.', get_class($controller), $request->getPathInfo()));
        }

        if (false === strpos($controller, ':')) {
            if (method_exists($controller, '__invoke')) {
                return array(new $controller, '__invoke');
            }
            elseif (function_exists($controller)) {
                return $controller;
            }
        }

        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $callable = array(new $class(), $method);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('The controller for URI "%s" is not callable.', $request->getPathInfo()));
        }

        return $callable;
    }

    protected function _doGetArguments(\Symfony\Component\HttpFoundation\Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $arguments  = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            }
            elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            }
            elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            }
            else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                }
                elseif (is_object($controller)) {
                    $repr = get_class($controller);
                }
                else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }

        return $arguments;
    }

}
