<?php

namespace SAPF\Validator;

class ValidatorCallback extends \SAPF\Validator\ValidatorAbstract
{

    protected $_callback;

    public function setCallback($callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    protected function _validate($input)
    {
        if (!$this->_callback) {
            throw new \SAPF\Validator\ValidatorException("Callback is empty! Set callback with function setCallback(\$callback)");
        }

        $ret = call_user_func($this->_callback, $input);
        if ($ret) {
            if (is_array($ret)) {
                foreach ($ret as $er) {
                    $this->_error($er);
                }
            }
            else {
                $this->_error($ret);
            }
        }
        return $this;
    }

}
