<?php


namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;
use think\Db;

class  Order  extends   Base{

    public  function  getnews(){
           $oModel = model('Order');
          //获取时间戳
           $map =  getTopTimeStamp() ;
           $orderlist =  $oModel->getLastsList($map);

           if(!empty($orderlist)){
               $e = new  ParameterException(array(
                   'msg' => 'success' ,
                   'errorCode' => '0',
                   'datas'  =>  $orderlist ,
               ));
               throw  $e ;
           }else{
               $e = new  ParameterException(array(
                   'msg' => 'success' ,
                   'errorCode' => '0',
                   'datas'  =>  [] ,
               ));
               throw  $e ;
           }
//            var_dump($orderlist)  ; die ;
    }


    /*
     * 获取用于订单列表
     * */
    public  function  getOrderList($user_id, $page=0, $per_page=10){

        $is_Inter = isAppPositiveInteger($user_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $isPageInter =   isPageInteger($page) ;
        $isperPageInter =   isPageInteger($per_page);
        if(!$isperPageInter  ||  !$isPageInter){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }


        //先根据用户编码查询出用户  手机号码 ， 用户名

        $udata =  M('users')->where('user_id' , $user_id)->field('user_id, nickname, mobile')->find() ;

        if(empty($udata)){
            $e = new  ParameterException(array(
                'msg' => '该用户不存在' ,
                'errorCode' => '391011',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }else{
            $orderModel =  model('Order');
            $orderList = $orderModel->getListBy($user_id , $page, $per_page);
            if(!empty($orderList)){
                 $udata['orderlist'] = $orderList ;
            }else{
                $udata['orderlist'] = [] ;
            }
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  $udata  ,
            ));
            throw  $e ;
        }

    }



    /*
     *  取消订单接口
     *  @param    $order_id    int   : 订单编码
     *  @return   boolean
     * */
    public  function  cancelOrder($order_id){

        $is_Inter = isAppPositiveInteger($order_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '订单编码必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $res =  M('order')->where('order_id', $order_id)->save(['order_status' => 3]) ;

        if($res){       //取消订单成功
            //
            $map = [
                'order_id'       =>  $order_id ,
                'action_user'   =>  0 ,
                'order_status'  =>  3 ,
                'action_note'   =>  "您取消了订单" ,
                'log_time'      =>  time() ,
                'status_desc'   =>  "用户取消订单" ,
            ];
            M('order_action')->save($map);

            $e = new  ParameterException(array(
                'msg' => '取消订单成功' ,
                'errorCode' => '0',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '取消订单失败' ,
                'errorCode' => '391046',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

    }

    /*
     *   获取订单详情接口
     *   @param   $rec_id  int  :  订单商品id
     * */
    public  function  getOrderDetail($rec_id){
         $is_Inter = isAppPositiveInteger($rec_id) ;
         if(!$is_Inter){
             $e = new  ParameterException(array(
                 'msg' => '参数必须为正整数' ,
                 'errorCode' => '391023',
                 'datas'  =>  null  ,
             ));
             throw  $e ;
         }

         $orderModel =   model('Order');
         $orderData =   $orderModel->getDetailBy($rec_id) ;
//         var_dump($orderData) ; die ;
         if(!empty($orderData)){
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'  =>  $orderData  ,
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
     *  释放掉图片
     * */
    protected  function  freeImage($ugData) {
         if($ugData['license'] != ""){
             $ugData['license'] = substr($ugData['license'], 1) ;
             @unlink($ugData['license']) ;
         }

        if($ugData['identity'] != ""){
            $ugData['identity'] = substr($ugData['identity'], 1) ;
            @unlink($ugData['identity']) ;
        }

        if($ugData['credit'] != ""){
            $ugData['credit'] = substr($ugData['credit'], 1) ;
            @unlink($ugData['credit']) ;
        }

        if($ugData['security'] != ""){
            $ugData['security'] = substr($ugData['security'], 1) ;
            @unlink($ugData['security']) ;
        }
        if($ugData['ownership'] != ""){
            $ugData['ownership'] = substr($ugData['ownership'], 1) ;
            @unlink($ugData['ownership']) ;
        }
        if($ugData['commencial'] != ""){
            $ugData['commencial'] = substr($ugData['commencial'], 1) ;
            @unlink($ugData['commencial']) ;
        }
        

    }


    /*
   *  提交订单
   * */
    public  function  submitOrder($user_id, $good_id){

        $files  =  request()->file() ;

       


    //getallheaders();     //获取HTTP 头信息

        $is_uid_Inter =  isAppPositiveInteger($user_id) ;
        $is_gid_Inter =  isAppPositiveInteger($good_id) ;

        if(!$is_uid_Inter  ||  !$is_gid_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files == NULL ){
            $e = new  ParameterException(array(
                'msg' => '缺少必填参数' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        //获取姓名
        $consignee =   request()->post('consignee') ;

        if(!$consignee ){
            $e = new  ParameterException(array(
                'msg' => '姓名不能为空' ,
                'errorCode' => '391017',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }



        //获取支付方式
        $pay_code = request()->post('pay_code') ;
        if(!$pay_code){
            $e = new  ParameterException(array(
                'msg' => '支付方式不能为空' ,
                'errorCode' => '391017',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        //获取商品价格
        $goods_price = request()->post('goods_price') ;
        if(!$goods_price){
            $e = new  ParameterException(array(
                'msg' => '商品价格不能为空' ,
                'errorCode' => '391030',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        //获取商品名称
        $goods_name = request()->post('goods_name') ;
        if(!$goods_name){
            $e = new  ParameterException(array(
                'msg' => '商品名称不能为空' ,
                'errorCode' => '391031',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        //获取商品数量
        $goods_num =   request()->post('goods_num') ;
        if(!$goods_num){
            $e = new  ParameterException(array(
                'msg' => '商品数量不能为空' ,
                'errorCode' => '391032',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        //获取手机号码
        $mobile = request()->post('mobile') ;
        if(!$mobile){
            $e = new  ParameterException(array(
                'msg' => '手机号码不能为空' ,
                'errorCode' => '391020',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }


        

        $contents = [
            'consignee' =>  $consignee ,
            'goods_price' => $goods_price ,
            'goods_num' =>  $goods_num ,
            'goods_name'  => $goods_name ,
            'mobile'     => $mobile ,
            'pay_code'  => $pay_code ,
        ];
      
        //获取省市区
        $province =  request()->post('province') ;
        $city =  request()->post('city') ;
        $district =  request()->post('district') ;
//        if(!$province  || !$city  || !$district ){
//            $e = new  ParameterException(array(
//                'msg' => '地区不能为空' ,
//                'errorCode' => '391028',
//                'datas'  =>  null  ,
//            ));
//            throw  $e ;
//        }

        if($province){
                $contents['province'] = $province  ;
        }
        if($city){
            $contents['city'] = $city  ;
        }
        if($district){
            $contents['district'] = $district  ;
        }

        
        $goods_sn = request()->post('goods_sn') ;
        if($goods_sn){
            $contents['goods_sn'] = $goods_sn  ;
        }

        //数据入库(order)
        $oModel =  model('Order');


        //先判断是否已经有了订单，如果有了则不能提交
        $order_data =  M('Order')->where(array('user_id' => $user_id , 'is_delete' => 1 ,'order_status'=>0))->find() ;
        if(!empty($order_data)){
            $e = new  ParameterException(array(
                'msg' => '您有未完成的订单' ,
                'errorCode' => '391045',
                'datas'  =>  null   ,
            ));
            throw  $e ;
        }

        $result  =   $oModel->saveData($user_id , $contents);

        if($result ){
            $ogModel =   model('OrderGoods');
            $carriage_fee =  request()->post('carriage_fee');
            $radion = request()->post('radion') ;
            if(isset($carriage_fee)){
                $contents['carriage_fee'] = $carriage_fee ;
            }
            if(isset($radion)){
                $contents['radion'] = $radion ;
            }

            $res =    $ogModel->saveData($result, $good_id,$contents) ;
            if($res){

                  $action_info = array(
                    'order_id'        =>$result,
                    'action_user'     =>0,
                    'action_note'     => '您提交了订单，请等待系统确认',
                    'status_desc'     =>'提交订单', //''
                    'log_time'        =>time(),
                );
                M('order_action')->insertGetId($action_info);
                
                //上传图片， 图片数据入库（tp_user_good_image）

                $save_url = 'public/upload/usergoods/' . date('Y', time()) . '/' . date('m-d', time());
                foreach ($files as  $k=>$file ){
//                         var_dump($file) ; die ;
                    $info = $file->rule('uniqid')->validate(['size' => 1024 * 1024 * 50, 'ext' => 'jpg,png,gif,jpeg'])->move($save_url);
                    if($info){
                        // 成功上传后 获取上传信息
                        // 输出 jpg
                        $comment_img[$k][] = '/'.$save_url . '/' . $info->getFilename();
                    }else{
                        $e = new  ParameterException(array(
                            'msg' => '图片上传失败' ,
                            'errorCode' => '391010',
                            'datas'  =>  null   ,
                        ));
                        throw  $e ;
                    }
                }

                $map = [
                    'user_id' =>  $user_id ,
                    'good_id' =>  $good_id ,
                    'create_time' => time() ,
                    'view'        => 0,
                ];
                if(!empty($comment_img["identity"])){
                    $map['identity'] =  $comment_img["identity"][0] ;
                }
                if (!empty($comment_img["license"])){
                    $map['license'] =  $comment_img["license"][0] ;
                }
                if (!empty($comment_img["credit"])){
                    $map['credit'] =  $comment_img["credit"][0] ;
                }
                if(!empty($comment_img["security"])){
                    $map['security'] =  $comment_img["security"][0] ;
                }
                if (!empty($comment_img["bankflow"])){
                    $map['bankflow'] =  $comment_img["bankflow"][0] ;
                }
                if(!empty($comment_img["ownership"])){
                    $map['ownership'] =  $comment_img["ownership"][0] ;
                }
                 if(!empty($comment_img["commencial"])){
                    $map['commencial'] =  $comment_img["commencial"][0] ;
                }


                //先根据user_id   和 good_id  查询该条记录是否存在， 如果存在则更新， 如果不存在则插入
                $ugData =  M('user_good_image')->where('user_id' , $user_id)->where('good_id' , $good_id)->find() ;
                if(empty($ugData)){
                    $resl =  M('user_good_image')->insert($map) ;
                }else{
                    //删除之前的图片
                    $this->freeImage($ugData) ;
                    $resl =  M('user_good_image')->where('user_id' , $user_id)->where('good_id' , $good_id)->save($map) ;
                }
                if($resl){
                    $e = new  ParameterException(array(
                        'msg' => '订单提交成功' ,
                        'errorCode' => '0',
                        'datas'  =>  null   ,
                    ));
                    throw  $e ;
                }else{
                    $e = new  ParameterException(array(
                        'msg' => '订单提交失败' ,
                        'errorCode' => '391026',
                        'datas'  =>  null   ,
                    ));
                    throw  $e ;
                }
            }else{
                $e = new  ParameterException(array(
                    'msg' => '订单提交失败' ,
                    'errorCode' => '391026',
                    'datas'  =>  null   ,
                ));
                throw  $e ;
            }

        }else{
            $e = new  ParameterException(array(
                'msg' => '订单提交失败' ,
                'errorCode' => '391026',
                'datas'  =>  null   ,
            ));
            throw  $e ;
        }
    }
}

























