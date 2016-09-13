<?php

namespace SAPF\Validator;

class ValidatorChain implements \SAPF\Validator\ValidatorInterface
{

    protected $_validator;

    public function __construct($validators = array())
    {
        $this->setValidators($validators);
    }

    /**
     * Dodaje walidator do łańcucha z uwzględnieniem kolejności
     * @param \SAPF\Validator\ValidatorInterface $validator Walidator
     * @param int $pos Kolejność na liście 0 - walidator będzie dodany na początku, -1 - walidator będzie dodany na końcu
     * @return \SAPF\Validator\ValidatorChain
     * @throws \SAPF\Validator\ValidatorException
     */
    public function addValidator($validator, $pos = -1)
    {
        if (!($validator instanceof \SAPF\Validator\ValidatorInterface)) {
            throw new \SAPF\Validator\ValidatorException("\$validator must implement \SAPF\Validator\ValidatorInterface");
        }

        $new = array();
        for ($i = 0; $i < count($this->_validator); $i ++) {
            if ($i == $pos) {
                $new[] = $validator;
            }
            $new[] = $this->_validator[$i];
        }

        if ($pos == -1) {
            $new[] = $validator;
        }

        $this->_validator = $new;

        return $this;
    }

    /**
     * Dodaje wszystkie walidatory z tablicy do łańcucha
     * @param array $validators
     * @param int $pos
     * @return \SAPF\Validator\ValidatorChain
     * @throws \SAPF\Validator\ValidatorException
     */
    public function addValidators($validators, $pos = -1)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator, $pos);
        }
        return $this;
    }

    /**
     * Zwraca walidatory z łańcucha
     * @return arrary
     */
    public function getValidators()
    {
        return $this->_validator;
    }

    /**
     * Ustawia tablicę walidatorów
     * @param array $validators
     * @return \SAPF\Validator\ValidatorChain
     */
    public function setValidators($validators)
    {
        $this->clearValidators();
        $this->addValidators($validators);
        return $this;
    }

    /**
     * Czyści łańcuch walidatorów
     * @return \SAPF\Validator\ValidatorChain
     */
    public function clearValidators()
    {
        $this->_validator = array();
        return $this;
    }

    public function validate($input)
    {
        foreach ($this->_validator as $validator) {
            $validator->validate($input);
        }
        return $this;
    }

    public function isValid()
    {
        foreach ($this->_validator as $validator) {
            if (!$validator->isValid()) {
                return false;
            }
        }
        return true;
    }

    public function getErrors()
    {
        $errors = array();
        foreach ($this->_validator as $validator) {
            if (!$validator->isValid()) {
                $errors = array_merge($errors, $validator->getErrors());
            }
        }
        return $errors;
    }

}
