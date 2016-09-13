<?php

namespace SAPF\Validator;

class ValidatorUrl extends \SAPF\Validator\ValidatorAbstract
{

    const ERROR_BAD_URL = "ERROR_BAD_URL";

    protected $_errorMsg = array(
        'ERROR_BAD_URL' => 'Wartość: "{val}" nie jest poprawnym URLem!',
    );
    protected $_vars     = array(
        '{val}' => '_inputVal',
    );

    protected function _validate($input)
    {
        if (!filter_var(trim($input), FILTER_VALIDATE_URL)) {
            $this->_error(self::ERROR_BAD_URL);
        }

        return $this;
    }

}
