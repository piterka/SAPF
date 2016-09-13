<?php

namespace SAPF\Router;

class SimpleRouterListener
{

    protected $_container;
    protected $_routes;

    public function __construct(\SAPF\Event\EventDispatcher $eventDispatcher, \SAPF\DI\ContainerInterface $container, $config = null)
    {
        $this->_container = $container;
        $eventDispatcher->addListener(\SAPF\Kernel\Event\HandleRequestEvent::EVENT_NAME, array($this, 'onHttpKernelHandleRequestEvent'));

        $this->_routes = array();
        if ($config) {
            foreach ($config as $route) {
                if ($route['path']) {
                    if (is_array($route['path'])) {
                        foreach ($route['path'] as $r_path) {
                            $this->registerRoute($r_path, $route['controller'], $route['controllerParams'] ? : array(), $route['methods'] ? $route['methods'] : '*', $route['redirect']);
                        }
                    }
                    else {
                        $this->registerRoute($route['path'], $route['controller'], $route['controllerParams'] ? : array(), $route['methods'] ? $route['methods'] : '*', $route['redirect']);
                    }
                }
                else {
                    $this->registerRoute('*', $route['controller'], $route['controllerParams'] ? : array(), $route['methods'] ? $route['methods'] : '*', $route['redirect']);
                }
            }
        }
    }

    public function registerRoute($path, $controller, $controllerParams = array(), $methods = array("GET"), $redirect = false)
    {
        if (!is_array($methods) && $methods != "*") {
            $methods = array($methods);
        }

        $this->_routes[strtolower($path)] = array(
            'path'             => $path,
            'methods'          => $methods,
            'controller'       => $controller,
            'controllerParams' => $controllerParams,
            'redirect'         => $redirect,
        );
    }

    public function onHttpKernelHandleRequestEvent(\SAPF\Kernel\Event\HandleRequestEvent $event, $eventName)
    {
        $requestMethod = $event->getRequest()->getMethod();
        $requestUrl    = $event->getRequest()->getPathInfo();

        // strip GET variables from URL
        if (($pos = strpos($requestUrl, '?')) !== false) {
            $requestUrl = substr($requestUrl, 0, $pos);
        }

        $requestTmp = explode("/", $requestUrl);
        $request    = array();
        foreach ($requestTmp as $r) {
            if ($r) {
                $request[] = $r;
            }
        }
        unset($requestTmp);

        $choosed = null;
        foreach ($this->_routes as $route) {
            if ($route['methods'] != "*" && !in_array($requestMethod, $route['methods'])) {
                continue;
            }

            if ($route['path'] == "*") {
                continue;
            }

            $requestTmpRoute = explode("/", $route['path']);
            $requestRoute    = array();
            foreach ($requestTmpRoute as $r) {
                if ($r) {
                    $requestRoute[] = $r;
                }
            }
            unset($requestTmpRoute);

            if (count($request) != count($requestRoute)) {
                continue;
            }

            $route['vars'] = array();
            $cont          = false;
            $replaced      = array();
            for ($i = 0; $i < count($request); $i++) {
                $req       = $request[$i];
                $routerReq = $requestRoute[$i];

                if (strlen($routerReq) > 2 && $routerReq[0] == "{" && $routerReq[strlen($routerReq) - 1] == "}") {
                    $var                 = substr($routerReq, 1, strlen($routerReq) - 2);
                    $route['vars'][$var] = $req;
                    $replaced[]          = $var;
                }
                else if (strtolower($req) != strtolower($routerReq)) {
                    $cont = true;
                    break;
                }
            }
            if ($cont) {
                continue;
            }

            $choosed = $route;
            break;
        }

        $controller = false;
        $params     = array();

        if ($choosed) {
            if (isset($choosed['redirect'])) {
                $url = $choosed['redirect'];
                if (isset($choosed['vars'])) {
                    foreach ($choosed['vars'] as $k => $v) {
                        $params[$k] = $v;
                        $url        = str_replace("{{$k}}", $v, $url);
                    }
                }
                $event->setResponse(new \Symfony\Component\HttpFoundation\RedirectResponse($url, isset($choosed['redirectCode']) ? $choosed['redirectCode'] : 302));
                return;
            }

            $controller = $choosed['controller'];
            if (isset($choosed['controllerParams'])) {
                foreach ($choosed['controllerParams'] as $k => $v) {
                    $params[$k] = $v;
                }
            }
            if (isset($choosed['vars'])) {
                foreach ($choosed['vars'] as $k => $v) {
                    $params[$k] = $v;
                }
            }
        }

        $event->getRequest()->attributes->add($params);
        $event->getRequest()->attributes->set('_container', $this->_container);
        $event->getRequest()->attributes->set('_controller', $controller);
    }

}
