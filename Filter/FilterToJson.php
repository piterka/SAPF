<?php

namespace SAPF\Filter;

class FilterToJson implements \SAPF\Filter\FilterInterface
{

    protected $_options;

    public function __construct($options = JSON_PRETTY_PRINT)
    {
        $this->_options = $options;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    public function filter($input)
    {
        return json_encode($input, $this->_options);
    }

}
