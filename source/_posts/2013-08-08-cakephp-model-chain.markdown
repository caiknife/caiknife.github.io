---
layout: post
title: "为CakePHP的Model增加链式操作"
date: 2013-08-08 09:33
comments: true
categories: php cakephp
---
{% codeblock lang:php %}
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
    
    protected $_params;
    /*
     * custom data query method goes here
     */
    public function relation($params) { $this->_params['recursive'] = $params; return $this; }
    
    public function where($params) { $this->_params['conditions'] = $params; return $this; }
    
    public function order($params) { $this->_params['order'] = $params; return $this; }
    
    public function fields($params) { $this->_params['fields'] = $params; return $this; }
    
    public function page($params) { $this->_params['page'] = $params; return $this; }
    
    public function limit($params) { $this->_params['limit'] = $params; return $this; }

    public function offset($params) { $this->_params['offset'] = $params; return $this; }
    
    public function joins($params) { $this->_params['joins'] = $params; return $this; }
    
    public function group($params) { $this->_params['group'] = $params; return $this; }
    
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
    
    public function drop($cascade=false) {
        if (!empty($this->_params['conditions'])) {
            $r = $this->deleteAll($this->_params['conditions'], $cascade);
        } else {
            $r = array();
        }
        $this->_params = array();
        return $r;
    }
}
{% endcodeblock %}
