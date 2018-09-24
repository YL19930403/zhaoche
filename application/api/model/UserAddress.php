<?php


namespace    app\api\model  ;

use think\Db;
use think\Model;

class  UserAddress  extends  Model {

    protected  $table = 'tp_user_address' ;

    /*
     *  数据入库
     *  @param     $post     array   :   数组
     *  @return    boolean
     * */
    public  function  insertData($post){
            return   $this->insert($post) ;
    }

    /*
     *  根据用户编码查询出对应的用户地址列表
     *  @param    $user_id    int  :  用户编码
     *  @return   array
     * */
    public  function  getAddrListBy($user_id){
      return    $this->alias('ua')->where('ua.is_delete', 1)
                ->where('ua.user_id', $user_id)
                ->join('tp_region r1', 'r1.id = ua.province')
                ->join('tp_region r2', 'r2.id = ua.city')
                ->join('tp_region r3', 'r3.id = ua.district')
                ->field('ua.address_id, ua.consignee, ua.mobile,ua.is_default , CONCAT_ws( ",",r1.name  ,  r2.name,  r3.name  ,  ua.address ) address')
                ->select() ;
    }

    /*
     *  删除地址
     *  @param   $address_id    int  ; 地址编码
     *  @return   boolean
     * */
    public  function  deladressBy($address_id){
         return   $this->where('is_delete', 1)
                        ->where('address_id', $address_id)
                        ->save(['is_delete'=>0]);
    }



}
