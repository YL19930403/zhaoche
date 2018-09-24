<?php
namespace app\api\controller\v2 ;

use  app\api\controller\v1\Base ;
use  app\lib\exception\ParameterException ;

class  Article  extends  Base {

   public  function getlist($page = 0 , $per_page = 10){
            $aModel =   model('Article');
            $searchKey =    model('SearchKey') ;
            $searchKey->setStartNum($page, $per_page) ;
            $data =  $aModel->getArticleList($searchKey);
            if(!empty($data)){
                    foreach ($data as $k=>$v ){
//                           $data[$k]['content']  =  strip_tags($v['content']) ;
//                           $data[$k]['content']  = strip_tags( htmlspecialchars_decode($v['content']) );
                        //1.将  $lt  等标签转化为htmp标签
                        $v['content']  =  htmlspecialchars_decode($v['content']) ;

                        //2. 对html标签截取图片，并以图片数组形式返回
                        preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i',$v['content'],$match);
                        $imgsArr =  html2imgs($match[0]);
                        if(!empty($imgsArr)){
                              $imgs = [] ;
                              foreach ($imgsArr as $k2=>$v2 ){
                                    $imgs[$k2] =  BASE_PATH .  $v2['src'] ;
                              }
                              $data[$k]['img'] = $imgs ;
                        }else{
                            $data[$k]['img'] = [] ;
                        }
                        //3. 对html标签过滤
                         $data[$k]['content']  =  strip_tags($v['content']) ;
                    }
            }
//            var_dump($data) ;die ;
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => $data
            ));
            throw  $e ;
    }
}