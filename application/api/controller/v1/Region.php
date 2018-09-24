<?php

namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;
use  think\Config ;

class  Region  extends  Base {

     public  function  getRegionList(){
             $rModel =   model('PickUp') ;
             $data =   $rModel->getRegionList() ;
             if(empty($data)){
                 $e = new  ParameterException(array(
                     'msg' => 'success' ,
                     'errorCode' => '0',
                     'datas'  =>  []  ,
                 ));
                 throw  $e ;
             }else{
                 $e = new  ParameterException(array(
                     'msg' => 'success' ,
                     'errorCode' => '0',
                     'datas'  =>  $data  ,
                 ));
                 throw  $e ;
             }
    }
}

