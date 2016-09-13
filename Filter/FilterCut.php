<?php

namespace SAPF\Filter;

class FilterCut implements \SAPF\Filter\FilterInterface
{

    protected $_encoding;
    protected $_endChar;
    protected $_maxLen;

    public function __construct($maxLen = 100, $endChar = '…', $encoding = 'UTF-8')
    {
        $this->_encoding = $encoding;
        $this->_endChar  = $endChar;
        $this->_maxLen   = $maxLen;
    }

    public function setMaxLen($maxLen)
    {
        $this->_maxLen = $maxLen;
        return $this;
    }

    public function getMaxLen()
    {
        return $this->_maxLen;
    }

    public function setEndChar($endChar)
    {
        $this->_endChar = $endChar;
        return $this;
    }

    public function getEndChar()
    {
        return $this->_endChar;
    }

    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
        return $this;
    }

    public function getEncoding()
    {
        return $this->_encoding;
    }

    public function filter($input)
    {
        if (isset($input[$this->_maxLen - 1])) {
            return mb_substr($input, 0, $this->_maxLen, $this->_encoding) . $this->_endChar;
        }

        return $input;
    }

}
