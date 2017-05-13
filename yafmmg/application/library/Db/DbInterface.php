<?php

namespace Db;

/*
 * Db接口定义
 *
 * @author zxcvdavid@gmail.com
 *
 */

interface DbInterface {

    //static public function getInstance(); //要求所有数据连接皆为单例
    /**
     * 直接运行SQL语句
     * @param  $sql sql语句 
     * @param  $param 绑定参数  
     * @example 
     *          $sql="select * from tab where field=value"; 
     *          $db->query($sql);
     *          OR
     *          $sql="select * from tab where field=? and field1=?";
     *          $params=array(param,param1);
     *          $db->query($sql,$params);     
     * @return 查询结果
     */
    function query($sql, $param);

    function transaction($query); //事务

    /**
     * 查询的字段
     * @param type $field 字段名(a,b,c)
     * @example $db->filed(filed,filed,filed,...) or $db->filed(array(filed,filed,....))
     * @return $this
     * 
     */
    function field($field);

    /**
     * 查询条件
     * @param  $where   查询条件 支持数组/字符串 array("where"=>"value","id"=>"5") or array("id"=>"?")
     * @param  $values  条件参数  array(param,param1,....);
     * @example 
     *          $db->where("field=1 and field=2 ....");
     *          OR
     *          $db->where("field=? and field=? ....",array(param,parame1,....));
     *          OR     
     *          $db->where(array(field=>value,field1=>value1,....));
     *          OR
     *          $db->where(array(field=>?,field1=>?,....),array(param,parame1,....));
     * @return $this
     * 
     */
    function where($where);

    /**
     * 排序
     * @param  $order   string/array
     * @example $db->order("field asc,field desc") or $db->order(array("a"=>"desc","b"=>"asc"))
     * @return $this
     * 
     */
    function order($order);

    /**
     * 
     * @param  $tabName 表名
     * @example $db->table(tableName)
     */
    function table($tabName);

    /**
     * 
     * @param  返回数据条数
     * @example $db->limit(0,1)
     */
    function limit($limt1=0,$limit=50);

    /**
     * 得到一条记录
     * @example $db->getOne();
     *      
     */
    function getOne();

    /**
     * 从结果集中取得一列作为关联数组(暂不可使用)
     * @example $db->getOne();
     *      
     */
    function getCol($query);

    /**
     * 查询全部记录
     * @example           
     *          $db->table()->where()->order()->limit()->getAll();
     * @return type array
     */
    function getAll();

    /**
     * 添加数据
     * @param  $data  要插入的数据 array(field=>value,field1=>value1,...)
     * @reurn  $ID 返回ID
     */
    function insert($data);

    /**
     * 添加数据
     * @param  $data  要更新的数据 array(field=>value,field1=>value1,...)
     * @reurn  $rowCount 返回受影响行数
     */
    function update($data); //更新一条记录;

    /**
     * 删除数据
     * @param  
     * @example 
     *          $db->delete();
     *          OR
     *          $db->where()->delete();
     *          OR
     *          $db->table()->where()->delete();
     * @reurn  $rowCount 返回受影响行数
     */
    function delete();

    function close(); //关闭数据库连接
}
