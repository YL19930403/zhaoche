<?php

namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;
use app\common\redis\Redis;
use think\Config;
use think\Cache;
use think\Db;


class   Good  extends  Base{
//  获取热门商品
    public  function   gethots($page=0 , $per_page=10){

        $goodModel =   model('Good');

        $pageInter =   isPageInteger($page) ;
        $perpageInter = isPageInteger($per_page) ;

        if( !( $pageInter  &&  $perpageInter )){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }

        $searchKey =  model('SearchKey');
        $searchKey->setStartNum($page, $per_page) ;
        $goodModel =   model('Good');
        
        //如果在token中传入了机构编码，则按照机构编码查询对应的商品信息
        $inst_id = request()->header('instid') ;

        if(isset($inst_id)) {
            $is_instid_Inter = isAppPositiveInteger($inst_id);
            if (!$is_instid_Inter) {
                $e = new  ParameterException(array(
                    'msg' => '机构编码必须为正整数',
                    'errorCode' => '391023',
                ));
                throw  $e;
            }
        }else{
            $inst_id =  0 ;
        }

       $datas =  $goodModel->getHotList( $searchKey ,$inst_id );

      if(!empty($datas)){
          foreach ($datas as $k=>$v ){
                if($v != ""){
                       $v['original_img'] =   BASE_PATH . '/' . $v['original_img'];
                       $datas[$k] = $v ;
                }
          }

          $e = new  ParameterException(array(
              'msg' => 'success' ,
              'errorCode' => '0',
              'datas'  =>  $datas ,
          ));
          throw  $e ;
      }else{
          $e = new  ParameterException(array(
              'msg' => 'success' ,
              'errorCode' => '0',
              'datas'  =>  null  ,
          ));
          throw  $e ;
      }

    }


    

