<?php

namespace SAPF\Validator;

class ValidatorRegex extends \SAPF\Validator\ValidatorAbstract
{

    const ERROR_NOT_MATCH = "ERROR_NOT_MATCH";

    protected $_errorMsg = array(
        'ERROR_NOT_MATCH' => 'Wartość: "{val}" nie pasuje do wzorca: {pattern}',
    );
    protected $_vars     = array(
        '{val}'     => '_inputVal',
        '{pattern}' => '_pattern',
    );
    
    protected $_pattern;

    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;
        return $this;
    }

    public function getPattern()
    {
        return $this->_pattern;
    }

    protected function _validate($input)
    {
        if (!$this->_pattern) {
            throw new \SAPF\Validator\ValidatorException("Regex pattern is empty! Set pattern with function setPattern(\$pattern)");
        }

        $ret = preg_match($this->_pattern, $input);

        if ($ret === FALSE) {
            throw new \SAPF\Validator\ValidatorException("Regex pattern: \"" . $this->_pattern . "\" is invalid!");
        }

        if (!$ret) {
            $this->_error(self::ERROR_NOT_MATCH);
        }

        return $this;
    }

}
