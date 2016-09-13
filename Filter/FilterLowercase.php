<?php

namespace SAPF\Filter;

class FilterLowercase implements \SAPF\Filter\FilterInterface
{

    public function filter($input)
    {
        return mb_strtolower($input);
    }

}
