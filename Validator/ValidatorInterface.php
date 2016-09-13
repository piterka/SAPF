<?php

namespace SAPF\Validator;

interface ValidatorInterface
{

    public function validate($input); // zwraca objekt typu ValidatorInterface

    public function isValid(); // zwraca czy jest zwalidowane (true|false)

    public function getErrors(); // zwraca listę error codów jakie zwrócił walidator
}
