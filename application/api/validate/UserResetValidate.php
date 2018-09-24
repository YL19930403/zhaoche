<?php

namespace   app\api\validate ;

class  UserResetValidate extends  BaseValidate {
    protected  $rule = [
        'mobile' =>'require|isMobile' ,
        'newpassword' =>'require|isNotEmpty',
        'vcode'     =>  'require|isNotEmpty'
    ];
}