<?php


namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;

class  Category   extends  Base {


	 /*
     * 查询所有分类
     * @return   Array
     * */
    public   function  getAllCats(){

         $total = [] ;

          $cat_data =   M('goods_category')->where(array('parent_id' => 0 , 'level' => 1 ,'is_show' => 1, 'is_delete' => 1))
                            ->field('id, name , CONCAT("'.BASE_PATH.'" , image)  image  ')
                            ->select() ;

            if(!empty($cat_data)){
                $total['cats'] = $cat_data ;
            }else{
                $total['cats'] = [] ;
            }

        $brand_data  =   M('Brand')->where(array('is_delete' => 1))
            ->field('id, name, CONCAT("'.BASE_PATH.'" , logo) image  ')
            ->select() ;

        if(!empty($brand_data)){
            $total['brands'] = $brand_data ;
        }else{
            $total['brands'] = [] ;
        }





           if(!empty($total)){
               $e = new  ParameterException(array(
                   'msg' => 'success' ,
                   'errorCode' => '0',
                   'datas'  =>  $total ,
               ));
               throw  $e ;
           }else{
               $e = new  ParameterException(array(
                   'msg' => 'success' ,
                   'errorCode' => '0',
                   'datas'  =>  null  ,
               ));
               throw  $e ;
           }
    }

}
















