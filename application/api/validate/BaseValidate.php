<?php

namespace   app\api\validate ;


use think\Validate;
use  think\Request ;
use  app\lib\exception\ParameterException ;

class  BaseValidate extends  Validate{

      public  function  goCheck(){

//              获取http传递的参数，对参数做校验
            $request = Request::instance() ;
            $params = $request->param() ;

            $result = $this->batch()->check($params) ;

            if(!$result){

//            抛出自定义异常
                $e = new  ParameterException() ;
                $e->msg = $this->error ;

                $e = new  ParameterException(array(
                    'msg' => $this->error ,
                ));
                throw  $e ;

//  抛出系统异常
//            $error = $this->error ;
//            throw  new  Exception($error) ;

//
//            $error = $this->error ;
//            throw  new  Exception($error) ;
            }else{

                return  true ;
            }
//
        }


     protected function isMobile($value, $rule='', $data='', $field=''){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^' ;

        $result = preg_match($rule, $value) ;
        if($result){
            return true  ;
        }else{
            return  false ;
        }
    }


     protected function isNotEmpty($value, $rule='', $data='', $field='')
      {
        if(empty($value)){
            return  $field . '不允许为空' ;
        }else{
            return  true ;
        }
    }

      protected function isPositiveInteger($value, $rule='', $data='', $field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return false ; // $field . '必须是正整数';
    }



    /*身份证号码检查接口函数  --- start  */
    protected   function  isIdentify_2($value, $rule='', $data='', $field=''){
        if(strlen($value) == 15 || strlen($value) == 18){
            if(strlen($value) == 15){
                $idcard = $this->idcard_15to18($value);
            }
            if($this->idcard_checksum18($value)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    protected  function idcard_15to18_2($idcard){
        if (strlen($idcard) != 15){
            return false;
        }else{// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
                $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 15);
            }else{
                $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 15);
            }
        }
        $idcard = $idcard . $this->idcard_verify_number($idcard);
        return $idcard;
    }


    function idcard_checksum18_2($idcard){
        if (strlen($idcard) != 18){ return false; }
        $idcard_base = substr($idcard, 0, 17);
        if ($this->idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){
            return false;
        }else{
            return true;
        }
    }


    function idcard_verify_number_2($idcard_base){
        if (strlen($idcard_base) != 17){
            return false;
        }
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); //debug 加权因子
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); //debug 校验码对应值
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++){
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }
    /*身份证号码检查接口函数  --- end */
}
