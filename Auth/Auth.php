<?php

namespace SAPF\Auth;

class Auth extends \SAPF\Database\Model
{

    protected $_idFields   = array('email', 'login');
    protected $_passFields = array('password');
    //
    protected $_user       = false;
    protected $_salt;
    protected $_session;

    public function __construct(\Symfony\Component\HttpFoundation\Session\Session $session, \SAPF\Database\SQLDatabase $db, $table = "users", $passSalt = "gj923f80yj09123y23453", $field_primarykey = "id")
    {
        $this->_salt    = $passSalt;
        $this->_session = $session;
        parent::__construct($db, $table, $field_primarykey);
    }

    public function authorize()
    {
        $this->_session->start();
        if ($this->_session->has("user_key")) {
            $this->_user = $this->param($this->_field_primarykey, $this->_session->get("user_key"))->fetch();
            $this->reset(); // reset post fetch
        }
    }

    public function password($rawPassword)
    {
        $p = sha1(sha1($rawPassword, true) . $this->_salt);
        return '*' . strtoupper($p);
    }

    public function is()
    {
        return is_array($this->_user);
    }

    public function logout()
    {
        $this->_user = false;
        $this->_session->remove("user_key");
        $this->_session->save();
    }

    public function getAllData()
    {
        return $this->_user;
    }

    public function getData($key)
    {
        return $this->_user[$key];
    }

    public function setData($key, $value)
    {
        $this->_user[$key] = $value;
    }

    public function update()
    {
        if (!is_array($this->_user) || !array_key_exists($this->_field_primarykey, $this->_user)) {
            return false;
        }
        return $this->save($this->_user);
    }

    public function create($data = [])
    {
        $hasId = false;
        foreach ($this->_idFields as $f) {
            if (!array_key_exists($f, $data)) {
                continue;
            }
            if (strlen($data[$f]) < 1) {
                continue;
            }
            $hasId = true;
        }
        if (!$hasId) {
            throw new \InvalidArgumentException("There is no any ID value specified (" . implode(", ", $this->_idFields) . ")");
        }

        $hasPass = false;
        foreach ($this->_passFields as $f) {
            if (!array_key_exists($f, $data)) {
                continue;
            }
            if (strlen($data[$f]) < 1) {
                continue;
            }
            $hasPass = true;
        }
        if (!$hasPass) {
            throw new \InvalidArgumentException("There is no any password value specified (" . implode(", ", $this->_passFields) . ")");
        }

        unset($data[$this->_field_primarykey]); // make sure we are creating new entity
        $this->save($data);
    }

    public function idExists($id)
    {
        $params = [];
        foreach ($this->_idFields as $f) {
            $params[$f] = $id;
        }
        $cnt = $this->params(['OR' => $params])->count() > 0;
        $this->reset();
        return $cnt;
    }

    public function checkCredentials($id, $pass)
    {
        $passHash = $this->password($pass);

        $params = [];

        // pass params
        $paramsA = [
            \SAPF\Database\SQLDatabase::OPERATOR_PREFIX . 'operator' => 'OR'
        ];
        foreach ($this->_passFields as $f) {
            $paramsA[$f] = $passHash;
        }
        $params[] = $paramsA;

        $paramsB = [
            \SAPF\Database\SQLDatabase::OPERATOR_PREFIX . 'operator' => 'OR'
        ];
        foreach ($this->_idFields as $f) {
            $paramsB[$f] = $id;
        }
        $params[] = $paramsB;

        $ret = $this->params($params)->count() > 0;
        $this->reset();
        return $ret;
    }

    public function login($id, $pass)
    {
        $passHash = $this->password($pass);

        $params = [];

        // pass params
        $paramsA = [
            \SAPF\Database\SQLDatabase::OPERATOR_PREFIX . 'operator' => 'OR'
        ];
        foreach ($this->_passFields as $f) {
            $paramsA[$f] = $passHash;
        }
        $params[] = $paramsA;

        $paramsB = [
            \SAPF\Database\SQLDatabase::OPERATOR_PREFIX . 'operator' => 'OR'
        ];
        foreach ($this->_idFields as $f) {
            $paramsB[$f] = $id;
        }
        $params[] = $paramsB;

        $this->_user = $this->params($params)->fetch();
        $this->reset();

        if ($this->_user) {
            $this->_session->set("user_key", $this->_user[$this->_field_primarykey]);
            $this->_session->save();
            return TRUE;
        }
        return FALSE;
    }

}
