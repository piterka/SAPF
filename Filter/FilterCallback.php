<?php

namespace SAPF\Filter;

class FilterCallback implements \SAPF\Filter\FilterInterface
{

    protected $_callback;

    public function __construct($callback = null)
    {
        if ($callback != null) {
            if (!is_callable($callback)) {
                throw new \SAPF\Filter\FilterException("FilterCallback __construct() function require callable object");
            }
            $this->_callback = $callback;
        }
    }

    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \SAPF\Filter\FilterException("setCallback() function require callable object");
        }
        $this->_callback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    public function filter($input)
    {
        if (!$this->_callback) {
            throw new \SAPF\Filter\FilterException("Callback is empty! Set callback with function setCallback(\$callback)");
        }

        return call_user_func($this->_callback, $input);
    }

}
