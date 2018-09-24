<?php

namespace   app\api\validate ;


class   IDMustBeInteger  extends   BaseValidate {
    protected  $rule = [
        'user_id' =>'require|isPositiveInteger' ,
    ];
}

