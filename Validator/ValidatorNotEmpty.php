<?php

namespace SAPF\Validator;

class ValidatorNotEmpty extends \SAPF\Validator\ValidatorAbstract
{

    const ERROR_EMPTY_INPUT = "ERROR_EMPTY_INPUT";

    protected $_errorMsg = array(
        'ERROR_EMPTY_INPUT' => 'Pole nie może być puste',
    );
    protected $_vars     = array(
        '{val}' => '_inputVal'
    );

    protected function _validate($input)
    {
        $len = 0;
        if (is_array($input)) {
            $len = count($input);
        }
        else {
            $len = strlen((string) $input);
        }

        if ($len == 0) {
            $this->_error(self::ERROR_EMPTY_INPUT);
        }

        return $this;
    }

}
