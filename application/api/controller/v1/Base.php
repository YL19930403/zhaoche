<?php


namespace app\api\controller\v1 ;

use  aiche\AESMcrypt  ;
use  app\common\memcache ;
use  think\Controller;
use  app\lib\exception\ParameterException ;


class  Base  extends  Controller {

     public    $post= null ;
     public    $jsondata = null ;

     protected   $aes = null ;
      //生成验证码的位数
     protected   $length = 8 ;
     protected  $memcache_obj = null ;

      //设置过期时间
     protected $expires_time =  604800   ;


      public   function  _initialize()
        {
            parent::_initialize();
            	
           if(config('app_debug') == false     ) {    

                $this->memcache_obj = memcache\Memcache::singleton() ;
                if($this->aes == null ){
                    $this->aes =  new  AESMcrypt($bit=128, $key='abcdef1234567890',$iv='0987654321fedcba',$mode='cbc') ;
                }
            }

            //1. 获取请求的数据
             $this->get_request_data() ;

            //2.添加黑名单
            if(config('app_debug') == false          ) {
                $this->add_black();
            }
        }


        public  function  get_request_data(){
            if(request()->isPost()){
                $postData = request()->getInput();
                $postData =  json_decode($postData ,true ) ;
                if(is_array($postData)){
                     $this->post = $postData ;
                }else{

                }

            }elseif (request()->isGet()){

            }
        }


        /*
    * 将传递进来的时间戳转为日期， 日期增加一个星期后再转为时间戳，返回时间戳
    * */
    public  function  timeTostamp($timestamp){

        //时间戳转日期
        $date =  date('Y-m-d H:i:s', $timestamp);
        //日期增加一个星期之后再转时间戳
        $date_out = date('Y-m-d H:i:s',strtotime("$date +7 day"));
        $time_out = strtotime($date_out);

        return $time_out ;
    }


    //生成固定长度的字符串
    public  function generate_char( $length = 16 ) {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $char = '';

        for ( $i = 0; $i < $length; $i++ )
        {
            // 取字符数组 $chars 的任意元素
            $char .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $char ;
    }


//    用户每次调用其他接口都会调用token的验证方法，每调用一次就把$time_out在当前时间上往后加7天，存入到memcache中，缓存的key就是token , 每次变化的只是value
    public  function  validateToken($token,$timestamp){
        
        if($token == null ){
            $e = new  ParameterException(array(
                'msg' => 'token为空' ,
                'errorCode' => '391033',
                'datas'  =>  null
            ));
            throw  $e ;
        }

        if(!is_string($token)){
            $e = new  ParameterException(array(
                'msg' => 'token类型错误' ,
                'errorCode' => '391034',
                'datas'  =>  null
            ));
            throw  $e ;
        }



        //先根据token获取到过期时间 ，判断是否过期,-----过期时间  跟   当前时间对比，如果过期时间小于当前时间，说明已经过期
        $m_data = json_decode( $this->memcache_obj->get($token),true );

        $time_out = $m_data[0] ;
        

        $user_id = $m_data[1] ;

        if(request()->get('user_id') != NULL ){

             //比对user_id   和  memcache取出来的user_id  , 如果不一致则表明不是同一个用户
             if($user_id != request()->get('user_id')){
                 $e = new  ParameterException(array(
                     'msg' => '用户编码不一致' ,
                     'errorCode' => '391038',
                     'datas'  =>  null
                 ));
                 throw  $e ;
             }
        }


        if(!$time_out){
            $e = new  ParameterException(array(
                'msg' => 'token已过期' ,
                'errorCode' => '391035',
                'datas'  =>  null
            ));
            throw  $e ;
        }

        if($time_out < (int)$timestamp){
            //由于memcache的惰性清空，所以我们可以在这里手动清空，让用户重新登录，重新写入token
            $this->memcache_obj->delete($token);
            $e = new  ParameterException(array(
                'msg' => 'token已经过期,请重新登录' ,
                'errorCode' => '391035',
                'datas'  =>  null
            ));
            throw  $e ;
        }

        $time_out = $this->timeTostamp($timestamp);
        $data =  json_encode(array( $time_out,$user_id), true );

        $this->memcache_obj->set($token,$data,0,$this->expires_time);
        $this->jsondata = $m_data ;
      	
         return  $m_data ;
    }

       /*
         *  在生成token的时候将用户编码和当前的时间戳写入memcache中
         *  @param   $user_id  int  :  用户编码
         * */
        public  function  generateToken($user_id){
            //拿到当期的时间戳
            $currentStamp = time() ;
            $time_out =   $this->timeTostamp($currentStamp) ;
            $msg =  $this->generate_char($this->length).$time_out ;
            // token  有三部分组成    1:对msg 编码后的字符串      2:seperator 分隔符（.）    3:对msg加密之后再编码的字符串
//            $token = base64_encode($msg).'.' .base64_encode($this->aes->encrypt($msg)) ;
           $token = $this->aes->encrypt($msg);

            $data = json_encode(array($time_out , $user_id) , true ) ;

            $this->memcache_obj->set($token, $data , 0,$this->expires_time );
            return $token ;
        }

        //        添加不要token 的黑名单、
        public   function  add_black(){
            //把登录 和 注册  拿出来， 其他的接口都要带上token
            $action =  request()->action() ;

            $is_black =  ( $action == "login" )  ||   ( $action == "regist" )  || ( $action == "sendCode" )   || ( $action == "getad")  ||   ( $action == "getlist")  || ( $action == "checkVersion" )   || ( $action == "aboutus")  ||  ($action == "agreements") ||  ($action == "reset") || ($action == "newsdetail");
            if($is_black){

            }else{
                $token  =  request()->header('token') ;
                //验证token是否为空
                if(!$token){
                    $e = new  ParameterException(array(
                        'msg' => 'token不能为空' ,
                        'errorCode' => '391033',
                        'datas'  =>  null
                    ));
                    throw  $e ;
                }
                $this->validateToken($token , time()) ;
            }
        }


}


























