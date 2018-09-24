<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;

class   Good  extends  Model{

    protected   $table = 'tp_goods' ;

     /*
     *  获取热门接口数据
     *  @param   $searchKey    分页对象
     *  @param   $inst_id  :  机构编码
     * */
    public function getHotList($searchKey  ,$inst_id){
    
        //娄底代理商下面的用户可以查看我们的商品（搜索商品）， 而我们的用户不能查看
        //根据token取出用户编码

        $gModel = model('Good') ;
        $sql =   ' select g.goods_id ,g.goods_name , t.number  mission, g.shop_price, g.goods_remark , g.original_img ,g.is_icon1_show, g.is_icon2_show from tp_goods  g  '
                . ' left  join tp_task t on t.goods_id = g.goods_id '
                .  ' where g.is_hot = 1 and  g.is_delete = 1 and g.is_on_sale = 1 '  ;

        if($inst_id == "1"){
            $sql .= '  and   g.inst_id = 1 '  ;
        }
             $sql .=      ' ORDER BY g.sort  desc '
                          . ' limit '  . $searchKey->startNum  . ','  . $searchKey->perPage ;

        $data = $gModel->query($sql) ;
        return  $data ;
        // $page = $page * $per_page ;
        // return   Db::table('tp_goods')
        //             ->alias('g')
        //             ->where('g.is_hot' , 1)
        //             ->where('g.is_on_sale', 1)
        //             ->where('g.is_delete',1)
        //             ->field('g.goods_id ,g.goods_name , g.mission, g.shop_price, g.goods_remark , original_img')
        //             ->limit($page, $per_page)
        //             ->order('g.sort   desc')
        //             ->select() ;
    }


    /*
     *   根据商品编码去获取商品详情
     *   @param   $good_id    ： int   商品编码
     * */
    public  function  getGoodsDetail($good_id){

         $sql = ' select  g.goods_id, g.goods_sn, g.goods_name, g.click_count, g.market_price, g.shop_price,  t.number  mission ,g.cat_id,g.extend_cat_id ,
                   g.original_img ,g.store_count, g.comment_count, g.goods_remark, g.goods_content, g.commission , tp.type_name , g.is_icon1_show, g.is_icon2_show , g.is_ratio_show ,
                   g.sales_sum , b.is_identity, b.is_license, b.is_credit,b.is_security , b.is_bankflow , b.is_ownership ,b.is_commencial     from  tp_goods g '
                . ' left  join  tp_goods_certificate b on  b.goods_id = g.goods_id '
                . ' left join tp_task t on t.goods_id = g.goods_id '
                . ' left join tp_type tp on g.type_id = tp.type_id '
                . ' where g.goods_id = '.$good_id.' and g.is_delete = 1  '  ;
                
        $data =  $this->query($sql) ;
        return  $data[0] ;
    }


    /*
     * 版本2 的商品详情 ：  去掉 original_img
     * */
    public  function   getv2GoodsDetail($good_id){
        $sql = ' select  g.goods_id, g.goods_sn, g.goods_name, g.click_count, g.market_price, g.shop_price,  t.number  mission ,g.cat_id,g.extend_cat_id ,
                   g.store_count, g.comment_count, g.goods_remark, g.goods_content, g.commission , tp.type_name , g.is_icon1_show, g.is_icon2_show ,g.is_ratio_show,
                   g.sales_sum , b.is_identity, b.is_license, b.is_credit,b.is_security , b.is_bankflow , b.is_ownership ,b.is_commencial     from  tp_goods g '
            . ' left  join  tp_goods_certificate b on  b.goods_id = g.goods_id '
            . ' left join tp_task t on t.goods_id = g.goods_id '
            . ' left join tp_type tp on g.type_id = tp.type_id '
            . ' where g.goods_id = '.$good_id.' and g.is_delete = 1  ' ;

        $data =  $this->query($sql) ;
        return  $data[0] ;
    }



     /*
    *  获取精品推荐列表数据
    *  @param   $searchKey   obj   :   分页对象
    *  $param   $inst_id    int   :  机构编码
    * */
   public  function getRecomList($searchKey, $inst_id){
        
       $gModel =  model('Good');
      
       $sql =    ' SELECT g.goods_id ,g.goods_name , t.number  mission  , g.shop_price, g.goods_remark , CONCAT("'.BASE_PATH.'" , g.original_img ) original_img    FROM  tp_goods g  '
                . '  left  join  tp_task t  on t.goods_id = g.goods_id '
                . ' where g.is_recommend = 1 and g.is_on_sale = 1 and g.is_delete = 1  ' ;

       if($inst_id == "1"){    //爱车送用户
            $sql .=   ' and g.inst_id = 1  '  ;
       }
        $sql .=  ' ORDER BY g.sort  desc '
                . ' limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;

        $data =  $gModel->query($sql);
        return  $data ;

       //          $page = $page * $per_page ;
//          $gData =   Db::table('tp_goods')
//                            ->alias('g')
//                            ->where('g.is_recommend', 1)
//                            ->where('g.is_on_sale', 1)
//                            ->where('g.is_delete',1)
//                            ->field('g.goods_id ,g.goods_name , g.mission, g.shop_price, g.goods_remark , CONCAT("'.BASE_PATH.'" , g.original_img ) original_img')
//                            ->limit($page, $per_page)
//                            ->order('g.sort desc')
//                            ->select() ;


//          return   $gData ;
   }


