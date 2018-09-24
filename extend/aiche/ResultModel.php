<?php

namespace   aiche ;

class  ResultModel {
    //代码
    public $code = 1;

    //消息
    public $msg = "success";

    //数据
    public $data ;



    public function resultData($data=null, $code=1, $msg="success"){
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }


}
