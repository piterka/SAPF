<?php

namespace SAPF\Database;

class SQLDatabase extends \PDO
{

    const OPERATOR_PREFIX         = ":";
    const DONT_QUOTE_VALUE_PREFIX = "!";

    // Debug and logging
    protected $_debug_mode = false;
    protected $_log        = [];

    /**
     * Creates MySQL connection
     * @param type $host MySQL server host
     * @param type $user MySQL username
     * @param type $pass MySQL password
     * @param type $database MySQL database
     * @param type $port MySQL server port
     * @return \SAPF\Database\SQLDatabase
     */
    public static function createMySQLConnection($host, $user, $pass, $database, $port = 3306)
    {
        return self::fromOptions([
                    'server'   => $host,
                    'username' => $user,
                    'password' => $pass,
                    'dbname'   => $database,
                    'port'     => $port,
        ]);
    }

    /**
     * Creates new SQLDatabase object from $options
     * @param array $options
     * @return \SAPF\Database\SQLDatabase
     */
    public static function fromOptions(array $options)
    {
        $commands = array();

        $options = array_change_key_case($options, CASE_LOWER);

        $type = strtolower($options['database_type']);

        switch ($type) {
            case 'mariadb':
                $type = 'mysql';

            case 'mysql':
                if ($options['socket']) {
                    $dsn = $type . ':unix_socket=' . $options['socket'] . ';dbname=' . $options['database_name'];
                }
                else {
                    $dsn = $type . ':host=' . $options['server'] . ($options['port'] ? ';port=' . $options['port'] : '') . ';dbname=' . $options['database_name'];
                }

                // Make MySQL using standard quoted identifier
                $commands[] = 'SET SQL_MODE=ANSI_QUOTES';
                break;

            case 'pgsql':
                $dsn = $type . ':host=' . $options['server'] . ($options['port'] ? ';port=' . $options['port'] : '') . ';dbname=' . $options['database_name'];
                break;

            case 'sybase':
                $dsn = 'dblib:host=' . $options['server'] . ($options['port'] ? ':' . $options['port'] : '') . ';dbname=' . $options['database_name'];
                break;

            case 'oracle':
                $dbname = $options['server'] ?
                        '//' . $options['server'] . ($options['port'] ? ':' . $options['port'] : ':1521') . '/' . $options['database_name'] :
                        $options['database_name'];

                $dsn = 'oci:dbname=' . $dbname . ($options['charset'] ? ';charset=' . $options['charset'] : '');
                break;

            case 'mssql':
                $dsn = strstr(PHP_OS, 'WIN') ?
                        'sqlsrv:server=' . $options['server'] . ($options['port'] ? ',' . $options['port'] : '') . ';database=' . $options['database_name'] :
                        'dblib:host=' . $options['server'] . ($options['port'] ? ':' . $options['port'] : '') . ';dbname=' . $options['database_name'];

                // Keep MSSQL QUOTED_IDENTIFIER is ON for standard quoting
                $commands[] = 'SET QUOTED_IDENTIFIER ON';
                break;

            case 'sqlite':
                $dsn                 = $type . ':' . $options['database_file'];
                $options['username'] = null;
                $options['password'] = null;
                break;
        }

        if (in_array($type, ['mariadb', 'mysql', 'pgsql', 'sybase', 'mssql']) && $options['charset']) {
            $commands[] = "SET NAMES '" . $options['charset'] . "'";
        }

        $sqlDatabase              = new SQLDatabase($dsn, $options['username'], $options['password'], $options['pdoOption']);
        $sqlDatabase->_debug_mode = $options['debug_mode'] ? : $sqlDatabase->_debug_mode;

        foreach ($commands as $value) {
            $sqlDatabase->exec($value);
        }

        return $sqlDatabase;
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     * @param string $query
     * @return \PDOStatement returns a PDOStatement object, or FALSE on failure.
     */
    public function query($query)
    {
        // TODO: psr Logger
        $this->_log[] = $query;

        if ($this->_debug_mode) {
            return false;
        }

        $ret = parent::query($query);
        return $ret;
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     * Data inside the query should be properly escaped.
     * @param string $query
     * @return int PDO::exec returns the number of rows that were modified
     */
    public function exec($query)
    {
        // TODO: psr Logger
        $this->_log[] = $query;

        if ($this->_debug_mode) {
            return false;
        }

        $ret = parent::exec($query);
        return $ret;
    }

    /**
     * Get query log
     * @return array Query logs
     */
    public function getQueryLog()
    {
        return $this->_log;
    }

    /**
     * Enables debug mode
     * If debug mode is enabled queries are not executed
     * @return \SAPF\Database\SQLDatabase
     */
    public function enableDebug()
    {
        $this->_debug_mode = true;
        return $this;
    }

    /**
     * Disables debug mode
     * If debug mode is enabled queries are not executed
     * @return \SAPF\Database\SQLDatabase
     */
    public function disableDebug()
    {
        $this->_debug_mode = false;
        return $this;
    }

    /**
     * Show debug mode state
     * @return boolean Is debug mode enabled?
     */
    public function isDebug()
    {
        return $this->_debug_mode;
    }

    /**
     * Enables throw sql query exceptions
     * If enabled SQLDatabase will throw all query exceptions
     * @return \SAPF\Database\SQLDatabase
     */
    public function enableQueryExceptions()
    {
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    /**
     * Disables throw sql query exceptions
     * If enabled SQLDatabase will throw all query exceptions
     * @return \SAPF\Database\SQLDatabase
     */
    public function disableQueryExceptions()
    {
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        return $this;
    }

    /**
     * Should throw sql query exceptions?
     * @return boolean Is debug mode enabled?
     */
    public function isQueryExceptions()
    {
        return $this->getAttribute(\PDO::ATTR_ERRMODE) == \PDO::ERRMODE_EXCEPTION;
    }

    /**
     * Return connection info
     * @return array Info
     */
    public function getInfo()
    {
        $output = array(
            'server'     => 'SERVER_INFO',
            'driver'     => 'DRIVER_NAME',
            'client'     => 'CLIENT_VERSION',
            'version'    => 'SERVER_VERSION',
            'connection' => 'CONNECTION_STATUS'
        );

        foreach ($output as $key => $value) {
            $output[$key] = $this->getAttribute(constant('\PDO::ATTR_' . $value));
        }

        return $output;
    }

    /**
     * Perform INSERT ON DUPLICATE KEY UPDATE query
     * @param string $table Table name
     * @param array $data Data array
     * @return FALSE|int FALSE - cant execute query, -1 - row updated, int >= 0 - inserted ID
     */
    public function insertOrUpdate($table, $data)
    {
        $values  = array();
        $columns = array();
        $sets    = array();

        foreach ($data as $key => $value) {
            $dontQuote = false;
            if (strpos($key, self::DONT_QUOTE_VALUE_PREFIX) === 0) {
                $key       = substr($key, 1);
                $dontQuote = true;
            }
            $columns[] = $c         = $this->quoteColumn($key);

            if ($value === NULL) {
                $values[]  = 'NULL';
                $sets[]    = $c . ' = NULL';
                $dontQuote = true;
            }
            else if (is_string($value) || is_numeric($value)) {
                $v        = $this->_quoteVal($value, $dontQuote);
                $values[] = $v;
                $sets[]   = $c . ' = ' . $v;
            }
            else {
                $values[] = $v        = $this->_quoteVal(json_encode($value));
                $sets[]   = $c . ' = ' . $v;
            }
        }

        $modified = $this->exec('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')'
                . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $sets));

        if ($modified === false) {
            return false;
        }

        return $modified === 1 ? $this->lastInsertId() : -1;
    }

    /**
     * Perform insert query
     * @param string $table Table name
     * @param type $datas Data array or array of data arrays
     * @return array|int - inserted ids|id
     */
    public function insert($table, $datas)
    {
        $lastId = array();

        // Check indexed or associative array
        if (!isset($datas[0])) {
            $datas = array($datas);
        }

        foreach ($datas as $data) {
            $values  = array();
            $columns = array();

            foreach ($data as $key => $value) {
                $dontQuote = false;
                if (strpos($key, self::DONT_QUOTE_VALUE_PREFIX) === 0) {
                    $key       = substr($key, 1);
                    $dontQuote = true;
                }
                $columns[] = $this->quoteColumn($key);

                if ($value === NULL) {
                    $dontQuote = true;
                    $values[]  = 'NULL';
                }
                else if (is_string($value) || is_numeric($value)) {
                    $values[] = $this->_quoteVal($value, $dontQuote);
                }
                else {
                    $values[] = $this->_quoteVal(json_encode($value));
                }
            }

            $this->exec('INSERT INTO ' . $this->quoteColumn($table) . ' (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')');

            $lastId[] = $this->lastInsertId();
        }

        return count($lastId) > 1 ? $lastId : $lastId[0];
    }

    /**
     * Perform update query
     * @param string $table Table name
     * @param array $data Data array
     * @param array $params Params array
     * @param type $joins Joins array
     * @return int returns the number of rows that were modified
     */
    public function update($table, $data, $params = [], $joins = [])
    {
        $dataQ = [];
        foreach ($data as $k => $v) {
            $dontQuote = false;
            if (strpos($k, self::DONT_QUOTE_VALUE_PREFIX) === 0) {
                $k         = substr($k, 1);
                $dontQuote = true;
            }
            if ($v === NULL) {
                $dontQuote = true;
                $v         = "NULL";
            }
            if ($v === FALSE) {
                $v = 0;
            }
            if ($v === TRUE) {
                $v = 1;
            }
            $dataQ[] = $this->quoteColumn($k) . " = " . $this->_quoteVal($v, $dontQuote);
        }

        return $this->exec('UPDATE ' . $this->quoteColumn($table) . $this->resolveJoins($joins) . " SET " . implode(", ", $dataQ) . $this->resolveCondition($params));
    }

    /**
     * Perform delete query
     * @param string $table Table name
     * @param array $params Params
     * @param array $joins Joins 
     * @return int returns the number of rows that were deleted
     */
    public function delete($table, $params, $joins = [])
    {
        $wh = $this->resolveJoins($joins); // " LEFT JOIN ..."
        $wh .= $this->resolveCondition($params); // " WHERE ..."
        return $this->exec('DELETE FROM ' . $this->quoteColumn($table) . $wh);
    }

    /**
     * Perform SELECT EXISTS query
     * @param string $table Table name
     * @param array $params Params array
     * @param type $joins Joins array
     * @return boolean return true if record exists
     */
    public function has($table, $params = [], $joins = [])
    {
        $query = $this->query('SELECT EXISTS(' . $this->selectContext($table, "*", $joins, $params) . ')');
        return $query ? $query->fetchColumn() === '1' : false;
    }

    /**
     * Perform SELECT COUNT() query
     * @param string $table Table name
     * @param string|array $field Field name or array of field names
     * @param array $params Params
     * @param type $joins Joins array
     * @return int returns selected count
     */
    public function count($table, $field = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $field, $joins, $params, "SELECT", 'COUNT'));
        return $query ? 0 + $query->fetchColumn() : false;
    }

    /**
     * Perform SELECT AVG() query
     * @param string $table Table name
     * @param string|array $field Field name or array of field names
     * @param array $params Params
     * @param type $joins Joins array
     * @return int returns selected avg
     */
    public function avg($table, $field = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $field, $joins, $params, "SELECT", 'AVG'));
        return $query ? 0 + $query->fetchColumn() : false;
    }

    /**
     * Perform SELECT SUM() query
     * @param string $table Table name
     * @param string|array $field Field name or array of field names
     * @param array $params Params
     * @param type $joins Joins array
     * @return int returns selected sum
     */
    public function sum($table, $field = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $field, $joins, $params, "SELECT", 'SUM'));
        return $query ? 0 + $query->fetchColumn() : false;
    }

    /**
     * Perform SELECT MAX() query
     * @param string $table Table name
     * @param string|array $field Field name or array of field names
     * @param array $params Params
     * @param type $joins Joins array
     * @return int returns selected max
     */
    public function max($table, $field = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $field, $joins, $params, "SELECT", 'MAX'));
        if ($query) {
            $max = $query->fetchColumn();
            return is_numeric($max) ? $max + 0 : $max;
        }
        else {
            return false;
        }
    }

    /**
     * Perform SELECT MIN() query
     * @param string $table Table name
     * @param string|array $field Field name or array of field names
     * @param array $params Params
     * @param type $joins Joins array
     * @return int returns selected min
     */
    public function min($table, $field = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $field, $joins, $params, "SELECT", 'MIN'));
        if ($query) {
            $max = $query->fetchColumn();
            return is_numeric($max) ? $max + 0 : $max;
        }
        else {
            return false;
        }
    }

    /**
     * Make select query
     * @param string $table Table name
     * @param string|array $fields Field name or array of field names
     * @param array $params Params
     * @param type $joins Joins array
     * @return \PDOStatement returns a PDOStatement object, or FALSE on failure.
     */
    public function select($table, $fields = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $fields, $joins, $params, "SELECT"));
        if (!$query) {
            return false;
        }

        return $query;
    }

    /**
     * Perform SELECT query and return all rows
     * @param string $table Table name
     * @param string|array $fields Field name or array of field names, if string - function returns array of field values
     * @param array $params Params
     * @param type $joins Joins array
     * @return array Array of selected data
     */
    public function fetchAll($table, $fields = "*", $params = [], $joins = [])
    {
        $query = $this->query($this->selectContext($table, $fields, $joins, $params, "SELECT"));
        if (!$query) {
            return false;
        }

        return $query->fetchAll((is_string($fields) && $fields != '*') ? \PDO::FETCH_COLUMN : \PDO::FETCH_ASSOC);
    }

    /**
     * Perform SELECT query and return first row
     * @param string $table Table name
     * @param string|array $fields Field name or array of field names, if string - function returns field value
     * @param array $params Params
     * @param type $joins Joins array
     * @return int|string Row data
     */
    public function fetch($table, $fields = "*", $params = [], $joins = [])
    {
        if (is_array($params[':limit'])) {
            $params[':limit'][1] = 1;
        }
        else {
            $params[':limit'] = 1;
        }

        $data = $this->fetchAll($table, $fields, $params, $joins);
        if (count($data) > 0) {
            return $data[0];
        }

        return false;
    }

    /**
     * Resolve params to WHERE string
     * @param array $params WHERE condition array
     * @return string WHERE string
     */
    public function resolveCondition($params)
    {
        $where_clause = $this->resolveConditionData($params);
        if ($where_clause) {
            $where_clause = " WHERE " . $where_clause;
        }

        // special params
        $paramsFunctional = array_change_key_case($params, CASE_UPPER);
        if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'MATCH'])) {
            $MATCH = $paramsFunctional[self::OPERATOR_PREFIX . 'MATCH'];

            if (is_array($MATCH) && isset($MATCH['columns'], $MATCH['keyword'])) {
                $where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . ' MATCH (' . $this->quoteColumn($MATCH['columns']) . ') AGAINST (' . $this->_quoteVal_one($MATCH['keyword']) . ($MATCH['mode'] ? " IN " . strtoupper($MATCH['mode']) . " MODE" : "") . ')';
            }
        }

        if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'GROUP'])) {
            $where_clause .= ' GROUP BY ' . $this->quoteColumn($paramsFunctional[self::OPERATOR_PREFIX . 'GROUP']);

            if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'HAVING'])) {
                $where_clause .= ' HAVING ' . $this->resolveConditionData($paramsFunctional[self::OPERATOR_PREFIX . 'HAVING']);
            }
        }

        if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'ORDER'])) {
            $where_clause .= ' ORDER BY ' . $this->quoteColumn($paramsFunctional[self::OPERATOR_PREFIX . 'ORDER']);
        }

        if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'LIMIT'])) {
            $LIMIT = $paramsFunctional[self::OPERATOR_PREFIX . 'LIMIT'];
            if (is_numeric($LIMIT)) {
                $where_clause .= ' LIMIT ' . $LIMIT;
                if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'OFFSET']) && is_numeric($paramsFunctional[self::OPERATOR_PREFIX . 'OFFSET'])) {
                    $where_clause .= ' OFFSET ' . $paramsFunctional[self::OPERATOR_PREFIX . 'OFFSET'];
                }
            }
            else if (is_array($LIMIT) && is_numeric($LIMIT[0]) && is_numeric($LIMIT[1])) {
                if (isset($paramsFunctional[self::OPERATOR_PREFIX . 'OFFSET']) && is_numeric($paramsFunctional[self::OPERATOR_PREFIX . 'OFFSET'])) {
                    $where_clause .= ' LIMIT ' . $LIMIT[1] . ' OFFSET ' . $paramsFunctional[self::OPERATOR_PREFIX . 'OFFSET'];
                }
                else {
                    $where_clause .= ' LIMIT ' . $LIMIT[1] . ' OFFSET ' . $LIMIT[0];
                }
            }
        }

        return $where_clause;
    }

    /**
     * Resolve data array to WHERE condition string
     * @param array $params WHERE data condition array
     * @return string WHERE string
     */
    public function resolveConditionData($params)
    {
        $dataWhere = "";

        $lowerKeyd = array_change_key_case($params, CASE_LOWER);
        $glue      = strtoupper(isset($lowerKeyd[self::OPERATOR_PREFIX . 'operator']) && strtolower($lowerKeyd[self::OPERATOR_PREFIX . 'operator']) == "or" ? "OR" : "AND");
        unset($lowerKeyd);

        foreach ($params as $k => $v) {
            if (strpos($k, self::OPERATOR_PREFIX) === 0) {
                continue;
            }
            if (is_numeric($k)) {
                if (is_array($v)) {
                    $dataWhere .= ($dataWhere ? " {$glue} (" : "(") . $this->resolveConditionData($v) . ")";
                }
                else {  // raw
                    $dataWhere .= ($dataWhere ? " {$glue} " : "") . $v;
                }
            }
            else {
                $expl     = explode(" ", $k, 2);
                $operator = count($expl) > 1 ? $expl[1] : "=";
                if ($operator == "=" && (is_array($v))) {
                    $operator = "IN";
                }
                if ($operator == "!=" && (is_array($v))) {
                    $operator = "NOT IN";
                }
                if ($operator == "=" && ($v === NULL)) {
                    $operator = "IS";
                }
                if ($operator == "!=" && ($v === NULL)) {
                    $operator = "IS NOT";
                }
                $dontQuote = false;
                if ($v === NULL) {
                    $v         = "NULL";
                    $dontQuote = true;
                }
                if ($v === TRUE) {
                    $v = 1;
                }
                if ($v === FALSE) {
                    $v = 0;
                }
                $column = $expl[0];
                if (strpos($column, self::DONT_QUOTE_VALUE_PREFIX) === 0) {
                    $column    = substr($column, 1);
                    $dontQuote = true;
                }
                $dataWhere .= ($dataWhere ? " {$glue} " : "") . $this->quoteColumn($column) . " {$operator} " . $this->_quoteVal($v, $dontQuote);
            }
        }

        return $dataWhere;
    }

    /**
     * Resolve joins to JOIN string
     * @param array $joins
     * @return string JOIN string
     */
    public function resolveJoins($joins)
    {
        $all = "";
        if (is_array($joins)) {
            foreach ($joins as $join => $on) {
                $all .= " " . $join . " ON (" . $this->resolveConditionData($on) . ")";
            }
        }
        return $all;
    }

    /**
     * Resolve from array and quote fields
     * @param array $fields
     * @return string quoted string
     */
    public function resolveFields($fields)
    {
        $all = [];
        if (is_array($fields)) {
            foreach ($fields as $k => $v) {
                if (is_numeric($k)) {
                    $all[] = $this->quoteColumn($v);
                }
                else {
                    $all[] = $this->quoteColumn($k) . " AS " . $this->quoteColumn($v);
                }
            }
        }
        else {
            return $fields;
        }

        return implode(", ", $all);
    }

    /**
     * Build SELECT like query string from params
     * @param string $table Table name
     * @param string|array $fields Field name or array of fields
     * @param array $joins Joins array
     * @param array $params Param array
     * @param string $method Method name
     * @param string $fieldFunction Function applied on field/fields
     * @return string Builded query string
     */
    public function selectContext($table, $fields, $joins, $params, $method = "SELECT", $fieldFunction = false)
    {
        $query = $method . " ";
        if ($fieldFunction) {
            $query .= $fieldFunction . "(";
        }
        $query .= $this->resolveFields($fields); // " LEFT JOIN ..."
        if ($fieldFunction) {
            $query .= ")";
        }
        $query .= " FROM " . $this->quoteColumn($table);
        $query .= $this->resolveJoins($joins); // " LEFT JOIN ..."
        $query .= $this->resolveCondition($params); // " WHERE ..."

        return $query;
    }

    /**
     * Quote column
     * @param string|array $columnData Column name or array of column names
     * @return string quoted column/columns
     */
    public function quoteColumn($columnData)
    {
        if (is_array($columnData)) {
            $processed = [];
            foreach ($columnData as $c) {
                $exploded    = explode(" ", $c, 2);
                $processed[] = $this->_quoteColumn_one($exploded[0]) . (count($exploded) > 1 ? " " . $exploded[1] : "");
            }
            return implode(", ", $processed);
        }

        $exploded = explode(" ", $columnData, 2);
        return $this->_quoteColumn_one($exploded[0]) . (count($exploded) > 1 ? " " . $exploded[1] : "");
    }

    // protected functions

    protected function _quoteColumn_one($columnOrFunction)
    {
        preg_match('/([a-zA-Z0-9_\-\.]*)\s*\(([a-zA-Z0-9`\'_\-]*)\)/i', $columnOrFunction, $match);
        if (strlen($match[1]) > 1) { // is function - paste RAW
            return $columnOrFunction;
        }

        return "`" . str_replace('.', '`.`', $columnOrFunction) . "`";
    }

    protected function _quoteVal($value, $dontQuoteSingle = false)
    {
        if (is_array($value)) {
            $processed = [];
            foreach ($value as $c) {
                $processed[] = $this->_quoteVal_one($c);
            }
            return "(" . implode(", ", $processed) . ")";
        }

        if ($dontQuoteSingle) {
            return $value;
        }
        return $this->_quoteVal_one($value);
    }

    protected function _quoteVal_one($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        return $this->quote($value);
    }

}
