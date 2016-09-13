<?php

namespace SAPF\Filter;

class FilterPHP implements \SAPF\Filter\FilterInterface
{

    protected $_filter;
    protected $_options;

    public function __construct($filter = FILTER_DEFAULT, $options = array())
    {
        $this->_filter  = $filter;
        $this->_options = $options;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }

    public function getFilter()
    {
        return $this->_filter;
    }

    public function filter($input)
    {
        return filter_var($input, $this->_filter, $this->_options);
    }

}
