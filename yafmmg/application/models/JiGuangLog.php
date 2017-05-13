<?php
/**
 * User: zhouyouth
 * Date: 2017/4/27
 * Time: 10:33
 */
 class  JiGuangLogModel extends BaseModel{

         public   function  insert($data){
          return    M('yaf_system_jiguang_log')->insert($data);
         }

 }