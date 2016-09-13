<?php

namespace SAPF\Filter;

class FilterChain implements \SAPF\Filter\FilterInterface
{

    protected $_filter;

    public function __construct($filters = array())
    {
        $this->setFilters($filters);
    }

    /**
     * Dodaje filtr do łańcucha z uwzględnieniem kolejności
     * @param \SAPF\Filter\FilterInterface $filter Filtr
     * @param int $pos Kolejność na liście 0 - filtr będzie dodany na początku, -1 - filtr będzie dodany na końcu
     * @return \SAPF\Filter\FilterChain
     * @throws \SAPF\Filter\FilterException
     */
    public function addFilter($filter, $pos = -1)
    {
        if (!($filter instanceof \SAPF\Filter\FilterInterface)) {
            throw new \SAPF\Filter\FilterException("\$filter must implement \SAPF\Filter\FilterInterface");
        }

        $new = array();
        for ($i = 0; $i < count($this->_filter); $i ++) {
            if ($i == $pos) {
                $new[] = $filter;
            }
            $new[] = $this->_filter[$i];
        }

        if ($pos == -1) {
            $new[] = $filter;
        }

        $this->_filter = $new;

        return $this;
    }

    /**
     * Dodaje wszystkie filtry z tablicy do łańcucha
     * @param array $filters
     * @param int $pos
     * @return \SAPF\Filter\FilterChain
     * @throws \SAPF\Filter\FilterException
     */
    public function addFilters($filters, $pos = -1)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter, $pos);
        }
        return $this;
    }

    /**
     * Zwraca filtry z łańcucha
     * @return arrary
     */
    public function getFilters()
    {
        return $this->_filter;
    }

    /**
     * Ustawia tablicę filtrów
     * @param array $filters
     * @return \SAPF\Filter\FilterChain
     */
    public function setFilters($filters)
    {
        $this->clearFilters();
        $this->addFilters($filters);
        return $this;
    }

    /**
     * Czyści łańcuch filtrów
     * @return \SAPF\Filter\FilterChain
     */
    public function clearFilters()
    {
        $this->_filter = array();
        return $this;
    }

    public function filter($input)
    {
        foreach ($this->_filter as $filter) {
            $input = $filter->filter($input);
        }
        return $input;
    }

}
