<?php

namespace SAPF\Decor;

class DecorFilter implements \SAPF\Decor\DecorInterface
{

    protected $_filter;

    public function setFilter($filter)
    {
        if (!($filter instanceof \SAPF\Filter\FilterInterface)) {
            throw new \SAPF\Decor\DecorException("setFilter() function require object implementing \SAPF\Filter\FilterInterface");
        }

        $this->_filter = $filter;
        return $this;
    }

    public function getFilter()
    {
        return $this->_filter;
    }

    public function decorate($subject)
    {
        if (!$this->_filter) {
            throw new \SAPF\Decor\DecorException("Filter is empty! Set filter with function setFilter(\$filter)");
        }

        return $this->_filter->filter($subject);
    }

}
