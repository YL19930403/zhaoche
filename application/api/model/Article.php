<?php

namespace    app\api\model  ;

use think\Model;

class  Article  extends  Model{

      public function   getArticleList($searchKey){
            $sql =      ' SELECT  a.article_id ,a.title , a.content , FROM_UNIXTIME( a.add_time , "%m-%d")  add_time   FROM tp_article a '
                        . ' limit  ' . $searchKey->startNum  . ',' . $searchKey->perPage ;
            $data = $this->query($sql) ;
            return $data ;
      }
}
