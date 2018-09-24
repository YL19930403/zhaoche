<?php

namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;

class  News extends  Base {

     public  function  getnews($page = 0 , $per_page = 10 ){

         $pageInter =   isPageInteger($page) ;
         $perpageInter = isPageInteger($per_page) ;

         if( !( $pageInter  &&  $perpageInter )){
             $e = new  ParameterException(array(
                 'msg' => '分页参数必须为整数' ,
                 'errorCode' => '391022',
             ));
             throw  $e ;
         }

         $searchKey =  model('SearchKey');
         $searchKey->setStartNum($page, $per_page) ;

         $nModel =   model('News');
         $data =  $nModel->getNewsList($searchKey);

         if(!empty($data)){
             foreach ($data  as  $k=>$v ){
                      // $data[$k]['message'] =   strip_tags($v['message']) ;
                    $data[$k]['message'] =  strip_tags(str_replace('&nbsp;','',$v['message'])) ;
                     preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i',$v['message'],$match);
                 $data[$k]['img'] =  $match[1] ;
                 if(strlen($match[1]) == "0"){
                     $data[$k]['img'] =  "" ;
                 }else{
                     $data[$k]['img'] =  BASE_PATH .  substr($match[1], 0,67 );
                 }
             }
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'  =>  $data
             ));
             throw  $e ;

         }else{
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas'   =>  null ,
             ));
             throw  $e ;
         }


     }


      //  查询消息详情
    public  function  newsdetail($news_id){
         $is_nid_Inter = isAppPositiveInteger($news_id) ;
        if (!$is_nid_Inter) {
            $e = new  ParameterException(array(
                'msg' => '消息编码必须为正整数',
                'errorCode' => '391023',
            ));
            throw  $e;
        }
        $nModel =  model('News');
        $ndata =  $nModel->getNewsInfoBy($news_id) ;
        $this->assign('ndata',$ndata) ;
         return   $this->fetch();
    }

}























