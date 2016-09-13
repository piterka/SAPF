<?php

namespace SAPF\DataBucket;

class DataBucket implements \IteratorAggregate, \Countable
{

    protected $_data;

    public function __construct(array $data = array())
    {
        $this->_data = $data;
    }

    /**
     * Returns all data
     * @return array
     */
    public function all()
    {
        return $this->_data;
    }

    /**
     * Returns all keys
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_data);
    }

    /**
     * Replace data with specified
     * @param array $data
     * @return \SAPF\DataBucket\DataBucket
     */
    public function replace(array $data = array())
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Adds data to bucket
     * @param array $data
     * @return \SAPF\DataBucket\DataBucket
     */
    public function add(array $data = array())
    {
        $this->_data = array_replace($this->_data, $data);
        return $this;
    }

    /**
     * Gets data by key
     * @param type $key Key
     * @param type $default Default value
     * @param \SAPF\Filter\FilterInterface $filter Filter to apply on data
     * @return mixed
     */
    public function get($key, $default, \SAPF\Filter\FilterInterface $filter = null)
    {
        if (isset($this->_data[$key])) {
            if ($filter != null) {
                return $filter->filter($this->_data[$key]);
            }
            return $this->_data[$key];
        }
        return $default;
    }

    /**
     * Gets alpha data
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAlpha($key, $default = '')
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

    /**
     * Gets alpha data
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAlnum($key, $default = '')
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    /**
     * Gets digit data
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getDigits($key, $default = '')
    {
        return str_replace(array('-', '+'), '', filter_var($this->get($key, $default), FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Gets integer data
     * @param string $key
     * @param int $default
     * @return int
     */
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Gets boolean data
     * @param string $key
     * @param boolean $default
     * @return boolean
     */
    public function getBoolean($key, $default = false)
    {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Sets data for specified key
     * @param string $key
     * @param mixed $value
     * @return \SAPF\DataBucket\DataBucket
     */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * Returns TRUE if bucket has value with specified key
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * Removes data with specified key
     * @param string $key
     * @return \SAPF\DataBucket\DataBucket
     */
    public function remove($key)
    {
        unset($this->_data[$key]);
        return $this;
    }

    /**
     * Gets data count
     * @param string $mode count() mode
     * @return int
     */
    public function count($mode = 'COUNT_NORMAL')
    {
        return count($this->_data, $mode);
    }

    /**
     * Gets ArrayIterator
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }

}
