<?php

namespace SAPF\Filter;

class FilterBase64Encode implements \SAPF\Filter\FilterInterface
{

    public function filter($input)
    {
        return base64_encode($input);
    }

}
