<?php

namespace   app\api\validate ;

class   UserLoginValidate  extends  BaseValidate{
    protected  $rule = [
        'mobile' =>'require|isMobile' ,
        'password' =>'require|isNotEmpty'
    ];

}

