<?php

namespace SAPF\Validator;

abstract class ValidatorAbstract implements \SAPF\Validator\ValidatorInterface, \SAPF\Translate\TranslateAwareInterface
{

    use \SAPF\Translate\TranslateAwareTrait;

    protected $_error    = array();
    protected $_errorMsg = array();
    protected $_vars     = array(
        '{val}' => '_inputVal'
    );
    protected $_inputVal;

    /**
     * Validate input
     * @param mixed $input
     * @return \SAPF\Validator\ValidatorAbstract
     */
    public function validate($input)
    {
        $this->_inputVal = $input;
        $this->_error    = array();
        $this->_validate($input);
        return $this;
    }

    abstract protected function _validate($input);

    public function _error($error)
    {
        $this->_error[] = $error;
    }

    public function isValid()
    {
        return count($this->getErrors()) == 0;
    }

    public function setVar($replacement = "{val}", $fieldName = "_inputVal")
    {
        $this->_vars[$replacement] = $fieldName;
        return $this;
    }

    public function setMsg($error, $message)
    {
        $this->_errorMsg[$error] = $message;
        return $this;
    }

    public function getErrors()
    {
        if (!$this->_error) {
            return array();
        }
        $errors = [];
        foreach ($this->_error as $error) {
            if (isset($this->_errorMsg[$error])) {
                $errors[$error] = $this->_errorMsg[$error];
                if ($this->getTranslate()) {
                    $replacements = [];
                    foreach ($this->_vars as $k => $field) {
                        if (isset($this->{$field})) {
                            $replacements[$k] = (string) $this->{$field};
                        }
                    }
                    $errors[$error] = $this->getTranslate()->translate($errors[$error], $replacements);
                }
                else {
                    foreach ($this->_vars as $k => $field) {
                        if (isset($this->{$field})) {
                            $errors[$error] = str_replace($k, (string) $this->{$field}, $errors[$error]);
                        }
                    }
                }
            }
            else {
                $errors[$error] = $error;
            }
        }
        return $errors;
    }

}
