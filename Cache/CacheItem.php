<?php

namespace SAPF\Cache;

class CacheItem implements \Psr\Cache\CacheItemInterface
{

    protected $_callable;
    protected $_key;
    protected $_value;
    protected $_expirationDate = null;
    protected $_hasValue       = false;

    /**
     * Construct CacheItem
     * @param type $key
     * @param callable $callable
     * @param type $value
     */
    public function __construct($key, $callable = null, $value = null)
    {
        $this->_key = $key;
        if ($callable === true) {
            $this->_hasValue = true;
            $this->_value    = $value;
        }
        elseif ($callable !== false) {
            // This must be a callable or null
            $this->_callable = $callable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        $this->_value    = $value;
        $this->_hasValue = true;
        $this->_callable = null;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!$this->isHit()) {
            return;
        }
        return $this->_value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        $this->_initialize();
        if (!$this->_hasValue) {
            return false;
        }
        if ($this->_expirationDate !== null) {
            return $this->_expirationDate > new \DateTime();
        }
        return true;
    }

    /**
     * Return expiration date
     * @return \DateTimeInterface expire date
     */
    public function getExpirationDate()
    {
        return $this->_expirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof \DateTimeInterface) {
            $this->_expirationDate = clone $expiration;
        }
        else {
            $this->_expirationDate = $expiration;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->_expirationDate = null;
        }
        if ($time instanceof \DateInterval) {
            $this->_expirationDate = new \DateTime();
            $this->_expirationDate->add($time);
        }
        if (is_int($time)) {
            $this->_expirationDate = new \DateTime(sprintf('+%sseconds', $time));
        }
        return $this;
    }

    protected function _initialize()
    {
        if ($this->_callable !== null) {
            $f = $this->_callable;
            list($this->_hasValue, $this->_value, $this->_expirationDate) = $f();
            $this->_callable = null;
        }
    }

}
