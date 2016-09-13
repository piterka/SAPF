<?php

namespace SAPF\Decor;

class DecorCallback implements \SAPF\Decor\DecorInterface
{

    protected $_callback;

    public function setCallback($callback)
    {
        if (!($callback instanceof callable)) {
            throw new \SAPF\Decor\DecorException("setCallback() function require object implementing callable");
        }
        $this->_callback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    public function decorate($subject)
    {
        if (!$this->_callback) {
            throw new \SAPF\Decor\DecorException("Callback is empty! Set callback with function setCallback(\$callback)");
        }

        return call_user_func($this->_callback, $subject);
    }

}