      /*
     *  查询所有的商品列表
     *  @param    $page    int   :   当前页
     *  @param    $per_page  int  :  每页显示的条目数
     *  @param    $key     int  :   搜索条件(分类编码， 品牌编码， 是否热销，时间数组编码)
     *  @return   Array
     * */
    public  function   getAllList($page = 0 , $per_page = 10  ){
        $is_page_Inter =   isPageInteger($page) ;
        $is_per_page_Inter =   isPageInteger($per_page) ;

        if(!$is_page_Inter  || !$is_per_page_Inter){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }

        $urldata = request()->get() ;
        $searchKey =    model('SearchKey') ;
        $searchKey->setStartNum($page, $per_page) ;
//        $user_id = $this->jsondata[1] ;

        $inst_id = request()->header('instid') ;
        if(isset($inst_id)) {
            $is_instid_Inter = isAppPositiveInteger($inst_id);
            if (!$is_instid_Inter) {
                $e = new  ParameterException(array(
                    'msg' => '机构编码必须为正整数',
                    'errorCode' => '391023',
                ));
                throw  $e;
            }
        }else{
            $inst_id =  0 ;
        }


        if(isset($urldata['cat_id'])){     //分类
            $cat_id = $urldata['cat_id'] ;
            $is_Int =  isPageInteger($cat_id) ;
            if(!$is_Int){
                $e = new  ParameterException(array(
                    'msg' => '分类参数必须为整数' ,
                    'errorCode' => '391022',
                ));
                throw  $e ;
            }

            $catModel =    model('Category');
//            if($cat_id == "100"){
//                $datalist = $catModel->getNotLimitCatList($searchKey , $inst_id ) ;
//            }else{
                $datalist =  $catModel->getCatListBy($cat_id , $searchKey,$inst_id );
//            }

        }elseif (isset($urldata['brand_id'])){
            $brand_id = $urldata['brand_id'] ;
            $is_Int =  isPageInteger($brand_id) ;
            if(!$is_Int){
                $e = new  ParameterException(array(
                    'msg' => '品牌参数必须为整数' ,
                    'errorCode' => '391022',
                ));
                throw  $e ;
            }

            $brandModel =   model('Brand');
//            if($brand_id == "100"){
//                $datalist =  $brandModel->getNotLimitBrandData($searchKey, $inst_id);
//            }else{
                $datalist = $brandModel->getAllListBy($brand_id, $searchKey, $inst_id);
//            }
        }elseif (isset($urldata['year'])){
              $year =  $urldata['year'] ;
              $is_year_Int =  isAppPositiveInteger($year) ;
              if(!$is_year_Int){
                  $e = new  ParameterException(array(
                      'msg' => '年份必须为正整数' ,
                      'errorCode' => '391023',
                  ));
                  throw  $e ;
              }

              //根据年份去查询所有满足该条件的商品
                   $gModel =   model('Good');
                   $datalist =  $gModel->searchListBy($year , $searchKey, $inst_id );
        }elseif (isset($urldata['price'])){
            $price = $urldata['price'] ;
            $is_year_Int =  isAppPositiveInteger($price) ;
            if(!$is_year_Int){
                $e = new  ParameterException(array(
                    'msg' => '价格参数必须为正整数' ,
                    'errorCode' => '391023',
                ));
                throw  $e ;
            }
             $gModel =   model('Good');
             $datalist =  $gModel->getGoodsByPrice($inst_id ,$searchKey , $price );

        }else{   //所有商品
             $gModel =   model('Good');
             $datalist =   $gModel->getAllList($searchKey , $inst_id);
        }

        if(!empty($datalist)){
            foreach ($datalist as  $k => $v ){
                     $v['original_img']  = BASE_PATH . $v['original_img'] ;
                     $datalist[$k]  = $v ;
            }
            
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  $datalist   ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  null   ,
            ));
            throw  $e ;
        }

    }


    /*
     *  获取商品详情
     *  @param   $good_id   int   :   商品编码
     *  @return    Array
     * */
    public  function  getdetails($good_id){

          $is_inter =   isAppPositiveInteger($good_id) ;

          if(!$is_inter){
              $e = new  ParameterException(array(
                  'msg' => '参数必须为正整数' ,
                  'errorCode' => '391023',
                  'datas'  =>  null  ,
              ));
              throw  $e ;
          }


        //先根据用户编码查询出该用户的姓名， 电话， 配送地址
        $uModel =  model('User');
//        $udata =  $uModel->getUserInfo($user_id);

//        if(empty($udata)){
//            $e = new  ParameterException(array(
//                'msg' => '该用户不存在' ,
//                'errorCode' => '391011',
//                'datas'  =>  null   ,
//            ));
//            throw  $e ;
//        }

         $goodmodel =  model('Good');

          $goods_info =  $goodmodel->getGoodsDetail($good_id) ;
//        var_dump($goods_info) ; die ;
            if(!empty($goods_info)){
                $goods_info['original_img'] = BASE_PATH . $goods_info['original_img'] ;
                $content  = html_entity_decode($goods_info['goods_content']) ;

                if($content != ""){
                    $cont_arr = getSrcImg($content) ;
                }else{
                    $goods_info['goods_content'] = [];
                }

                if(!empty($cont_arr)){
                    $goods_info['goods_content'] = [] ;
                    foreach ( $cont_arr   as $k=>$v ) {
                        $goods_info['goods_content'][$k] =   BASE_PATH  . $v ;
                    }

                }

               // 进入了商品详情页，就要在 商品浏览历史表 中记录一下
            // 如果 该用户  查看过该商品，那么只需要更新浏览时间，否则需要插入一条新的记录
            // $user_id =  $this->jsondata[1] ;
            // $vdata =  Db::name('goods_visit')->where(array('user_id'=>$user_id, 'goods_id'=>$good_id))->field('visit_id')->find() ;
            // if($vdata == NULL  ){
            //     $map = [
            //         'goods_id' => $good_id,
            //         'user_id'  => $user_id ,
            //         'visittime' => time(),
            //         'cat_id'  => $goods_info['cat_id'],
            //         'extend_cat_id' => $goods_info['extend_cat_id'],
            //     ];
             
            //     Db::name('goods_visit')->insert($map) ;

            // }else{
            //     Db::name('goods_visit')->where(array('goods_id'=>$good_id,'user_id'=>$user_id))->update(['visittime'=>time()]);
            // }

                $e = new  ParameterException(array(
                    'msg' => 'success' ,
                    'errorCode' => '0',
                    'datas'  =>  $goods_info  ,
                ));
                throw  $e ;
            }else{
                $e = new  ParameterException(array(
                    'msg' => 'success' ,
                    'errorCode' => '0',
                    'datas'  =>  null   ,
                ));
                throw  $e ;
            }

    }

    /*
     *  获取精品推荐列表接口
     * */
    public  function  getRecomList($page=0, $per_page=10 ){

        $pageInter =   isPageInteger($page) ;
        $perpageInter = isPageInteger($per_page) ;

        if( !( $pageInter  &&  $perpageInter )){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }

           // $user_id =  $this->jsondata[1] ;
          $goodModel =   model('Good');
          $searchKey =   model('SearchKey');
          $searchKey->setStartNum($page, $per_page) ;
           //如果在token中传入了机构编码，则按照机构编码查询对应的商品信息
          $inst_id = request()->header('instid') ;


        if(isset($inst_id)) {
            $is_instid_Inter = isAppPositiveInteger($inst_id);
            if (!$is_instid_Inter) {
                $e = new  ParameterException(array(
                    'msg' => '机构编码必须为正整数',
                    'errorCode' => '391023',
                ));
                throw  $e;
            }
        }else{
            $inst_id =  0 ;
        }

          $goodList =   $goodModel->getRecomList($searchKey, $inst_id) ;
           
          if(!empty($goodList)){
              $e = new  ParameterException(array(
                  'msg' => 'success' ,
                  'errorCode' => '0',
                  'datas' =>  $goodList ,
              ));
              throw  $e ;
          }else{
              $e = new  ParameterException(array(
                  'msg' => 'success' ,
                  'errorCode' => '0',
              ));
              throw  $e ;
          }

    }

    /*
     *  根据 品牌 查询所有的商品
     *   @param    $brand_id   int    :   品牌编码
     *   return   Array
     * */
    public  function  getbrandgoods($brand_id, $page= 0 , $per_page = 10 ){
        $pageInter =   isPageInteger($page) ;
        $perpageInter = isPageInteger($per_page) ;
        if( !( $pageInter  &&  $perpageInter )){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }

             $is_b_Inter =   isAppPositiveInteger($brand_id) ;
             if(!$is_b_Inter){
                 $e = new  ParameterException(array(
                     'msg' => '参数必须为正整数' ,
                     'errorCode' => '391023',
                 ));
                 throw  $e ;
              }

             $gModel =   model('Good')  ;
             $gData =  $gModel->getGoodsBy($brand_id , $page , $per_page) ;
             if(!empty($gData)){
                 $e = new  ParameterException(array(
                     'msg' => 'success' ,
                     'errorCode' => '0',
                     'datas' =>  $gData ,
                 ));
                 throw  $e ;
             }else{
                 $e = new  ParameterException(array(
                     'msg' => 'success' ,
                     'errorCode' => '0',
                     'datas' =>  null  ,
                 ));
                 throw  $e ;
             }
    }


    /*
     * 获取按照年份、商品价格分组的数据
     * @return   Array
     * */
    public  function   getDateList(){
        //该接口 与 服务器上的代码不一致， 因为我本地没有部署redis , 服务器上有用到redis
        $all_total = [] ;

        $inst_id = request()->header('instid') ;
        if(isset($inst_id)) {
            $is_instid_Inter = isAppPositiveInteger($inst_id);
            if (!$is_instid_Inter) {
                $e = new  ParameterException(array(
                    'msg' => '机构编码必须为正整数',
                    'errorCode' => '391023',
                ));
                throw  $e;
            }
        }else{
            $inst_id =  0 ;
        }

        //        年份
        $gModel =    model('Good') ;
        $dates = $gModel->getDateList($inst_id);
        if(!empty($dates)){
            $all_total['years'] = $dates ;
        }else{
            $all_total['years'] =  [] ;
        }

        //先查询出所有的商品，再便利处理
        $data =   $gModel->getAllGoods($inst_id);
        //判断缓存的count 和查询出来的count是否相等
        $cache_count =   Cache::get('yearcount') ;

        $total = [] ;
        if(!empty($data)){
            $count =  count($data) ;
            if($cache_count == $count ){
                $total = Cache::get('yeardata');
            }else{
                foreach ($data as  $k => $v ){
                    $floatValue =  floatval($v['shop_price']) ;
                    //10000.00       10000.00  -  50000.00     ,  50000.00 - 100000.00     , 100000.00  -   150000.00  , 150000.00 - 200000.00  , 200000.00
                    if($floatValue  < 10000.00){
                        //一万以下
                        $total[0]['price'] = 1 ;
                        $total[0]['count'] += 1 ;
                    }elseif (  ( 10000.00  < $floatValue  )  && ( $floatValue <  50000.00) ){
                        //   1 - 5 万
                        $total[1]['price'] = 2 ;
                        $total[1]['count'] += 1 ;
                    }elseif (( 50000.00  < $floatValue  )  && (  $floatValue < 100000.00)){
                        // 5 - 10 万
                        $total[2]['price'] = 3 ;
                        $total[2]['count'] += 1 ;
                    }elseif (( 100000.00  < $floatValue  )  && ( $floatValue < 150000.00)){
                        // 10 - 15 万
                        $total[3]['price'] = 4 ;
                        $total[3]['count'] += 1 ;
                    }elseif (( 150000.00  < $floatValue  )  && (  $floatValue < 200000.00)){
                        // 15 - 20 万
                        $total[4]['price'] = 5 ;
                        $total[4]['count'] += 1 ;
                    }else{
                        // 20 万 以上
                        $total[5]['price'] = 6 ;
                        $total[5]['count'] += 1 ;
                    }
                }
                //写入缓存
                Cache::set('yeardata', $total, 0 ) ;
                Cache::set('yearcount',$count, 0);
            }
        }else{
            $all_total['prices'] = [] ;
        }

        if(!empty($total)){
            $tmp = [];
            $i = 0 ;
            foreach ($total as  $v ){
                $tmp[$i] = $v ;
                $i += 1 ;
            }
            $all_total['prices'] =  $tmp ;
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  $all_total  ,
            ));
            throw  $e ;
        }
    }
  

}























