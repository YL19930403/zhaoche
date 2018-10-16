<?php

namespace app\api\controller\v2 ;

use app\common\logic\OssLogic;
use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;
use OSS\OssClient;
use think\Config;
require_once './vendor/aliyun-oss-php-sdk/autoload.php';

class User  extends   Base {
    //    用户登录接口
    public   function  login(){

        $uModel = model('User');
        $token =  request()->header('token') ;
        $is_token =    isAppNotEmpty($token) ;

        if($is_token){
            // token登录
            $this->validateToken($token , time()) ;
            $user_id =  $this->jsondata[1]  ;
            if($user_id){    //取出memcache中的用户编码
                //根据用户编码去获取用户登录信息

                $data =  $uModel->getUserTokenInfo($user_id) ;
                if(!empty($data)){
                    $e = new  ParameterException(array(
                        'msg' => '登录成功' ,
                        'errorCode' => '0',
                        'datas'  =>  $data
                    ));
                    throw  $e ;
                }else{
                    $e = new  ParameterException(array(
                        'msg' => '该用户不存在',
                        'errorCode' => '391011',
                        'datas' => null
                    ));
                    throw  $e;
                }
            }else{
                $e = new  ParameterException(array(
                    'msg' => 'token已过期' ,
                    'errorCode' => '391035',
                    'datas'  =>  null
                ));
                throw  $e ;
            }
        }else {
            //账号密码登录
            $postdata = request()->post();
            $isMobile = isAppMobile($postdata['mobile']);
            if ($isMobile == false) {
                $e = new  ParameterException(array(
                    'msg' => '手机格式不正确',
                    'errorCode' => '391014',
                ));
                throw  $e;
            }

            if (!isAppNotEmpty($postdata['password'])) {
                $e = new  ParameterException(array(
                    'msg' => '密码不能为空',
                    'errorCode' => '391014',
                ));
                throw  $e;
            }

            //根据 手机号码  ， 密码  去查询数据
            $data = $uModel->identiMobilePass2($postdata);


            if ($data != NULL) {
                //生成token
                if (config('app_debug') == false    ) {
                    $token = $this->generateToken($data['user_id']);
                    $data['token'] = $token;
                }

                $e = new  ParameterException(array(
                    'msg' => '登录成功',
                    'errorCode' => '0',
                    'datas' => $data
                ));
                throw  $e;
            } else {
                $e = new  ParameterException(array(
                    'msg' => '手机号或密码错误',
                    'errorCode' => '391007',
                    'datas' => $data
                ));
                throw  $e;
            }
        }
    }


