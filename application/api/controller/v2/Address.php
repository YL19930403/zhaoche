<?php


namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;

class  Address extends  Base {

    //    添加收货地址
    public   function   add_address($user_id){

        $is_Inter = isAppPositiveInteger($user_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $post =   request()->post(); // $this->post ;
        if(empty($post)){
            $e = new  ParameterException(array(
                'msg' => '缺少必填参数' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($post['province'])){
            $e = new  ParameterException(array(
                'msg' => '省份不能为空' ,
                'errorCode' => '391028',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_p_Inter = isAppPositiveInteger($post['province']) ;
        if(!$is_p_Inter){
            $e = new  ParameterException(array(
                'msg' => '省份编码必须为正整数' ,
                'errorCode' => '391044',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($post['city'])){
            $e = new  ParameterException(array(
                'msg' => '市不能为空' ,
                'errorCode' => '391028',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_c_Inter = isAppPositiveInteger($post['city']) ;
        if(!$is_c_Inter){
            $e = new  ParameterException(array(
                'msg' => '市编码必须为正整数' ,
                'errorCode' => '391044',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($post['district'])){
            $e = new  ParameterException(array(
                'msg' => '区不能为空' ,
                'errorCode' => '391028',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_d_Inter = isAppPositiveInteger($post['district']) ;
        if(!$is_d_Inter){
            $e = new  ParameterException(array(
                'msg' => '区编码必须为正整数' ,
                'errorCode' => '391044',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($post['consignee'])  ||  ( trim($post['consignee']) == "" ) ){
            $e = new  ParameterException(array(
                'msg' => '收货人不能为空' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }


        if(!isset($post['address'])  ||  ( trim($post['address']) == "" ) ){
            $e = new  ParameterException(array(
                'msg' => '收货地址不能为空' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isAppNotEmpty($post['mobile'])){
            $e = new  ParameterException(array(
                'msg' => '手机号码不能为空' ,
                'errorCode' => '391020',
            ));
            throw  $e ;
        }


        $ismobile =   isAppMobile($post['mobile']) ;
        if(!$ismobile){
            $e = new  ParameterException(array(
                'msg' => '手机号码格式不正确' ,
                'errorCode' => '391014',
            ));
            throw  $e ;
        }

        $uaModel =  model('UserAddress');
        $post['user_id'] = $user_id ;
        $res =  $uaModel->insertData($post) ;
        if($res){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '添加收货地址失败' ,
                'errorCode' => '391047',
            ));
            throw  $e ;
        }
    }

    //查询收货地址列表
    public  function  getaddrlist($user_id){
        $is_Inter = isAppPositiveInteger($user_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $uaModel =  model('UserAddress');
        $data =  $uaModel->getAddrListBy($user_id) ;
        $e = new  ParameterException(array(
            'msg' => 'success' ,
            'errorCode' => '0',
            'datas' => $data
        ));
        throw  $e ;
    }

//    删除地址接口
    public   function  deladress($address_id){
        $is_Inter = isAppPositiveInteger($address_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $uaModel =  model('UserAddress');
        $res =  $uaModel->deladressBy($address_id);
        if($res){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '删除地址失败' ,
                'errorCode' => '391048',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }
    }

//        设为默认收货地址
    public  function  defaultadress($address_id){
        $is_Inter = isAppPositiveInteger($address_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        //先获取该用户编码， 再将该用户编码对应的所有地址改为“非默认”状态, 最后再将这条address_id 对应的改为默认状态
         $user_id = $this->jsondata[1] ;
         M('user_address')->where(array('user_id'=>$user_id , 'is_delete'=>1))->save(array('is_default'=>0));
         $row = M('user_address')->where(array('user_id'=>$user_id,'address_id'=>$address_id,'is_delete'=>1))->save(array('is_default'=>1));
         if($row){
               $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'  =>  null  ,
             ));
             throw  $e ;
         }else{
             $e = new  ParameterException(array(
                 'msg' => '设置默认收货地址失败' ,
                 'errorCode' => '391049',
                 'datas'  =>  null  ,
             ));
             throw  $e ;
         }
    }


    //    修改收货地址
    public  function  updateadress($address_id){
        $is_Inter = isAppPositiveInteger($address_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

       $putdata = request()->post() ;
        
//       var_dump($putdata) ; die ;
        
        if(empty($putdata)){
            $e = new  ParameterException(array(
                'msg' => '缺少必填参数' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($putdata['province'])){
            $e = new  ParameterException(array(
                'msg' => '省份不能为空' ,
                'errorCode' => '391028',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_p_Inter = isAppPositiveInteger($putdata['province']) ;
        if(!$is_p_Inter){
            $e = new  ParameterException(array(
                'msg' => '省份编码必须为正整数' ,
                'errorCode' => '391044',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($putdata['city'])){
            $e = new  ParameterException(array(
                'msg' => '市不能为空' ,
                'errorCode' => '391028',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_c_Inter = isAppPositiveInteger($putdata['city']) ;
        if(!$is_c_Inter){
            $e = new  ParameterException(array(
                'msg' => '市编码必须为正整数' ,
                'errorCode' => '391044',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($putdata['district'])){
            $e = new  ParameterException(array(
                'msg' => '区不能为空' ,
                'errorCode' => '391028',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $is_d_Inter = isAppPositiveInteger($putdata['district']) ;
        if(!$is_d_Inter){
            $e = new  ParameterException(array(
                'msg' => '区编码必须为正整数' ,
                'errorCode' => '391044',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isset($putdata['consignee'])  ||  ( trim($putdata['consignee']) == "" ) ){
            $e = new  ParameterException(array(
                'msg' => '收货人不能为空' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }


        if(!isset($putdata['address'])  ||  ( trim($putdata['address']) == "" ) ){
            $e = new  ParameterException(array(
                'msg' => '收货地址不能为空' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if(!isAppNotEmpty($putdata['mobile'])){
            $e = new  ParameterException(array(
                'msg' => '手机号码不能为空' ,
                'errorCode' => '391020',
            ));
            throw  $e ;
        }


        $ismobile =   isAppMobile($putdata['mobile']) ;
        if(!$ismobile){
            $e = new  ParameterException(array(
                'msg' => '手机号码格式不正确' ,
                'errorCode' => '391014',
            ));
            throw  $e ;
        }

//        $uaModel =  model('UserAddress');
//        $post['user_id'] = $user_id ;
        $res =  M('user_address')->where(array('address_id'=>$address_id,'is_delete'=>1))->save($putdata) ;
        if($res){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '更新收货地址失败' ,
                'errorCode' => '391050',
            ));
            throw  $e ;
        }
    }

}

