    public  function getRecomDataList($searchKey , $inst_id){
      
        $totalData = [];
        // $gData =   Db::table('tp_goods')
        //     ->alias('g')
        //     ->where('g.is_recommend', 1)
        //     ->where('g.is_on_sale', 1)
        //     ->where('g.is_delete',1)
        //     ->field('g.goods_id ,g.goods_name , g.mission, g.shop_price, g.goods_remark , CONCAT("'.BASE_PATH.'" , g.original_img ) original_img')
        //     ->limit($page, $per_page)
        //     ->order('g.sort  desc')
        //     ->select() ;

        
        $gModel =  model('Good') ;
       
        $sql =  ' select  g.goods_id ,g.goods_name ,  t.number  mission, g.shop_price, g.goods_remark , CONCAT("'.BASE_PATH.'" , g.original_img ) original_img  from  tp_goods  g  '
                . ' left join tp_task t on t.goods_id = g.goods_id  '
                . ' where  g.is_recommend = 1 and  g.is_on_sale = 1  and g.is_delete = 1  ' ;

        if($inst_id == "1"){
            $sql .=  ' and  g.inst_id = 1 '  ;
        }

        $sql .=  ' ORDER  BY  g.sort  desc '
            . ' limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;

        $gData =   $gModel->query($sql) ;
        
        $totalData['list'] = [] ;
        if(!empty($gData)){
            $totalData['goodlist'] = $gData ;
        }else{
            $totalData['goodlist'] = [] ;
        }
        return   $totalData ;
    }



    /*
     *  根据价格区间参数查询商品数据
     *  @param    $inst_id    int   : 机构编码
     *  @param    $searchKey    obj  :  分页对象
     *  @param     $price     int   : 价格区间编码
     *  @return   Array
     * */
    public  function  getGoodsByPrice($inst_id ,$searchKey, $price ){

        $sql =   ' select   g.goods_id ,g.goods_name ,  t.number  mission, g.shop_price, g.goods_remark ,g.original_img,g.is_icon1_show,g.is_icon2_show  from   tp_goods  g '
                . ' left join tp_task t on t.goods_id = g.goods_id  '
                . '  where  g.is_on_sale = 1 and  g.is_delete = 1  ' ;

        if($inst_id == "1"){
                $sql .= ' and g.inst_id  = 1 ' ;
        }

       if($price == "1"){
            $sql .= ' and  g.shop_price < 10000.00 ' ;
       }elseif ($price == "2"){
            $sql .=  ' and  g.shop_price  between  10000.00  and  50000.00 '  ;
       }elseif ($price == "3"){
           $sql .=  ' and  g.shop_price  between  50000.00  and  100000.00 '  ;
       }elseif ($price == "4"){
           $sql .=  ' and  g.shop_price  between  100000.00  and  150000.00 '  ;
       }elseif ($price == "5"){
           $sql .=  ' and  g.shop_price  between  150000.00  and  200000.00 '  ;
       }elseif ($price == "6"){
           $sql .=  ' and  g.shop_price  > 200000.00 '  ;
       }elseif ($price == "100"){    //不限价格

       }

       $sql .=   ' limit '  . $searchKey->startNum  . ','  . $searchKey->perPage ;

       $data =   $this->query($sql) ;
       return  $data ;
    }

