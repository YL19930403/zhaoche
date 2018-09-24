<?php

namespace   app\api\validate ;


class  UserRegistValidate  extends  BaseValidate{
    protected  $rule = [
        'nickname' =>'require|isNotEmpty' ,
        'mobile' =>'require|isMobile' ,
        'password' =>'require|isNotEmpty' ,
        'recond_mobile' =>'require|isMobile' ,
        'id_card' =>'require|isIdentify' ,
    ];
}

