<?php


namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;

class  GoodsPayInfo extends  Base {

    public  function getPayInfo($good_id, $ration_id){
        $is_Inter = isAppPositiveInteger($good_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_ration_Inter =  isPageInteger($ration_id);
        if(!$is_ration_Inter){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $gpiModel =   model('GoodsPayInfo') ;
        $data =   $gpiModel->getPayInfoBy($good_id , $ration_id);
        if(empty($data)){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  null   ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  $data    ,
            ));
            throw  $e ;
        }
    }

}