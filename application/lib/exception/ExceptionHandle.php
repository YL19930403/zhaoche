<?php

namespace   app\lib\exception ;

use think\Log;
use think\Request;
use think\exception\Handle;


class  ExceptionHandle  extends  Handle{
    private $code ;
    private $msg ;
    private  $errorCode ;
    private  $datas ;

    public  function  render(\Exception $e ){

        if($e instanceof  BaseException){
            $this->code = $e->code ;
            $this->msg  = $e->msg ;
            $this->errorCode = $e->errorCode ;
            $this->datas = $e->datas ;
        }else{
//            读取配置信息
//            Config::get('app_debug');
            if(config('app_debug')){
                return  parent::render($e);
            }

//            $this->code = 500 ;
            $this->msg = '服务器内部错误' ;
            $this->errorCode = 999 ;
//            日志记录
            $this->recordErrorLog($e);

        }

//        $request = Request::instance() ;

        $result = [
            'msg'   =>  $this->msg ,
            'error_code' => $this->errorCode ,
//            'request_url'  => $request->url() ,
            'datas'  =>   $this->datas ,
        ];
        return  json($result, $this->code );
    }

    private  function  recordErrorLog(\Exception $e ){
//        我们在config文件里里面把日志功能关闭了， 所以在这里需要初始化日志
        Log::init([
            'type' => 'File' ,
            'path' => MY_LOG_PATH ,
            'level' => ['error'] ,
        ]);

        Log::record($e->getMessage(), 'error');
    }
}
