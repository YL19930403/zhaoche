<?php

namespace    app\api\model  ;

use think\Model;

class  Ad extends  Model {
    protected   $table = 'tp_ad' ;


    /*
     *   根据广告位参数（排序参数）  获取对应的广告
     *   @param   $orderby   int   :  广告位（100， 101， 102）
     *   @return   Array
     * */
    public  function  getAdList($orderby){
        //获取当前的时间戳
        $currtimes = time() ;

        $sql =   ' select a.ad_id , CONCAT("'.BASE_PATH.'" , a.ad_code )  ad_code  from  tp_ad a  '
                . '  where a.start_time < '.$currtimes.' and  a.end_time > '.$currtimes.' and a.enabled = 1 and a.orderby = '.$orderby.'  ' ;
        $data =   $this->query($sql) ;
        return  $data ;
    }

}







