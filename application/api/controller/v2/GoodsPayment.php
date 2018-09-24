<?php


namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;
use think\Db;

class  GoodsPayment  extends  Base{

    public  function    getPayRatio($good_id){
         $is_inter =   isAppPositiveInteger($good_id) ;

        if(!$is_inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }
        $data =    Db::table('tp_goods_payment_info')
                ->alias('pi')
                ->distinct(true)
                ->join('tp_goods_payment p','p.pay_id = pi.pay_id')
                ->where('pi.goods_id', $good_id)
                ->where('pi.is_delete', 1)
                ->field('pi.pay_id,p.radion')
                ->order('p.radion  asc')
                ->select();

         if(empty($data)){
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'  =>  null  ,
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
