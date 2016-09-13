<?php

namespace SAPF\Database;

class Model
{

    protected $_db;
    // data
    protected $_data;
    // params
    protected $_table;
    protected $_params;
    protected $_fields;
    protected $_joins;
    //
    protected $_field_primarykey;

    public function __construct(SQLDatabase $db, $table, $field_primarykey = "id")
    {
        $this->_db               = $db;
        $this->_field_primarykey = $field_primarykey;
        $this->_table            = $table;
        $this->_params           = [];
        $this->_fields           = "*";
        $this->_joins            = [];
    }

    // getters

    public function getPrimaryKeyName()
    {
        return $this->_field_primarykey;
    }

    public function getTableName()
    {
        return $this->_table;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getJoins()
    {
        return $this->_joins;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Set fields
     * @param array|string $fields
     * @return \SAPF\Database\Model
     */
    public function fields($fields)
    {
        $this->_fields = $fields;
        return $this;
    }

    /**
     * Set params, if value is false param will be unset
     * @param array $params
     * @return \SAPF\Database\Model
     */
    public function params($params)
    {
        foreach ($params as $k => $v) {
            if ($v) {
                $this->_params[$k] = $v;
            }
            else {
                unserialize($this->_params[$k]);
            }
        }
        return $this;
    }

    /**
     * Set param
     * If key prepends with "!" value won't be escaped
     * @param string $key
     * @param string|array $value
     * @return \SAPF\Database\Model
     */
    public function param($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Sets MATCH
     * @param string|array $columns One or more columns
     * @param string $keyword
     * @param string $mode Match mode
     * @return \SAPF\Database\Model
     */
    public function match($columns, $keyword, $mode = FALSE)
    {
        $this->_params[SQLDatabase::OPERATOR_PREFIX . 'match'] = [
            'columns' => $columns,
            'keyword' => $keyword,
        ];
        if ($mode) {
            $this->_params[SQLDatabase::OPERATOR_PREFIX . 'match']['mode'] = $mode;
        }
        return $this;
    }

    /**
     * Order BY
     * @param string|array $order one or more orders
     * for example "id ASC"
     * @return \SAPF\Database\Model
     */
    public function order($order)
    {
        $this->_params[SQLDatabase::OPERATOR_PREFIX . 'order'] = $order;
        return $this;
    }

    /**
     * GROUP BY
     * @param string $groupByField
     * @param array $havingParams Params for GROUP BY ... HAVING ...
     * If you don't want "HAVING" set to FALSE
     * @return \SAPF\Database\Model
     */
    public function group($groupByField, $havingParams = FALSE)
    {
        $this->_params[SQLDatabase::OPERATOR_PREFIX . 'group'] = $groupByField;
        if ($havingParams) {
            $this->_params[SQLDatabase::OPERATOR_PREFIX . 'having'] = $havingParams;
        }
        return $this;
    }

    /**
     * Offset
     * @param int $offset
     * @return \SAPF\Database\Model
     */
    public function offset($offset)
    {
        $this->_params[SQLDatabase::OPERATOR_PREFIX . 'offset'] = $offset;
        return $this;
    }

    /**
     * Limit
     * @param int $limit
     * @return \SAPF\Database\Model
     */
    public function limit($limit)
    {
        $this->_params[SQLDatabase::OPERATOR_PREFIX . 'limit'] = $limit;
        return $this;
    }

    /**
     * Set pagination object
     * @param \SAPF\Paging\Paging $paging
     * @return \SAPF\Database\Model
     */
    public function paging(\SAPF\Paging\Paging $paging)
    {
        $this->_params[SQLDatabase::OPERATOR_PREFIX . 'paging'] = $paging;
        return $this;
    }

    /**
     * Add JOIN LEFT
     * @param string $table Table name
     * @param array $on Join params
     * @param string $as alias for table name
     * If false - table won't be aliased
     * @return \SAPF\Database\Model
     */
    public function joinLeft($table, array $on, $as = false)
    {
        $this->_joins["LEFT JOIN `" . $table . "`" . ($as ? " AS `" . $as . "`" : "")] = $on;
        return $this;
    }

    /**
     * Custom, raw join
     * @param string $join for ex. JOIN LEFT `table` AS `tableXD`
     * @param array $on Join params
     * @return \SAPF\Database\Model
     */
    public function joinRaw($join, array $on)
    {
        $this->_joins[$join] = $on;
        return $this;
    }

    /**
     * Fetch one entity from database
     * @return array Result data
     */
    public function fetch()
    {
        return $this->_db->fetch($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Reset all params, joins and fields to defaults
     * @return \SAPF\Database\Model
     */
    public function reset()
    {
        $this->_params = [];
        $this->_fields = "*";
        $this->_joins  = [];
        return $this;
    }

    /**
     * Fetch all entities that match specified params
     * @return array Result datas
     */
    public function fetchAll()
    {
        if (isset($this->_params[SQLDatabase::OPERATOR_PREFIX . 'paging']) && $this->_params[SQLDatabase::OPERATOR_PREFIX . 'paging'] instanceof \SAPF\Paging\PagingInterface) {
            if ($this->_params[SQLDatabase::OPERATOR_PREFIX . 'paging']->getMax() <= 0) {
                $this->_params[SQLDatabase::OPERATOR_PREFIX . 'paging']->setMax($this->count());
            }
            $this->_params[SQLDatabase::OPERATOR_PREFIX . 'limit'] = $this->_params[SQLDatabase::OPERATOR_PREFIX . 'paging']->getDBLimit();
        }
        return $this->_db->fetchAll($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Maksimum
     * @return double maksimum
     */
    public function max()
    {
        return $this->_db->max($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Minimum
     * @return double minimum
     */
    public function min()
    {
        return $this->_db->min($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Avg
     * @return double average
     */
    public function avg()
    {
        return $this->_db->avg($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Sum
     * @return double sum
     */
    public function sum()
    {
        return $this->_db->sum($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Fetch count of entities that match specified params
     * @return int Count
     */
    public function count()
    {
        return $this->_db->count($this->_table, $this->_fields, $this->_params, $this->_joins);
    }

    /**
     * Check if at least one record with specified params exist
     * @return boolean Exists at least one record
     */
    public function has()
    {
        return $this->_db->has($this->_table, $this->_params, $this->_joins);
    }

    /**
     * Insert/Update data
     * @param array $data
     * @return Inserted primary key OR -1 if record is updated
     */
    public function save($data)
    {
        if (isset($data[$this->_field_primarykey])) {
            // update or insert
            if ($this->_db->insertOrUpdate($this->_table, $data) === -1) {
                // updated!
                return -1;
            }
        }
        else {
            // insert
            $data[$this->_field_primarykey] = $this->_db->insert($this->_table, $data);
        }

        return $data[$this->_field_primarykey];
    }

    /**
     * Delete records from MySQL with given params
     * @throws InvalidArgumentException
     */
    public function delete()
    {
        if (count($this->_params) < 1) {
            return false;
        }
        return $this->_db->delete($this->_table, $this->_params, $this->_joins);
    }

    /**
     * Alias for delete()
     * @throws InvalidArgumentException
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * Returns array with data. All keys not in field array will be stripped
     * @param array $data Input data
     * @return array
     */
    public function filterData($data)
    {
        $fields = $this->getFields();
        foreach ($data as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

}
