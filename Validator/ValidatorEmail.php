<?php

namespace SAPF\Validator;

class ValidatorEmail extends \SAPF\Validator\ValidatorAbstract
{

    const ERROR_BAD_EMAIL = "ERROR_BAD_EMAIL";

    protected $_errorMsg = array(
        'ERROR_BAD_EMAIL' => 'Wartość: "{val}" nie jest poprawnym emailem!',
    );
    protected $_vars     = array(
        '{val}' => '_inputVal',
    );

    protected function _validate($input)
    {
        if (!filter_var(trim($input), FILTER_VALIDATE_EMAIL)) {
            $this->_error(self::ERROR_BAD_EMAIL);
        }

        return $this;
    }

}
