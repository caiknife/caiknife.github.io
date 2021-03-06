<?php
class AppModel extends Model {
    public function save($data=null, $validate=true, $fieldList=array()) {
        if (isset($this->data) && isset($this->data[$this->name])) {
            unset($this->data[$this->name]['modified']);
        }
        if (isset($data) && isset($data[$this->name])) {
            unset($data[$this->name]['modified']);
        }
        return parent::save($data, $validate, $fieldList);
    }

    public function relation($params) {
        $this->_params['recursive'] = $params;
        return $this;
    }

    public function where(array $params) {
        $this->_params['conditions'] = $params;
        return $this;
    }

    public function order($params) {
        $this->_params['order'] = $params;
        return $this;
    }

    public function fields($params) {
        $this->_params['fields'] = $params;
        return $this;
    }

    public function page($params) {
        $this->_params['page'] = $params;
        return $this;
    }

    public function limit($params) {
        $this->_params['limit'] = $params;
        return $this;
    }

    public function offset($params) {
        $this->_params['offset'] = $params;
        return $this;
    }

    public function joins($params) {
        $this->_params['joins'] = $params;
        return $this;
    }

    public function group($params) {
        $this->_params['group'] = $params;
        return $this;
    }

    public function count() {
        $r = $this->find('count', $this->_params);
        $this->_params = array();
        return $r;
    }

    public function first() {
        $r = $this->find('first', $this->_params);
        $this->_params = array();
        return $r;
    }

    public function select() {
        $r = $this->find('all', $this->_params);
        $this->_params = array();
        return $r;
    }

    public function lists() {
        $r = $this->find('list', $this->_params);
        $this->_params = array();
        return $r;
    }

    public function threaded() {
        $r = $this->find('threaded', $this->_params);
        $this->_params = array();
        return $r;
    }

    public function neighbors() {
        $r = $this->find('neighbors', $this->_params);
        $this->_params = array();
        return $r;
    }

    public function getConditions() {
        if (isset($this->_params['conditions'])) {
            return $this->_params['conditions'];
        }
        return array();
    }

    protected function _appendConditions($params) {
        if (!isset($this->_params['conditions'])) {
            $this->_params['conditions'] = $params;
        } else {
            $this->_params['conditions'] = am($this->_params['conditions'], $params);
        }
        return $this;
    }
}