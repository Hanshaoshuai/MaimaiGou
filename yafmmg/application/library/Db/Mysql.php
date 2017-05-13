<?php

namespace Db;

/**
 * mysql操作类
 *
 * @author zxcvdavid@gmail.com
 *
 */
class Mysql implements DbInterface {

    private static $_instances;
    private $_dbh; //PDO对象
    private $_sth; //SQL执行对象
    private $_sql;
    private $_conf;
    private $field = "*";
    private $where = array();
    private $order;
    private $tabName;
    private $params = array(); //条件参数
    private $limit;

    private function __construct($conf) {
        try {
            $db = new \PDO('mysql:dbname=' . $conf['db_name'] . ';host=' . $conf['db_host'], $conf['db_user'], $conf['db_pwd'], array(\PDO::ATTR_PERSISTENT => true));
            $db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING);
            $db->query("SET NAMES UTF8");
            $this->_dbh = $db;
        } catch (PDOException $e) {
            echo '<pre>';
            echo '<b>Connection failed:</b> ' . $e->getMessage();
            die();
        }
    }

    public function init() {
        $this->field = "*";
        $this->where = array();
        $this->order = array();
        $this->params = array();
        $this->limit = "";
    }

    public static function getInstance($conf, $tableName = "") {
        $idx = md5($conf['db_host'] . $conf['db_name'].$tableName);
        if (!isset(self::$_instances[$idx])) {
            self::$_instances[$idx] = new self($conf);
        }
        self::$_instances[$idx]->tabName = $tableName;
        return self::$_instances[$idx];
    }
    /**
     * 获取DB 对象
     * 
     */    
    public function getDbh(){
        return $this->_dbh;
    }
    /**
     * 查询的字段
     * @param type $field 字段名(a,b,c)
     * @return $this
     */
    public function field($field = "*") {
        if (is_array($field)) {
            $this->field = "";
            foreach ($field as $v) {
                $this->field .= $v . ",";
            }
            $this->field = rtrim($this->field, ",");
        } elseif (is_string($field)) {
            $this->field = $field;
        }
        return $this;
    }

    /**
     * 查询条件
     * @param type $where   查询条件 支持数组/字符串 array("where"=>"value","id"=>"5") array("id"=>"?")
     * @param type $values  条件参数
     * @return $this
     */
    public function where($where, $values = array()) {        
        if (is_array($where)&&!empty ($where)) {
            $n=0;
            foreach ($where as $key => $v) {
                $this->where[] = " " . $key . "=? ";                
                if ($v !== "?") {                    
                    $this->params[] = $v;
                }else{
                    $this->params[]=$values[$n];
                }
                $n++;
            }
        } elseif(!empty ($where)) {
            $this->where[] = $where;     
            $this->params = array_merge($this->params, $values);
        }                        
        return $this;
    }

    /**
     * 排序
     * @param type $order   排序字段 支持字符串(a desc,b asc) 数组 array("a"=>"desc","b"=>"asc")
     */
    public function order($order) {
        if (is_array($order)) {
            foreach ($order as $key => $v) {
                if (strtolower($v) !== "asc") {
                    $v = "desc";
                }
                $this->order[] = $key . " " . $v . "";
            }
        } else {
            $this->order[] = $order;
        }        
        return $this;
    }

    /**
     * 排序
     * @param type $order   排序字段 
     */
    public function limit($limit1 = 0, $limit2 = "") {
        if($limit2==""){
            $limit2=$limit1;
            $limit1=0;
        }
        $this->limit = "limit ".$limit1 . "," . $limit2;
        return $this;
    }

    /**
     * 表名设置
     * @param type $tabName 表名
     * @return $this
     */
    public function table($tabName) {
        $this->tabName = $tabName;
        return $this;
    }

    public function halt($msg = '', $sql = '') {
        $error_info = $this->_sth->errorInfo();
        $s = '<pre>';
        $s .= '<b>Error:</b>' . $error_info[2] . '<br />';
        $s .= '<b>Errno:</b>' . $error_info[1] . '<br />';
        $s .= '<b>Sql:</b>' . $this->_sql;
        exit($s);
    }

    public function execute($sql, $values = array()) {
        $sth = $this->_dbh->prepare($sql);
        $sth->execute($values);
        $this->sth = $sth;
        $this->init();
        return $sth;
    }

    /**
     * 直接运行查询语句
     * @param type $sql sql语句
     * @param type $param   参数
     * @param type $fetch_style 
     * @return 查询结果
     */
    public function query($sql, $param = array(),$is_res=true) {
        if($is_res){
            return $this->execute($sql, $param)->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            return $this->execute($sql, $param);
        }
    }

    /**
     * 查询所有数据
     * @param type $fetch_style 无需填
     * @return type
     */
    public function getAll() {
        return $this->execute($this->getSql(), $this->params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSql() {
        $sql = "select " . $this->field . " from " . $this->tabName . "";        
        if (!empty($this->where)) {
            $where = " where ";
            $n=0;
            foreach ($this->where as $key => $v) {
                if($n==0){
                    $where .= $v ;
                }else{
                    $where .= " and " .$v  ;
                }
                $n++;
            }            
            $sql .= $where;
        }
        if (!empty($this->order)) {
            $order = "";
            foreach ($this->order as $orderV) {
                $order .= $orderV . ",";
            }
            $orderNew = rtrim($order, ",");
            $sql .= " order by " . $orderNew;
        }
        $sql.=" ".$this->limit;
        return $sql;
    }

    function getCol($params = array(), $column_number = 0) {
        
    }

    /**
     * 获取受影响行数
     * @return type
     */
    function getRowCount() {
        return $this->execute($this->getSql(), $this->params)->rowCount();
    }

    /**
     * 获取一条记录
     * @param type $fetch_style
     * @return type
     */
    function getOne() {
        return $this->execute($this->getSql(), $this->params)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 
     * @param type 添加的数据array(field=>value,...)
     * @return type ID 
     */
    function insert($data = array()) {
        $params = array();
        $filed = "";
        $values = "";
        foreach ($data as $key => $v) {
            $filed .= "`".$key."`,";
            $values .= "?,";
            $params[] = $v;
        }
        $filed1 = rtrim($filed, ",");
        $values1 = rtrim($values, ",");
        $sql = "INSERT INTO `" . $this->tabName . "`(" . $filed1 . ")VALUES(" . $values1 . ")";
        $this->execute($sql, $params);
        return $this->_dbh->lastInsertId();
    }

    /**
     * 
     * @param type $data  要更新的数据 array(field=>value,...)
     * @return type
     */
    function update($data = array()) {
        $sql = "UPDATE `" . $this->tabName . "` SET ";
        $UpField = "";
        foreach ($data as $key => $v) {
            $UpField .= "`".$key."`='" . $v . "',";
        }
        $sql .= rtrim($UpField, ",");
        if (!empty($this->where)) {
            $where = " where ";
            foreach ($this->where as $whereVal) {
                $where .= $whereVal . " and ";
            }
            $where .= "1=1";
            $sql .= " " . $where;
        }
        return $this->execute($sql, $this->params)->rowCount();
    }

    /**
     * 
     * @return type 受影响行数
     */
    function delete() {
        $sql = "DELETE FROM  `" . $this->tabName . "` ";
        if (!empty($this->where)) {
            $where = " where ";
            foreach ($this->where as $v) {
                $where .= $v . " and ";
            }
            $where .= "1=1";
            $sql .= " " . $where;
        }
        return $this->execute($sql, $this->params)->rowCount();
    }

    /*
     * 处理事务（暂时不用）
     */

    function transaction($sql) {
        try {
            $this->_dbh->beginTransaction();
        return    $this->_dbh->exec($sql);
        } catch (PDOException $ex) {
            $this->_dbh->rollBack();
        }
    }

    //事务开始
    function begintransaction(){
        $this->_dbh->beginTransaction();
    }

    //事务提交
    function commit(){
        $this->_dbh->commit();
    }

    //事务回滚
    function rollBack(){
        $this->_dbh->rollBack();
    }

    function close() {
        unset($this->_instances);
        unset($this->_dbh);
    }

    //防止对象被复制
    public function __clone() {
        trigger_error('Clone is not allowed !');
    }
  public function sqlexec($sql){
    return  $this->_dbh->exec($sql);
  }
}
