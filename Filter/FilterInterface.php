<?php

namespace SAPF\Filter;

interface FilterInterface
{

    public function filter($input); // filtruje input, zwraca przefiltrowane. Może wywalać błąd: \SAPF\Filter\FilterException
}
