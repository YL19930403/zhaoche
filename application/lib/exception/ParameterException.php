<?php

namespace   app\lib\exception ;

class  ParameterException  extends  BaseException {

    public  $code = 200  ;
    public  $msg = 'invalid parameters' ;
    public  $errorCode = 10000 ;

    public  $datas = null  ;

public function __construct(array $params = [])
{

    parent::__construct($params);
}

}

