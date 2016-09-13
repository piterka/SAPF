<?php

namespace SAPF\Filter;

class FilterBase64Decode implements \SAPF\Filter\FilterInterface
{

    public function filter($input)
    {
        return base64_decode($input);
    }

}