    /*
     *   根据关键字搜索匹配的商品
     *   @param     $searchKey   obj  :  分页对象
     *   @param     $key     string   ： 搜索条件
     *   @param     $inst_id     int    ： 机构编码
     * */
    public  function  searchGoodList($searchKey , $key , $inst_id){
       $gModel =   model('Good') ;
        // $sql =   ' select  g.goods_id , g.cat_id , g.brand_id ,  g.goods_name ,g.shop_price , b.name  brand_name  , cat.name  category_name  ,
        //                 t.number  mission, g.goods_remark , CONCAT("'.BASE_PATH.'",g.original_img)  original_img    from  tp_goods  g '
        //     . ' LEFT  JOIN  tp_goods_category cat on  cat.id = g.cat_id  '
        //     . ' LEFT  JOIN   tp_brand  b on b.id = g.brand_id  '
        //     . ' left  join tp_task t on t.goods_id = g.goods_id  '  ;

        // $sql .=    ' where g.goods_name like "%'.$key.'%"  ' ;

        // if($inst_id == "1"){
        //     $sql .=  'and  g.inst_id = 1' ;
        // }

        // $sql .=   ' or  b.name like "%'.$key.'%"  or  cat.name  like "%'.$key.'%"  ' ;

        // $sql .=     ' limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;
        // $data =  $gModel->query($sql) ;
        // return  $data ;



          $sql = ' select   g.goods_id , g.cat_id , g.brand_id ,  g.goods_name ,g.shop_price , 
                        t.number  mission, g.goods_remark , CONCAT("'.BASE_PATH.'",g.original_img)  original_img ,g.is_icon1_show,g.is_icon2_show   from  tp_goods  g  ' 
                    .  ' left  join tp_task t on t.goods_id = g.goods_id  '  ; 

        $sql .=   ' where  g.is_delete = 1 and  g.is_on_sale = 1 and  g.goods_name like "%'.$key.'%"  ' ;

        if($inst_id == "1"){
            $sql .=  'and    g.inst_id = 1' ;
        }else{
             $sql .=  ' and  ( g.inst_id = '.$inst_id.'  or   g.inst_id = 1 )' ;
        }

        
        $sql .=     ' limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;

        $data =  $gModel->query($sql) ;

        return  $data ;
    }



      /*
     * 根据年份条件查询所有满足的商品列表
     * @param   $year    int   :  年份
     * @param   $searchKey    obj   :  分页对象
     * @param   $inst_id   int  :  机构编码
     * @return    Array
     * */
    public  function  searchListBy($year , $searchKey, $inst_id ){
            $gModel  =   model('Good');

            $sql =  ' select   g.goods_id ,g.goods_name ,  t.number  mission, g.shop_price, g.goods_remark ,g.original_img ,g.is_icon1_show,g.is_icon2_show from  tp_goods  g  '
                    . ' left  join  tp_task t on t.goods_id = g.goods_id '
                    . ' where  g.is_on_sale = 1 and  g.is_delete = 1 '  ;

            if($year != "100"){    //不限年份
                    $sql .=  ' and FROM_UNIXTIME(g.add_time ,"%Y") = "'.$year.'" ' ;
            }

            if($inst_id == "1"){
                    $sql .= ' and  g.inst_id = 1 ' ;
            }else{
                   $sql .=  ' and  ( g.inst_id = '.$inst_id.'  or   g.inst_id = 1 )' ;
            }

         $sql .=  ' ORDER  BY g.sort  desc  '
                  . ' limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;

          $data =  $gModel->query($sql) ;
          return  $data ;
    }


   /*
     *   获取日期的分组数据
     *   @param     $inst_id   int   :机构编码  1:爱车送  2：娄底代理
     *   @return   Array
     * */
    public  function  getDateList($inst_id){

        $sql =   ' select   FROM_UNIXTIME(g.add_time , "%Y") year  , count(g.goods_id)  count   FROM  tp_goods g     '
                .  ' where  g.is_delete = 1  and  g.is_on_sale = 1 ' ;

        if($inst_id == "1"){
             $sql .=  ' and   g.inst_id = 1 '  ;
        }

        $sql .=  ' group by  year '  ;
        $dates =  $this->query($sql) ;
        
        return  $dates ;
    }



      /*
     *  获取所有商品列表 的商品编码  和 商品价格 字段
     *  @param    $inst_id   int  :  机构编码
     *  @return    Array
     * */
    public  function  getAllGoods($inst_id){
         $sql  =   ' select   g.goods_id, g.shop_price  from  tp_goods  g   '
                    . ' where  g.is_delete = 1 and  g.is_on_sale = 1 '  ;

         if($inst_id == "1"){
             $sql .=  ' and  g.inst_id = 1 '  ;
         }

        $data =   $this->query($sql);
        return  $data ;
    }

       /*
     * 查询所有商品列表数据
     * @param     $searchKey    obj  :   分页对象
     * @param     $inst_id      int   :  机构编码
     * return   Array
     * */
     public  function  getAllList($searchKey  , $inst_id){

          $sql =    ' select     g.goods_id ,g.goods_name ,  t.number  mission, g.shop_price, g.goods_remark  , g.original_img,g.is_icon1_show, g.is_icon2_show from  tp_goods  g  '
                     . ' left  join  tp_task t on t.goods_id = g.goods_id '
                    . ' where  g.is_on_sale = 1 and  g.is_delete = 1 ' ;


       if($inst_id == "1"){
            $sql .= ' and  g.inst_id = 1 ' ;
       }else{
             $sql .=  ' and  ( g.inst_id = '.$inst_id.'  or   g.inst_id = 1 )' ;
       }

        //    MySQL5.6   ORDER  BY 与Limit 搭配使用还有问题
            $sql .=  '    ORDER  BY   g.sort    desc    '       
                 . '   limit ' . $searchKey->startNum . ','  . $searchKey->perPage ;

         $data =  $this->query($sql) ;
         return   $data ;
     }

}


