<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;

class  GoodsImage  extends  Model{

    protected  $table = 'tp_goods_images';
    
    /*
     *  根据传入的商品编码获取到对应的商品图片编码
     *  @param    $goods_id     int   :  商品编码
     *  @return   Array
     * */
    public  function   getImgidBy($goods_id){
          return   Db::table('tp_goods_images')
                        ->where('goods_id', $goods_id)
                        ->field('img_id')
                        ->select() ;
    }

       /*
     *  根据传入的商品编码获取到数据
     *  @param   $good_id    商品编码  ：  int
     *  @return  Array
     * */
    public  function  getImages($good_id){
            return  $this->where('goods_id', $good_id)
                        ->field('img_id, image_url')
                        ->select() ;
    }

}


