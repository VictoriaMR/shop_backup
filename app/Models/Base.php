<?php

namespace App\Models;

class Base
{
    protected $_instance;
    protected $_connect;
    protected $_table;
    protected $_primaryKey;

    protected function getInstance()
    {
        if (is_null($this->_instance)) {
            $this->_instance = new \frame\Query($this->_connect);
        }
        $this->_instance->table($this->_table);
        return $this->_instance;
    }

    public function loadData($id = null, $field = '')
    {   
        if (empty($id)) {
            return $this->getInstance()->field($field)->get();
        } else {
            return $this->getInstance()->where($this->_primaryKey, $id)->field($field)->find();
        }
    }

    public function updateDataById($id, $data)
    {
        $id = (int) $id;
        if (empty($id)) return false;
        return $this->getInstance()->where($this->_primaryKey, $id)->update($data);
    }

    public function deleteById($id)
    {
        $id = (int) $id;
        if (empty($id)) return false;
        return $this->getInstance()->where($this->_primaryKey, $id)->delete();
    }

    public function getCount(array $where = []) 
    {
        return $this->getInstance()->where($where)->count();
    }

    public function getInfoByWhere(array $where = [], $fields = [])
    {
        return $this->getInstance()->where($where)->field($fields)->find();
    }

    public function getListByWhere(array $where = [], $fields = [])
    {
        return $this->getInstance()->where($where)->field($fields)->get();
    }

    public function getPaginationList($total = 0, $list = [], $page = 1, $pagesize = 10)
    {
        return [
            'total' => $total,
            'pagesize' => $pagesize,
            'page' => $page,
            'page_total' => ceil($total / $pagesize),
            'list' => $list,
        ];
    }

    public function __call($func, $arg)
    {
        return $this->getInstance()->$func(...$arg);
    }
}