    public  function  regist(){
        if(request()->isPost()){
            //根据   推荐人号码  找到推荐人uid

//          (new UserRegistValidate() )->goCheck() ;

            $postdata = request()->post() ;

            if(!isset($postdata['nickname'])  ||  !isset($postdata['mobile'])  || !isset($postdata['password'])  ||  !isset($postdata['recond_mobile'])   ){
                $e = new  ParameterException(array(
                    'msg' => '缺少必填参数' ,
                    'errorCode' => '391016',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['mobile'])){
                $e = new  ParameterException(array(
                    'msg' => '手机号码不能为空' ,
                    'errorCode' => '391020',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['recond_mobile'])){
                $e = new  ParameterException(array(
                    'msg' => '手机号码不能为空' ,
                    'errorCode' => '391020',
                ));
                throw  $e ;
            }

            


            if(!isAppNotEmpty($postdata['nickname'])){
                $e = new  ParameterException(array(
                    'msg' => '用户名不能为空' ,
                    'errorCode' => '391017',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['password'])){
                $e = new  ParameterException(array(
                    'msg' => '密码不能为空' ,
                    'errorCode' => '391015',
                ));
                throw  $e ;
            }

            // if(!isAppNotEmpty($postdata['id_card'])){
            //     $e = new  ParameterException(array(
            //         'msg' => '身份证号码不能为空' ,
            //         'errorCode' => '391018',
            //     ));
            //     throw  $e ;
            // }

            // if(!isIdentify($postdata['id_card'])){
            //     $e = new  ParameterException(array(
            //         'msg' => '身份证号码格式错误' ,
            //         'errorCode' => '391019',
            //     ));
            //     throw  $e ;
            // }

            if(!isAppMobile($postdata['mobile'])){
                $e = new  ParameterException(array(
                    'msg' => '手机号码格式不正确' ,
                    'errorCode' => '391014',
                ));
                throw  $e ;
            }



            $ismobile =   isAppMobile($postdata['mobile']) ;
            if(!$ismobile){
                $e = new  ParameterException(array(
                    'msg' => '手机号码格式不正确' ,
                    'errorCode' => '391014',
                ));
                throw  $e ;
            }

            if($postdata['mobile'] == $postdata['recond_mobile']){
                $e = new  ParameterException(array(
                    'msg' => '注册手机号不能与推荐人手机号一样' ,
                    'errorCode' => '391002',
                ));
                throw  $e ;
            }

              //获取第三方注册 传参
            $oauth =  request()->header('oauth') ;
            if(isset($oauth) ) {
                if ($oauth != "other") {
                    $e = new  ParameterException(array(
                        'msg' => '第三方来源传入错误',
                        'errorCode' => '391039',
                    ));
                    throw  $e;
                }
                $map['oauth'] = $oauth;
//                $map['inst_id'] = 2 ;
            }else{
                //验证码不能为空
                if(!isAppNotEmpty($postdata['vcode'])){
                    $e = new  ParameterException(array(
                        'msg' => '验证码不能为空' ,
                        'errorCode' => '391051',
                    ));
                    throw  $e ;
                }
                if(config('app_debug') == true ){
                    $mobile =$postdata['mobile'] ;
                    $code = $postdata['vcode'];
                    $check_code =check($code, $mobile, $scene="1");
                    if($check_code['status'] != 1){
                        $e = new  ParameterException(array(
                            'msg' => '手机验证码不正确' ,
                            'errorCode' => '391005',
                        ));
                        throw  $e ;
                    }
                }else{
                    //先把 验证码写死
                    if($postdata['vcode'] != "888888"){
                        $e = new  ParameterException(array(
                            'msg' => '验证码错误' ,
                            'errorCode' => '391005',
                        ));
                        throw  $e ;
                    }
                }
            }

            //查询该身份证号码是否已经存在
            $card =  M('users')->where('id_card', $postdata['id_card'])->find() ;
            if(!empty($card)){
                $e = new  ParameterException(array(
                    'msg' => '该用户已存在' ,
                    'errorCode' => '391006',
                ));
                throw  $e ;
            }

            //查询该手机号码是否已经存在    ， 查询推荐人手机号是否存在
            $result =   M('users')->where('mobile',$postdata['mobile'])->find();
//           var_dump($result) ; die ;
            if(!empty($result)){
                $e = new  ParameterException(array(
                    'msg' => '该号码已注册' ,
                    'errorCode' => '391001',
                ));
                throw  $e ;
            }

            $udata =  M('users')->where('mobile' , $postdata['recond_mobile'])->field('user_id,inst_id')->find() ;

            if($udata == NULL ){
                $e = new  ParameterException(array(
                    'msg' => '推荐人手机号不存在' ,
                    'errorCode' => '391003',
                ));
                throw  $e ;
            }

            //验证身份证信息
            // if(config('app_debug') == false){
            //     $validator = identity($postdata['nickname'], $postdata['id_card']) ;
            //     $validate_info =substr($validator,strpos($validator,'{'));
            //     $validate_info =   json_decode($validate_info, true ) ;
            //     if($validate_info != null){
            //         if($validate_info['result']['isok'] == false){
            //             $e = new  ParameterException(array(
            //                 'msg' => '身份信息不符合，请填写真实信息' ,
            //                 'errorCode' => '391012',
            //             ));
            //             throw  $e ;
            //         }
            //     }else{
            //         $e = new  ParameterException(array(
            //             'msg' => '身份认证失败' ,
            //             'errorCode' => '391013',
            //         ));
            //         throw  $e ;
            //     }
            // }

        

            //执行 用户注册的逻辑
            $map['password'] =   request()->post('password') ;
            $map['nickname'] = $postdata['nickname'];
            $map['uid'] = $udata['user_id'];
            $map['mobile'] = $postdata['mobile'];
            $map['reg_time'] = time() ;
            // $map['id_card'] = $postdata['id_card'] ;
            $map['inst_id'] = $udata['inst_id'];
            $map['token'] = md5(time().mt_rand(1,999999999));


            //数据入库
            $res =    M('users')->save($map);
            if($res){
                $e = new  ParameterException(array(
                    'msg' => '注册成功' ,
                    'errorCode' => '0',
                ));
                throw  $e ;
            }else{
                $e = new  ParameterException(array(
                    'msg' => '注册失败' ,
                    'errorCode' => '391004',
                ));
                throw  $e ;
            }

        }
    }


    //    用户注册接口
    public  function  regist2(){
        if(request()->isPost()){
            //根据   推荐人号码  找到推荐人uid

//          (new UserRegistValidate() )->goCheck() ;

            $postdata = request()->post() ;
            if(!isset($postdata['nickname'])  ||  !isset($postdata['mobile'])  || !isset($postdata['password'])  ||  !isset($postdata['recond_mobile'])  ||  !isset($postdata['id_card'])  ){
                $e = new  ParameterException(array(
                    'msg' => '缺少必填参数' ,
                    'errorCode' => '391016',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['mobile'])){
                $e = new  ParameterException(array(
                    'msg' => '手机号码不能为空' ,
                    'errorCode' => '391020',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['recond_mobile'])){
                $e = new  ParameterException(array(
                    'msg' => '手机号码不能为空' ,
                    'errorCode' => '391020',
                ));
                throw  $e ;
            }

            if(config('app_debug') == false){
                $mobile =$postdata['mobile'] ;
                $code = $postdata['vcode'];
                $check_code =check($code, $mobile, $scene="1");
                if($check_code['status'] != 1){
                    $e = new  ParameterException(array(
                        'msg' => '手机验证码不正确' ,
                        'errorCode' => '391005',
                    ));
                    throw  $e ;
                }
            }else{
                //先把 验证码写死
                if($postdata['vcode'] != "888888"){
                    $e = new  ParameterException(array(
                        'msg' => '验证码错误' ,
                        'errorCode' => '391005',
                    ));
                    throw  $e ;
                }
            }


            if(!isAppNotEmpty($postdata['nickname'])){
                $e = new  ParameterException(array(
                    'msg' => '用户名不能为空' ,
                    'errorCode' => '391017',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['password'])){
                $e = new  ParameterException(array(
                    'msg' => '密码不能为空' ,
                    'errorCode' => '391015',
                ));
                throw  $e ;
            }

            if(!isAppNotEmpty($postdata['id_card'])){
                $e = new  ParameterException(array(
                    'msg' => '身份证号码不能为空' ,
                    'errorCode' => '391018',
                ));
                throw  $e ;
            }

            if(!isIdentify($postdata['id_card'])){
                $e = new  ParameterException(array(
                    'msg' => '身份证号码格式错误' ,
                    'errorCode' => '391019',
                ));
                throw  $e ;
            }

            if(!isAppMobile($postdata['mobile'])){
                $e = new  ParameterException(array(
                    'msg' => '手机号码格式不正确' ,
                    'errorCode' => '391014',
                ));
                throw  $e ;
            }



            $ismobile =   isAppMobile($postdata['mobile']) ;
            if(!$ismobile){
                $e = new  ParameterException(array(
                    'msg' => '手机号码格式不正确' ,
                    'errorCode' => '391014',
                ));
                throw  $e ;
            }

            if($postdata['mobile'] == $postdata['recond_mobile']){
                $e = new  ParameterException(array(
                    'msg' => '注册手机号不能与推荐人手机号一样' ,
                    'errorCode' => '391002',
                ));
                throw  $e ;
            }



            //查询该身份证号码是否已经存在
            $card =  M('users')->where('id_card', $postdata['id_card'])->find() ;
            if(!empty($card)){
                $e = new  ParameterException(array(
                    'msg' => '该用户已存在' ,
                    'errorCode' => '391006',
                ));
                throw  $e ;
            }

            //查询该手机号码是否已经存在    ， 查询推荐人手机号是否存在
            $result =   M('users')->where('mobile',$postdata['mobile'])->find();
//           var_dump($result) ; die ;
            if(!empty($result)){
                $e = new  ParameterException(array(
                    'msg' => '该号码已注册' ,
                    'errorCode' => '391001',
                ));
                throw  $e ;
            }

            $udata =  M('users')->where('mobile' , $postdata['recond_mobile'])->field('user_id,inst_id')->find() ;

            if($udata == NULL ){
                $e = new  ParameterException(array(
                    'msg' => '推荐人手机号不存在' ,
                    'errorCode' => '391003',
                ));
                throw  $e ;
            }

            //验证身份证信息
            if(config('app_debug') == false){
                $validator = identity($postdata['nickname'], $postdata['id_card']) ;
                $validate_info =substr($validator,strpos($validator,'{'));
                $validate_info =   json_decode($validate_info, true ) ;
                if($validate_info != null){
                    if($validate_info['result']['isok'] == false){
                        $e = new  ParameterException(array(
                            'msg' => '身份信息不符合，请填写真实信息' ,
                            'errorCode' => '391012',
                        ));
                        throw  $e ;
                    }
                }else{
                    $e = new  ParameterException(array(
                        'msg' => '身份认证失败' ,
                        'errorCode' => '391013',
                    ));
                    throw  $e ;
                }
            }

            //获取第三方注册 传参
            $oauth =  request()->header('oauth') ;
            if(isset($oauth) ) {
                if ($oauth != "other") {
                    $e = new  ParameterException(array(
                        'msg' => '第三方来源传入错误',
                        'errorCode' => '391039',
                    ));
                    throw  $e;
                }
                $map['oauth'] = $oauth;
//                $map['inst_id'] = 2 ;
            }

            //执行 用户注册的逻辑
            $map['password'] =   request()->post('password') ;
            $map['nickname'] = $postdata['nickname'];
            $map['uid'] = $udata['user_id'];
            $map['mobile'] = $postdata['mobile'];
            $map['reg_time'] = time() ;
            $map['id_card'] = $postdata['id_card'] ;
            $map['inst_id'] = $udata['inst_id'];
            $map['token'] = md5(time().mt_rand(1,999999999));


            //数据入库
            $res =    M('users')->save($map);
            if($res){
                $e = new  ParameterException(array(
                    'msg' => '注册成功' ,
                    'errorCode' => '0',
                ));
                throw  $e ;
            }else{
                $e = new  ParameterException(array(
                    'msg' => '注册失败' ,
                    'errorCode' => '391004',
                ));
                throw  $e ;
            }

        }
    }


    //    更换密码
    public  function  reset(){
        // (new  UserResetValidate())->goCheck() ;
        $postdata = request()->post() ;

        if(config('app_debug') == true ){
            $mobile =$postdata['mobile'];
            $code = $postdata['vcode'];

            $check_code =check($code, $mobile, $scene="2");

            if($check_code['status'] != 1){
                $e = new  ParameterException(array(
                    'msg' => '验证码错误' ,
                    'errorCode' => '391005',
                ));
                throw  $e ;
            }
        }else{

            //先把 验证码写死
            if($postdata['vcode'] != "888888"){
                $e = new  ParameterException(array(
                    'msg' => '验证码错误' ,
                    'errorCode' => '391005',
                ));
                throw  $e ;
            }
        }


        if(!isAppNotEmpty($postdata['newpassword'])){
            $e = new  ParameterException(array(
                'msg' => '密码不能为空' ,
                'errorCode' => '391015',
            ));
            throw  $e ;
        }

        if(!isAppNotEmpty($postdata['mobile'])){
            $e = new  ParameterException(array(
                'msg' => '手机号码不能为空' ,
                'errorCode' => '391020',
            ));
            throw  $e ;
        }

        if(!isAppMobile($postdata['mobile'])){
            $e = new  ParameterException(array(
                'msg' => '手机号码格式不正确' ,
                'errorCode' => '391014',
            ));
            throw  $e ;
        }


        $password =   $postdata['newpassword'] ;

        $oldpassword =  M('users')->where('mobile', $postdata['mobile'])->getField('password') ;

        if($oldpassword != NULL ){
            if($password == $oldpassword){
                $e = new  ParameterException(array(
                    'msg' => '新密码不能和旧密码相同' ,
                    'errorCode' => '391008',
                ));
                throw  $e ;
            }
        }else{
            $e = new  ParameterException(array(
                'msg' => '手机号不存在' ,
                'errorCode' => '391003',
            ));
            throw  $e ;
        }

        $res =   M('users')->where('mobile', $postdata['mobile'])->save(['password' => $password]) ;
        if($res){
            $e = new  ParameterException(array(
                'msg' => '重置密码成功' ,
                'errorCode' => '0',
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '重置密码失败' ,
                'errorCode' => '391009',
            ));
            throw  $e ;
        }
    }

    //   用户签名
    public  function  signame(){
          $putdata = request()->put() ;
          if(empty($putdata)){
              $e = new  ParameterException(array(
                  'msg' => '参数不能为空' ,
                  'errorCode' => '391016',
              ));
              throw  $e ;
          }

        if(!isset($putdata['username'])  ||  ( trim($putdata['username']) == "")){
            $e = new  ParameterException(array(
                'msg' => '签名参数不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }

        $user_id =   $this->jsondata[1] ;
        $username = $putdata['username'] ;
        $res =    M('users')->where('user_id',$user_id)->save(array('username'=>$username));
        if($res){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '签名修改失败' ,
                'errorCode' => '391056',
            ));
            throw  $e ;
        }
    }

    /**
     * 测试 OSS
     * @throws ParameterException
     */
    public  function uploadimg(){
        $user_id =   request()->get('user_id') ;

        //(new  IDMustBeInteger())->goCheck() ;

//        $file  =   request()->file('image') ;
        $file = $_FILES['image'];

        if($file == NULL ){
            $e = new  ParameterException(array(
                'msg' => '上传图片不能为空' ,
                'errorCode' => '391021',
            ));
            throw  $e ;
        }

        if(!isAppPositiveInteger($user_id)){
            $e = new  ParameterException(array(
                'msg' => '用户编码必须为正整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }


        $id  =  M('users')->where('user_id' , $user_id)->getField('user_id');
        if($id == NULL ){
            $e = new  ParameterException(array(
                'msg' => '该用户不存在' ,
                'errorCode' => '391011',
            ));
            throw  $e ;
        }

        $object = 'abc';
        $filePath = '/usr/local/IMG_1269.jpg';
        //上传照片到OSS
        $ossClient = new OssClient(Config::get('ACCESS_KEY_ID'), Config::get('ACCESS_KEY_SECRET'),Config::get('END_POINT'));
        $res = $ossClient->uploadFile(Config::get('BUCKET'),$object,$filePath);
        var_dump($res);die;
//        $res = $ossClient->putObject(Config::get('BUCKET') , $file ,'aaa');
//        $ossClient->uploadFile();
//        var_dump($res);die;

//        $ossLogic = new OssLogic() ;
//        $res = $ossLogic->uploadFile($file['tmp_name'], $file);
//        var_dump($res);die;
//        var_dump($file['name']);die;
//        $ossLogic->uploadFile();

        //删除数据库中原有的图片， 上传新的图片
//        $head_pic_url =  M('users')->where('user_id' , $user_id)->getField('head_pic');
//        if($head_pic_url != ""){
//
//            @unlink(substr($head_pic_url, 1)) ;
//        }

//        $res =   M('users')->where('user_id' , $user_id)->save(['head_pic' => $path]) ;
//        if($res){
//            $path = addslashes($path);
//            //图片上传成功
//            $e = new  ParameterException(array(
//                'msg' => '图片上传成功' ,
//                'errorCode' => '0',
//                'datas' =>  BASE_PATH  . $path  ,
//            ));
//            throw  $e ;
//        }else{
//            //图片上传失败
//            $e = new  ParameterException(array(
//                'msg' => '图片上传失败' ,
//                'errorCode' => '391010',
//            ));
//            throw  $e ;
//        }
    }

}
