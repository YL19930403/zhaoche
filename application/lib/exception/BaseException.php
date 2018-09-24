<?php

namespace  app\lib\exception ;

use think\Exception;

class   BaseException extends  Exception {
    //    HTTP状态码
    public  $code = 200  ;
//    错误基本信息
    public  $msg = '参数错误';
//    自定义错误码
    public  $errorCode = 10000 ;

    public  $datas = null ;



    public  function  __construct($params = [])
    {
//        parent::__construct($params);
        if(!is_array($params)){
            return ;
//            throw  new  Exception('参数必须是数组');
        }

        if (array_key_exists('code', $params)){
            $this->code = $params['code'];
        }

        if (array_key_exists('msg', $params)){
            $this->msg = $params['msg'];
        }


        if (array_key_exists('errorCode', $params)){
            $this->errorCode = $params['errorCode'];
        }

        if (array_key_exists('datas', $params)){
            $this->datas =   $params['datas'] ;
        }

    }


}

