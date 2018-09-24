<?php

namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;
use  think\Db;

class  Good  extends  Base {

    /*
   *  获取商品详情
   *  @param   $good_id   int   :   商品编码
   *  @return    Array
   * */
    public   function  getdetails($good_id){
      
        $is_inter =   isAppPositiveInteger($good_id) ;

        if(!$is_inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $gModel =  model('Good');
        $gdata =   $gModel->getv2GoodsDetail($good_id);


        if(!empty($gdata)){
            //处理 original_img
            $giModel =  model('GoodsImage');
            
            $gidata =  $giModel->getImages($good_id);

            $imgArr = [] ;
            if(!empty($gidata)){
                foreach ($gidata as $k => $v ){
                    $v['image_url'] = BASE_PATH . $v['image_url'];
                    $imgArr[$k]  = $v['image_url'] ;
                }

            }else{
                $imgArr = [] ;
            }
            $gdata['original_img'] = $imgArr ;

            // 处理 goods_content
            $content  = html_entity_decode($gdata['goods_content']) ;
            if($content != ""){
                $cont_arr = getSrcImg($content) ;
            }else{
                $gdata['goods_content'] = [];
            }

            if(!empty($cont_arr)){
                $gdata['goods_content'] = [] ;
                foreach ( $cont_arr   as $k=>$v ) {
                   $gdata['goods_content'][$k] =   BASE_PATH  . $v ;
                }
            }
           // 如果 该用户  查看过该商品，那么只需要更新浏览时间，否则需要插入一条新的记录
            // $user_id =  $this->jsondata[1] ;
            // $vdata =  Db::name('goods_visit')->where(array('user_id'=>$user_id, 'goods_id'=>$good_id))->field('visit_id')->find() ;
            // if($vdata == NULL  ){
            //     $map = [
            //         'goods_id' => $good_id,
            //         'user_id'  => $user_id ,
            //         'visittime' => time(),
            //         'cat_id'  => $gdata['cat_id'],
            //         'extend_cat_id' => $gdata['extend_cat_id'],
            //     ];
            //     Db::name('goods_visit')->insert($map) ;

            // }else{
            //     Db::name('goods_visit')->where(array('goods_id'=>$good_id,'user_id'=>$user_id))->update(['visittime'=>time()]);
            // }
        }

        $e = new  ParameterException(array(
            'msg' => 'success' ,
            'errorCode' => '0',
            'datas'  =>  $gdata  ,
        ));
        throw  $e ;
    }

   

}

