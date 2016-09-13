<?php

namespace SAPF\Filter;

class FilterTrim implements \SAPF\Filter\FilterInterface
{

    public function filter($input)
    {
        return trim($input);
    }

}
