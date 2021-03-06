<?php

namespace SAPF\Auth;

class DigestAuth
{

    protected $_users;
    protected $_realm;

    public function __construct($users, $realm = "Restricted area")
    {
        $this->_users = $users;
        $this->_realm = $realm;
    }

    public function authorize()
    {
        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            $this->_show_base_auth($this->_realm);
        }

        // analyze the PHP_AUTH_DIGEST variable
        if (!($data = _http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($this->_users[$data['username']])) {
            $this->_show_base_auth($this->_realm);
        }

        // generate the valid response
        $A1             = md5($data['username'] . ':' . $this->_realm . ':' . $this->_users[$data['username']]);
        $A2             = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
        $valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);

        if ($data['response'] != $valid_response) {
            $this->_show_base_auth($this->_realm);
        }
    }

    // function to parse the http auth header
    protected function _http_digest_parse($txt)
    {
        // protect against missing data
        $needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
        $data         = array();
        $keys         = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

    protected function _show_base_auth($realm)
    {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="' . $realm .
                '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($realm) . '"');

        die('401 Authorization Required');
    }

}
