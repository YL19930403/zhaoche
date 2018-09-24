<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;

class  GoodCollect  extends  Model {

    protected   $name  = 'goods_collect' ;

    /*
     * 根据商品编码  和  用户编码  获取到该条收藏记录
     * @param    $good_id    int   : 商品编码
     * @param    $user_id    int   : 用户编码
     * @return    Array
     * */
    public  function getCollectBy($good_id , $user_id){

        return   Db::table('tp_goods_collect')
                    ->where(array('user_id'=>$user_id, 'goods_id'=>$good_id,'is_delete'=>1))
                    ->field('is_collect')
                    ->find() ;

    }

       /*
     *  根据 用户编码获取该用户收藏的商品
     *  @param   $user_id   int  : 用户编码
     *  @return   Array
     * */
    public  function  getCollectListBy($user_id){

          $sql = ' select  gc.collect_id, g.goods_id,CONCAT("'.BASE_PATH.'", g.original_img) original_img ,g.goods_name,g.shop_price,g.is_icon1_show,g.is_icon2_show
                    from  tp_goods_collect gc  '
                . '  join tp_goods g on  gc.goods_id = g.goods_id and g.is_delete = 1    '
                . ' where  gc.user_id = '.$user_id.'  and  gc.is_delete = 1 and  gc.is_collect = 1  ' ;

          $data  = $this->query($sql) ;
          return  $data ;
    }

}
