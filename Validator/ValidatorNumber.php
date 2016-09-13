<?php

namespace SAPF\Validator;

class ValidatorNumber extends \SAPF\Validator\ValidatorAbstract
{

    const ERROR_TOO_LARGE  = "ERROR_TOO_LARGE";
    const ERROR_TOO_SMALL  = "ERROR_TOO_SMALL";
    const ERROR_NOT_NUMBER = "ERROR_NOT_NUMBER";

    protected $_errorMsg = array(
        'ERROR_NOT_NUMBER' => 'Wartość: "{val}" nie jest liczbą!',
        'ERROR_TOO_LARGE'  => 'Wartość: "{val}" jest zbyt duża! Maksimum to: {max}',
        'ERROR_TOO_SMALL'  => 'Wartość: "{val}" jest zbyt mała! Minimum to: {min}',
    );
    protected $_vars     = array(
        '{val}' => '_inputVal',
        '{min}' => '_min',
        '{max}' => '_max',
    );
    protected $_min      = FALSE;
    protected $_max      = FALSE;

    public function setMin($min)
    {
        $this->_min = $min;
        return $this;
    }

    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }

    public function getMin()
    {
        return $this->_min;
    }

    public function getMax()
    {
        return $this->_max;
    }

    protected function _validate($input)
    {
        $input = trim($input);
        if (!ctype_digit($input)) {
            $this->_error(self::ERROR_NOT_NUMBER);
            return $this;
        }
        $int = intval($input);
        if ($this->_max !== FALSE && $int > $this->_max) {
            $this->_error(self::ERROR_TOO_LARGE);
        }
        if ($this->_min !== FALSE && $int < $this->_min) {
            $this->_error(self::ERROR_TOO_SMALL);
        }
        return $this;
    }

}
