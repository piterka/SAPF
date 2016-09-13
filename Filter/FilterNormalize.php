<?php

namespace SAPF\Filter;

class FilterNormalize implements \SAPF\Filter\FilterInterface
{

    protected $_space = '-';
    protected $_allow = '.()';

    public function setSpace($space)
    {
        $this->_space = $space;
        return $this;
    }

    public function getSpace()
    {
        return $this->_space;
    }

    public function setAllow($allowRegex)
    {
        $this->_allow = $allowRegex;
        return $this;
    }

    public function getAllow()
    {
        return $this->_allow;
    }

    public function filter($input)
    {
        return preg_replace( // wiele znakow spacji obok siebie zostaje zastąpionych jednym znakiem spacji
            '#[' . preg_quote($this->_space, '#') . '\s]+#',
            $this->_space,
            preg_replace( // usuwanie wszystkich niedozwolonych znakow
                '#[^' . preg_quote($this->_space, '#') . 'a-zA-Z0-9' . preg_quote($this->_allow, '#') . '\s]+#',
                '',
                $input
            )
        );
    }
}
