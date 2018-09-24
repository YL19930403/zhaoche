<?php

namespace app\api\controller\v2 ;

use  app\lib\exception\ParameterException ;
use  app\api\controller\v1\Base ;

class  SearchWord  extends   Base {

    /*
     * 热门搜索
     * */
    public  function  gethotsearch(){
          $swModel = model('SearchWord');
          $data =   $swModel->getSearchList();
         $e = new  ParameterException(array(
            'msg' => 'success' ,
            'errorCode' => '0',
             'datas' => $data ,
        ));
        throw  $e ;
    }

}

