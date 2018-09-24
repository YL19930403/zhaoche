<?php

namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;

class   Attr extends  Base{

    /*
     * 查看商品属性接口
     * */
    public  function  getattr($good_id){

        $is_Inter =   isAppPositiveInteger($good_id) ;

        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas' => null  ,
            ));
            throw  $e ;
        }

        $attrModel =  model('Attr') ;

        $attrArr =   $attrModel->getAttrById($good_id);
        if(!empty($attrArr)){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => $attrArr  ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => null  ,
            ));
            throw  $e ;
        }

    }

}
