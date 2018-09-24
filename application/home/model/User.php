<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\9\25 0025
 * Time: 20:02
 */

namespace  app\home\model ;

use think\Model;

class   User  extends  Model{
    protected  $table  = 'tp_users' ;

    public  function  insertData($data){
//            $uModel = model('User');
            return  $this->insert($data) ;

    }
}