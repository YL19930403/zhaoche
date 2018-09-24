<?php



namespace    app\api\model  ;

use think\Model;

class  GoodsPayInfo  extends  Model {

    protected  $table = 'tp_goods_payment_info' ;

    public  function getPayInfoBy($good_id , $ration_id){
         return   $this->alias('gpi')
                        ->where('gpi.goods_id', $good_id)
                        ->where('gpi.pay_id', $ration_id)
                        ->where('gpi.is_delete' , 1)
                        ->field('gpi.id, gpi.first_payment, gpi.purchase_tax, gpi.carriage_fee, gpi.security_deposit, gpi.GPS, gpi.month_payment,gpi.remarks')
                        ->find() ;
    }
}