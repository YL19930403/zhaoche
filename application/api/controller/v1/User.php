<?php


namespace app\api\controller\v1 ;

use app\api\validate\IDMustBeInteger;
use app\api\validate\UserLoginValidate;
use app\api\validate\UserRegistValidate;
use app\api\validate\UserResetValidate;
use  app\lib\exception\ParameterException ;


class User  extends   Base {

//    用户注册接口
    public  function  regist(){
        if(request()->isPost()){
             //根据   推荐人号码  找到推荐人uid
//            var_dump($this->post['recond_mobile']) ; die ;

//          (new UserRegistValidate() )->goCheck() ;

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
            }
            
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

            if(config('app_debug') == false  ){
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
            if(config('app_debug') == false  ){
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



          //执行 用户注册的逻辑
            $map['password'] =   encrypt(request()->post('password')) ;
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


    //提交用户资料
    public  function  submitMaterial($user_id){


         $is_uid_Inter =  isAppPositiveInteger($user_id) ;
         if(!$is_uid_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $files  =  request()->file() ;
        // var_dump($files) ; die ;
         if($files == NULL ){
            $e = new  ParameterException(array(
                'msg' => '缺少必填参数' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files['credit'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传银行卡资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files['identity'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传身份证资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files['license'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传驾驶证资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files['bankflow'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传银行流水资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files['ownership'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传房产证资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        

         if($files['commencial'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传商业保险单资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

         if($files['personal_credit'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传个人征信资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        if($files['fund'] == NULL ){
             $e = new  ParameterException(array(
                'msg' => '请上传个人基金资料' ,
                'errorCode' => '391016',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $save_url = 'public/upload/usergoods/' . date('Y', time()) . '/' . date('m-d', time());
        foreach($files as $k=>$file ){
             $info = $file->rule('uniqid')->validate(['size' => 1024 * 1024 * 50, 'ext' => 'jpg,png,gif,jpeg'])->move($save_url);
             if($info){
                $comment_img[$k] = '/'.$save_url . '/' . $info->getFilename();
             }else{
                $e = new  ParameterException(array(
                            'msg' => '图片上传失败' ,
                            'errorCode' => '391010',
                            'datas'  =>  null   ,
                        ));
                        throw  $e ;
             }
        }
       // var_dump($comment_img) ;die ;

        // var_dump($comment_img); die ;
        //根据User_id 判断该用户是否已经存在（提交过资料），如果提交过则更新数据，如果没有则插入数据
        $udata =  M('user_material')->where('user_id',$user_id )->find() ;
        $comment_img['user_id'] = $user_id ;
             
        if(empty($udata)){  
          $comment_img['add_time'] = time() ;
          $res = M('user_material')->insert($comment_img);
                   
        }else{
            //删除之前的图片
             $this->freeImage($udata) ;
             $res = M('user_material')->where('user_id',$user_id)->save($comment_img);
        }

         if($res){
                    $e = new  ParameterException(array(
                        'msg' => '资料提交成功' ,
                        'errorCode' => '0',
                        'datas'  =>  null   ,
                    ));
                    throw  $e ;
                }else{
                    $e = new  ParameterException(array(
                        'msg' => '资料提交失败' ,
                        'errorCode' => '391026',
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
         if($ugData['fund'] != ""){
            $ugData['fund'] = substr($ugData['fund'], 1) ;
            @unlink($ugData['fund']) ;
        }
         if($ugData['personal_credit'] != ""){
            $ugData['personal_credit'] = substr($ugData['personal_credit'], 1) ;
            @unlink($ugData['personal_credit']) ;
        }


    }

    //    用户协议接口
    public   function  agreements(){

       $contents =   M('agreement')->where(array('is_delete' => 1))->field('id, content, title')->find() ;
        if(!empty($contents)){
            if(isset($contents['content'])){
                 $content = $contents['content'] ;
                 $content =   htmlspecialchars_decode($content) ;
                 $this->assign('content' ,$content) ;
            }
        }else{

        }
        return  $this->fetch() ;
    }



    //    关于我们
    public  function  aboutus(){

        return   $this->fetch() ;
    }


    //    更新用户地区
    public  function  updateRegion($user_id){
        $is_Inter =   isAppPositiveInteger($user_id) ;

        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
            ));
            throw  $e ;
        }
        $data = request()->put() ;
        if(empty($data)){
            $e = new  ParameterException(array(
                'msg' => '地区参数不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }

        if(!isset($data['province'])){
            $e = new  ParameterException(array(
                'msg' => '省不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }

        $is_p_Inter =   isAppPositiveInteger($data['province'])  ;
        $is_c_Inter =   isAppPositiveInteger($data['city'])  ;
        $is_d_Inter =   isAppPositiveInteger($data['district'])  ;

        if(  ( !$is_p_Inter )    ||  (!$is_c_Inter)  || (!$is_d_Inter) ){
            $e = new  ParameterException(array(
                'msg' => '地区参数必须为正整数' ,
                'errorCode' => '391044',
            ));
            throw  $e ;
        }

        if(!isset($data['city'])){
            $e = new  ParameterException(array(
                'msg' => '市不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }

        if(!isset($data['district'])){
            $e = new  ParameterException(array(
                'msg' => '区不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }


        //更新数据
          $res =        M('users')->where(array('user_id' => $user_id))->save([
                             'province'  =>  $data['province'] ,
                             'city'  =>  $data['city'] ,
                             'district'  =>  $data['district'] ,
                         ]) ;
            if($res){
                $e = new  ParameterException(array(
                    'msg' => '更新成功' ,
                    'errorCode' => '0',
                ));
                throw  $e ;
            }else{
                $e = new  ParameterException(array(
                    'msg' => '更新失败' ,
                    'errorCode' => '391043',
                ));
                throw  $e ;
            }

    }


//    用户登录接口
    public   function  login(){
        
       $uModel = model('User');
        $token =  request()->header('token') ;
        $is_token =    isAppNotEmpty($token) ;
        if($is_token){
            // token登录
          
             $m_data =  $this->validateToken($token , time()) ;

             $user_id =  $m_data[1] ;

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
            $data = $uModel->identiMobilePass($postdata);

            if ($data != NULL) {
                //生成token
                if (config('app_debug') == false          ) {
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


//    用户登录接口
    public   function  login2(){

//        (  new  UserLoginValidate() )->goCheck() ;


        $uModel = model('User');
        $token =  request()->header('token') ;
        $is_token =    isAppNotEmpty($token) ;


        $postdata =   request()->post() ;

         $isMobile =  isAppMobile($postdata['mobile']) ;
         if($isMobile ==  false ){
             $e = new  ParameterException(array(
                 'msg' => '手机格式不正确' ,
                 'errorCode' => '391014',
             ));
             throw  $e ;
         }


         if(!isAppNotEmpty($postdata['password'])){
             $e = new  ParameterException(array(
                 'msg' => '密码不能为空' ,
                 'errorCode' => '391014',
             ));
             throw  $e ;
         }


        //根据 手机号码  ， 密码  去查询数据
         $data =  $uModel->identiMobilePass($postdata) ;


        if($data != NULL ){
            //生成token
           // $token =   $this->generateToken($data['user_id']) ;

           // $data['token'] = $token ;
            $e = new  ParameterException(array(
                'msg' => '登录成功' ,
                'errorCode' => '0',
                'datas'  =>  $data
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '手机号或密码错误' ,
                'errorCode' => '391007',
                'datas'  =>  $data
            ));
            throw  $e ;
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




        $password =  encrypt($postdata['newpassword']) ;

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

//     上传图像
    public  function uploadimg(){
        $user_id =   request()->get('user_id') ;

        //(new  IDMustBeInteger())->goCheck() ;

            $file  =   request()->file('image') ;
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

            $dateArr =   sub_year_month_day();
            $yearpath = "public/upload/head_pic/" . $dateArr[0] ;

            //判断文件夹是否存在  ( 2017 )

            if(!is_dir($yearpath)){
                mkdir($yearpath);
            }

            $info =  $file->move($yearpath) ;
            $logo =  str_replace('\\', '/',$info->getSaveName()) ;
            $path =   '/' . $yearpath . '/' . $logo ;



            //删除数据库中原有的图片， 上传新的图片
            $head_pic_url =  M('users')->where('user_id' , $user_id)->getField('head_pic');
            if($head_pic_url != ""){

                @unlink(substr($head_pic_url, 1)) ;
            }


            $res =   M('users')->where('user_id' , $user_id)->save(['head_pic' => $path]) ;

            if($res){
                //图片上传成功
                $e = new  ParameterException(array(
                    'msg' => '图片上传成功' ,
                    'errorCode' => '0',
                    'datas' =>  BASE_PATH  . $path  ,
                ));
                throw  $e ;
            }else{
                //图片上传失败
                $e = new  ParameterException(array(
                    'msg' => '图片上传失败' ,
                    'errorCode' => '391010',
                ));
                throw  $e ;
            }
      }


//      查询我的下级用户
    public  function  getSubordinates($user_id){

        $is_Inter =   isAppPositiveInteger($user_id) ;
        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
            ));
            throw  $e ;
        }

        //根据用户编码查询该用户的下级用户
         $uModel  =   model('User');
         $udata =  $uModel->getSubordinators($user_id) ;
         if(!empty($udata)){
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'    =>  $udata ,
             ));
             throw  $e ;
         }else{
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'    =>  null  ,
             ));
             throw  $e ;
         }
    }

//      获取用户信息
    public  function  getuserinfo($user_id){

        $is_Inter =   isAppPositiveInteger($user_id) ;

        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
            ));
            throw  $e ;
        }

         $uModel =   model('User');
         $udata =   $uModel->getUserInfoBy($user_id);
         if(!empty($udata)){
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'      => $udata ,
             ));
             throw  $e ;
         }else{
             $e = new  ParameterException(array(
                 'msg' => '该用户不存在' ,
                 'errorCode' => '391011',
                 'datas'      => null ,
             ));
             throw  $e ;
         }
    }



    //    推荐注册
    public  function recomregist(){

        $postdata = request()->post() ;
        if(!isset($postdata['nickname'])  ||  !isset($postdata['mobile'])   || !isset($postdata['password'])  ||  !isset($postdata['id_card'])  ){
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

        if(!isAppMobile($postdata['mobile'])){
            $e = new  ParameterException(array(
                'msg' => '手机号码格式不正确' ,
                'errorCode' => '391014',
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

        if(!isAppNotEmpty($postdata['nickname'])){
            $e = new  ParameterException(array(
                'msg' => '用户名不能为空' ,
                'errorCode' => '391017',
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

        //根据token  取出当前用户的编码  $user_id
        $user_id = $this->jsondata[1] ;

//        $udata =  M('users')->where('mobile' , $postdata['recond_mobile'])->field('user_id')->find() ;
        $uModel =   model('User') ;
        $udata = $uModel->getUserValueBy($user_id , 'mobile');
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

        //获取第三方推荐注册 传参
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
            // $map['inst_id'] = 2 ;
        }


        $inst_id=   $uModel->getUserValueBy($user_id , 'inst_id');

        //执行 用户注册的逻辑
        $map['password'] =   encrypt(request()->post('password')) ;
        $map['nickname'] = $postdata['nickname'];
        $map['uid'] = $user_id ;
        $map['mobile'] = $postdata['mobile'];
        $map['reg_time'] = time() ;
        $map['id_card'] = $postdata['id_card'] ;
        $map['inst_id'] = $inst_id ;
        $map['token'] = md5(time().mt_rand(1,999999999));

//        var_dump($map) ;die ;
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
    

//    更新用户信息
    public  function  updateSex($user_id){
        $is_Inter =   isAppPositiveInteger($user_id) ;

        if(!$is_Inter){
            $e = new  ParameterException(array(
                'msg' => '参数必须为正整数' ,
                'errorCode' => '391023',
            ));
            throw  $e ;
        }

        $data = request()->put() ;
//        var_dump($data) ;die ;

        if(empty($data)){
            $e = new  ParameterException(array(
                'msg' => '参数不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }


        if(!isset($data['sex'])){
            $e = new  ParameterException(array(
                'msg' => '性别不能为空' ,
                'errorCode' => '391016',
            ));
            throw  $e ;
        }

        $udata =  M('users')->where('user_id' , $user_id)->field('sex')->find() ;
        if($udata == NULL ){
            $e = new  ParameterException(array(
                'msg' => '	该用户不存在' ,
                'errorCode' => '391011',
            ));
            throw  $e ;
        }else{
            if($udata['sex'] == $data['sex']){
                $e = new  ParameterException(array(
                    'msg' => 'success' ,
                    'errorCode' => '0',
                ));
                throw  $e ;
            }
        }

        $res =   M('users')->where('user_id' , $user_id)->save(['sex' => $data['sex']]) ;

        if($res){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => '性别更改失败' ,
                'errorCode' => '391027',
            ));
            throw  $e ;
        }

    }

//    退出登录接口
    public  function  quit($user_id){

            $is_Inter =  isAppPositiveInteger($user_id) ;
            if(!$is_Inter){
                $e = new  ParameterException(array(
                    'msg' => '参数必须为正整数' ,
                    'errorCode' => '391023',
                ));
                throw  $e ;
        }

/*
            $token =  request()->header('token') ;

            $res =  $this->memcache_obj->delete($token) ;

            if($res ){
                $e = new  ParameterException(array(
                    'msg' => '退出成功' ,
                    'errorCode' => '0',
                ));
                throw  $e ;
            }else{
                $e = new  ParameterException(array(
                    'msg' => '退出登录失败' ,
                    'errorCode' => '391037',
                ));
                throw  $e ;
            }
            */

             $e = new  ParameterException(array(
                    'msg' => '退出成功' ,
                    'errorCode' => '0',
                ));
                throw  $e ;
    }

}














