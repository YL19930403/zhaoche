<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;

class  Category  extends  Model{




    /*
     *  获取所有分类的数据
     *  @param   $searchKey   obj   :    分页对象
     *  @param   $inst_id   int  :  机构编码
     *  return   Array
     * */
    public  function  getAllCatList($searchKey , $inst_id){
         // $page = $page * $per_page ;
         $totalData = [] ;
         $catData  =    Db::table('tp_goods_category')
                        ->where('parent_id' , 0)
                        ->where('is_delete',1)
                        ->field('id, name  , CONCAT("'.BASE_PATH.'" , image )  logo ')
                        ->select() ;


        if(!empty($catData)){
            $totalData['list'] = $catData ;

              $gModel =  model('Good');
           
            // $goodData =    Db::table('tp_goods')
            //             ->alias('g')
            //             ->where('g.is_on_sale', 1)
            //             ->where('g.is_delete',1)
            //             ->field('g.goods_id ,g.goods_name , g.mission, g.shop_price, g.goods_remark ,CONCAT( "'.BASE_PATH.'" , g.original_img) original_img  ')
            //             ->order('g.sort  desc')
            //             ->limit($page , $per_page)
            //             ->select();

             $sql =  ' select   g.goods_id ,g.goods_name ,  t.number  mission, g.shop_price, g.goods_remark ,CONCAT( "'.BASE_PATH.'" , g.original_img) original_img  from tp_goods g  '
                    . ' left join  tp_task t on t.goods_id = g.goods_id '
                    .  ' where  g.is_on_sale = 1 and g.is_delete    '  ;

            if($inst_id  == "1"){
                $sql  .=  ' and g.inst_id = 1 ' ;
            }

            $sql .=  '  ORDER  BY g.sort desc  '
                    . '  limit '  . $searchKey->startNum  . ','  . $searchKey->perPage  ;

            $goodData =  $gModel->query($sql) ;

         if(!empty($goodData)){
             $totalData['goodlist'] = $goodData ;
         }else{
             $totalData['goodlist'] = [] ;
         }
        }
        return  $totalData ;
    }


      
          /*
           *  根据分类编码获取该分类下面的所有商品
           *  @param   $cat_id   int  :  分类编码
           *  @param   $searchKey   obj  :    分页对象
           *  @param   $inst_id   int  :    机构编码
           *  return   Array
          * */
        public  function  getCatListBy($cat_id ,$searchKey, $inst_id ){
          //     $page = $page * $per_page ;
          // return  Db::table('tp_goods')
          //                         ->alias('g')
          //                         ->where('g.is_on_sale', 1)
          //                         ->where('g.is_delete',1)
          //                         ->where('g.cat_id', $cat_id)
          //                         ->field('g.goods_id ,g.goods_name , g.mission, g.shop_price, g.goods_remark ,CONCAT( "'.BASE_PATH.'" , g.original_img) original_img  ')
          //                         ->order('g.sort desc')
          //                         ->limit($page , $per_page)
          //                         ->select();

          
         $gModel =  model('Good');

         $sql    =  ' select  g.goods_id ,g.goods_name , t.number  mission, g.shop_price, g.goods_remark ,g.original_img ,g.is_icon1_show,g.is_icon2_show    from  tp_goods g   '
                     . ' left join  tp_task t on t.goods_id = g.goods_id '
                    .  '  where  g.is_on_sale = 1 and  g.is_delete = 1  '   ;    //and  g.cat_id ='.$cat_id.'

          if($inst_id == "1"){
                $sql .=  ' and g.inst_id = 1 '  ;
          }else{
                $sql .=  ' and  ( g.inst_id = '.$inst_id.'  or   g.inst_id = 1 )' ;
          }

          if($cat_id != "100"){
                $sql .= ' and  g.extend_cat_id = '.$cat_id.' '  ;
          }

          $sql  .=  'ORDER  BY g.sort  desc '
                .  ' limit '  . $searchKey->startNum . ','  . $searchKey->perPage ;

          $data = $gModel->query($sql) ;
          return  $data ;
   }

}



















