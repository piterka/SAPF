<?php

namespace SAPF\Config;

class ConfigFile
{

    protected $_loaded;
    protected $_path;

    public function __construct($path)
    {
        $this->_path   = $path;
        $this->_loaded = [];
    }

    public function __call($name, $params)
    {
        $config               = in_array($name, $this->_loaded) ? $this->_loaded[$name] : $this->_loaded[$name] = include $this->_path . $name . '.php';
        if (count($params) > 0) {
            return $this->_getVal($config, $params);
        }
        return $config;
    }

    protected function _getVal($config, $params)
    {
        $val = $config;
        foreach ($params as $p) {
            $val = $val[$p];
        }
        return $val;
    }

}
