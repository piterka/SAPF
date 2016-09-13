<?php

namespace SAPF\Kernel;

class Controller implements \SAPF\DI\ContainerAwareInterface
{

    use \SAPF\DI\ContainerAwareTrait;

    public function __get($name)
    {
        if ($this->getContainer() !== FALSE) {
            return $this->getContainer()->get($name);
        }
    }

    public function __call($name, $arguments)
    {
        if ($this->getContainer() !== FALSE) {
            return call_user_func_array(array($this->getContainer(), $name), $arguments);
        }
    }

}
