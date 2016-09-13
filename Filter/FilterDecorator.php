<?php

namespace SAPF\Filter;

class FilterDecorator implements \SAPF\Filter\FilterInterface
{

    protected $_decorator;

    public function setDecorator(\SAPF\Decor\DecorInterface $decorator)
    {
        $this->_decorator = $decorator;
        return $this;
    }

    public function getDecorator()
    {
        return $this->_decorator;
    }

    public function filter($input)
    {
        if (!$this->_decorator) {
            throw new \SAPF\Filter\FilterException("Decorator is empty! Set decorator with function setDecorator(\$decorator)");
        }
        return $input;
    }

}
