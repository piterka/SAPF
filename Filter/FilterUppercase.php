<?php

namespace SAPF\Filter;

class FilterUppercase implements \SAPF\Filter\FilterInterface
{

    public function filter($input)
    {
        return mb_strtoupper($input);
    }

}
