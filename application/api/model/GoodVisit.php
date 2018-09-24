<?php

namespace    app\api\model  ;
use think\Model;

class  GoodVisit extends   Model {


    /*
     *  根据用户编码获取该用户的浏览记录
     *  @param    $user_id     int  :  用户编码
     *  @param    $searchKey   obj  :  分页对象
     *  @return   Array
     * */
    public    function  getLookListyBy($user_id , $searchKey){
        $beforetime = strtotime('-30 days') ;
        $currtime = time() ;
         $sql = ' select gv.visit_id, g.goods_id, g.goods_name, CONCAT( "'.BASE_PATH.'" ,g.original_img) original_img, g.shop_price  from  tp_goods_visit gv '
                . ' join tp_goods g on g.goods_id = gv.goods_id and g.is_delete = 1 '
                 . '  where gv.user_id = '.$user_id.' and gv.visittime between  "'.$beforetime.'"  and  "'.$currtime.'" '
                . ' ORDER BY gv.visittime desc '
                . ' limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;
         $data = $this->query($sql);
         return  $data ;
    }
}