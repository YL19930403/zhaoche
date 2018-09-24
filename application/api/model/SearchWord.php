<?php

namespace    app\api\model  ;

use think\Model;

class  SearchWord extends  Model {

    /*
     * 获取热门搜索数据：按照搜索次数排序
     * */
    public   function  getSearchList(){
         $sql =  ' select  id, keywords ,MAX(sw.search_num) as  max_count from  tp_search_word sw '
                . ' group by sw.search_num   order by max_count desc  limit 0, 9 ' ;
         $data =  $this->query($sql) ;
         return  $data ;
    }

}