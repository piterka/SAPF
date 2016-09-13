<?php

namespace SAPF\URL;

class URL
{

    public static function isUrlEqual($uri, $uri2)
    {
        $u  = new URL($uri);
        $u2 = new URL($uri2);
        return $u->getUrl() == $u2->getUrl();
    }

    public static function isPathEqual($uri, $uri2)
    {
        $u  = new URL($uri);
        $u2 = new URL($uri2);
        return $u->getPath() == $u2->getPath();
    }

    public static function fromRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        return new URL($request->getUri());
    }

    protected $_scheme   = "http";
    protected $_host     = "localhost";
    protected $_user     = false;
    protected $_pass     = false;
    protected $_path     = [];
    protected $_query    = [];
    protected $_fragment = false;

    public function __construct($url = FALSE)
    {
        $this->_path  = [];
        $this->_query = [];

        if ($url !== FALSE) {
            $this->setUrl($url);
        }
        else {
            $this->_host   = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : "localhost";
            $this->_scheme = $_SERVER['HTTPS'] ? "https" : "http";
        }
    }

    // user 
    public function getUser()
    {
        return $this->_user;
    }

    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }

    // host
    public function getHost()
    {
        return $this->_host;
    }

    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }

    // pass
    public function getPass()
    {
        return $this->_pass;
    }

    public function setPass($pass)
    {
        $this->_pass = $pass;
        return $this;
    }

    // scheme
    public function getScheme()
    {
        return $this->_scheme;
    }

    public function setScheme($scheme)
    {
        $this->_scheme = $scheme;
        return $this;
    }

    // fragment
    public function getFragment()
    {
        return $this->_fragment;
    }

    public function setFragment($fragment)
    {
        $this->_fragment = $fragment;
        return $this;
    }

    // path
    public function getPathArray()
    {
        return $this->_path;
    }

    public function getPath()
    {
        return "/" . implode("/", $this->_path);
    }

    public function setPath($path)
    {
        $this->_path  = [];
        $pathExploded = explode("/", $path);
        foreach ($pathExploded as $p) {
            if (strlen($p) > 0) {
                $this->_path[] = $p;
            }
        }
    }

    // params
    public function removeQueryParam($key)
    {
        $this->setQueryParam($key, null);
        return $this;
    }

    public function setQueryParam($key, $val)
    {
        if ($val === null) {
            unset($this->_query[$key]);
        }
        else {
            $this->_query[$key] = $val;
        }

        return $this;
    }

    public function getQueryParam($key)
    {
        return $this->_query[$key];
    }

    public function clearQueryParams()
    {
        $this->_query = [];
        return $this;
    }

    public function getQueryParams()
    {
        return $this->_query;
    }

    public function getQueryParamsMerged()
    {
        return http_build_query($this->_query);
    }

    public function setUrl($url)
    {
        $params          = parse_url($url);
        $this->_scheme   = $params['scheme'] ? $params['scheme'] : "http";
        $this->_host     = $params['host'] ? $params['host'] : "localhost";
        $this->_user     = $params['user'] ? $params['user'] : false;
        $this->_pass     = $params['pass'] ? $params['pass'] : false;
        $this->_fragment = $params['fragment'] ? $params['fragment'] : false;
        $this->_query    = [];
        if ($params['query']) {
            parse_str($params['query'], $this->_query);
        }
        $this->setPath($params['path']);
    }

    // build url from params
    public function getUrl()
    {
        $url     = $this->getScheme() . "://" . ($this->_user ? $this->_user . ($this->_pass ? ":" . $this->_pass : "" ) . "@" : "") . $this->getHost() . $this->getPath();
        $paramsQ = "?" . $this->getQueryParamsMerged();

        return $url . ( strlen($paramsQ) > 1 ? $paramsQ : "") . ($this->_fragment ? "#" . $this->_fragment : "");
    }

    public function __toString()
    {
        return $this->getUrl();
    }

}
