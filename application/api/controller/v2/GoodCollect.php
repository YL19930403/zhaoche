<?php

namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;
use think\Db;

class  GoodCollect  extends  Base {

    //查询收藏接口
    public  function  getcollect($good_id, $user_id){
          $is_gid_Inter =   isAppPositiveInteger($good_id) ;
          if(!$is_gid_Inter){
              $e = new  ParameterException(array(
                  'msg' => '商品编码必须为正整数' ,
                  'errorCode' => '391023',
                  'datas'  =>  null  ,
              ));
              throw  $e ;
          }

         $is_uid_Inter =   isAppPositiveInteger($user_id) ;
         if(!$is_uid_Inter){
            $e = new  ParameterException(array(
                'msg' => '用户编码必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

         $gcModel =   model('GoodCollect');
         $colldata =   $gcModel->getCollectBy($good_id , $user_id);
//        var_dump($colldata) ; die ;
        if($colldata == null ){
                // 说明没有该用户没有收藏这件商品
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  [
                    'is_collect' => 0,
                ],
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  => $colldata,
            ));
            throw  $e ;
        }
    }

    // 收藏和取消收藏接口
    public  function   collect($good_id, $user_id){
        $is_gid_Inter =   isAppPositiveInteger($good_id) ;
        if(!$is_gid_Inter){
            $e = new  ParameterException(array(
                'msg' => '商品编码必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_uid_Inter =   isAppPositiveInteger($user_id) ;
        if(!$is_uid_Inter){
            $e = new  ParameterException(array(
                'msg' => '用户编码必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }


        //先根据 商品编码 和 用户编码 去查询(is_delete)， 如果没有，则插入，如果有，则根据is_delete的值去反向更新
        $data =  Db::table('tp_goods_collect')->where(array('user_id'=>$user_id, 'goods_id'=>$good_id,'is_delete'=>1))->field('is_collect')->find() ;

        if($data == null ){
            $res =  Db::name('goods_collect')->insert(array('user_id'=>$user_id, 'goods_id'=>$good_id,'add_time'=>time() ,'is_collect'=>1)) ;
            if($res){
                $e = new  ParameterException(array(
                    'msg' => 'success' ,
                    'errorCode' => '0',
                    'datas'  => [
                        'is_collect' => 1,
                    ],
                ));
                throw  $e ;
            }else{
                $e = new  ParameterException(array(
                    'msg' => '收藏失败' ,
                    'errorCode' => '391053',
                    'datas'  => [
                        'is_collect' => 0,
                    ] ,
                ));
                throw  $e ;
            }
        }else{
            if($data['is_collect'] == 0){
                 $result = Db::name('goods_collect')->where(array('user_id'=>$user_id, 'goods_id'=>$good_id,'is_delete'=>1))->update(array('is_collect'=>1)) ;
                 if($result){
                     $e = new  ParameterException(array(
                         'msg' => 'success' ,
                         'errorCode' => '0',
                         'datas'  =>  [
                             'is_collect' => 1 ,
                         ],
                     ));
                     throw  $e ;
                 }else{
                     $e = new  ParameterException(array(
                         'msg' => '收藏失败' ,
                         'errorCode' => '391053',
                         'datas'  =>  [
                             'is_collect' => 0,
                         ],
                     ));
                     throw  $e ;
                 }
            }elseif ($data['is_collect'] == 1){
                 $result =   Db::name('goods_collect')->where(array('user_id'=>$user_id, 'goods_id'=>$good_id,'is_delete'=>1))->update(['is_collect'=>0]) ;
                 if($result){
                     $e = new  ParameterException(array(
                         'msg' => 'success' ,
                         'errorCode' => '0',
                         'datas'  =>  [
                             'is_collect' => 0 ,
                         ],
                     ));
                     throw  $e ;
                 }else{
                     $e = new  ParameterException(array(
                         'msg' => '取消收藏失败' ,
                         'errorCode' => '391053',
                         'datas'  =>  [
                             'is_collect' => 1 ,
                         ],
                     ));
                     throw  $e ;
                 }
            }
        }
    }


    //    查询 我的收藏列表
    public  function   getcollectlist($user_id){



        $is_uid_Inter =   isAppPositiveInteger($user_id) ;
        if(!$is_uid_Inter){
            $e = new  ParameterException(array(
                'msg' => '用户编码必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $gcModel =  model('GoodCollect');
        $data =  $gcModel->getCollectListBy($user_id);

            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  $data   ,
            ));
            throw  $e ;

    }


}

