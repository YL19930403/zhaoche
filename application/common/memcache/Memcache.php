<?php

namespace  app\common\memcache ;

class  Memcache {
    private static $_instance ;

    private  function __construct(){}


    // //单例方法
     public  static function  singleton(){
         if(!isset(self::$_instance)){
              self::$_instance =  memcache_connect("localhost",11211) ;
         }
         return self::$_instance ;
     }

     //阻止用户复制对象实例
     public  function  __clone()
     {
          trigger_error('clone  is not allow',E_USER_ERROR) ;
     }
}










