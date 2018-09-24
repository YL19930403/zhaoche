<?php

namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;

class  GoodVisit  extends  Base {

//    浏览历史
    public  function  getlooklist($user_id, $page=0, $per_page=10){
        $pageInter =   isPageInteger($page) ;
        $perpageInter = isPageInteger($per_page) ;

        if( !( $pageInter  &&  $perpageInter )){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }

        $is_inter =   isAppPositiveInteger($user_id) ;
        if(!$is_inter){
            $e = new  ParameterException(array(
                'msg' => '用户编码必须为正整数' ,
                'errorCode' => '391023',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

        $searchKey =   model('SearchKey');
        $searchKey->setStartNum($page, $per_page) ;

        $gvModel =  model('GoodVisit');
        $data =  $gvModel->getLookListyBy($user_id,$searchKey) ;
        $e = new  ParameterException(array(
            'msg' => 'success' ,
            'errorCode' => '0',
            'datas'  =>  $data  ,
        ));
        throw  $e ;
    }

}