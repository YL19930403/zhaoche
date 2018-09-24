<?php


namespace   app\api\controller\v1 ;
use  app\lib\exception\ParameterException ;
use  aiche\HelperSpell ;
use  think\Db;

class  HomeList extends  Base {

    public  function  getHomeList($nav_id , $page = 0 , $per_page = 10){

            $is_Inter =   isAppPositiveInteger($nav_id) ;
            if(!$is_Inter){
                $e = new  ParameterException(array(
                    'msg' => '参数必须为正整数' ,
                    'errorCode' => '391023',
                    'datas'  =>  null  ,
                ));
                throw  $e ;
            }

            $is_page_Inter =   isPageInteger($page) ;
            $is_per_page_Inter =   isPageInteger($per_page) ;

            if(!$is_page_Inter  || !$is_per_page_Inter){
                $e = new  ParameterException(array(
                    'msg' => '分页参数必须为整数' ,
                    'errorCode' => '391022',
                ));
                throw  $e ;
            }


            $searchKey =   model('SearchKey');
            $searchKey->setStartNum($page, $per_page) ;
             $inst_id = request()->header('instid') ;

             if(isset($inst_id)) {
                $is_instid_Inter = isAppPositiveInteger($inst_id);
                if (!$is_instid_Inter) {
                    $e = new  ParameterException(array(
                        'msg' => '机构编码必须为正整数',
                        'errorCode' => '391023',
                    ));
                    throw  $e;
                }
            }else{
                $inst_id =  0 ;
            }


            
            if($nav_id == "2" ){    // 汽车整车
                //判断是否有传入  cat_id  参数
                 $cat_id = request()->get('cat_id') ;
                 $catModel =   model('Category');
                if($cat_id == NULL ){
                     $dataList =    $catModel->getAllCatList($searchKey, $inst_id) ;
                }else{
                      //cat_id  必须为正整数
                       $cat_inter  =  isAppPositiveInteger($cat_id) ;
                       if(!$cat_inter){
                           $e = new  ParameterException(array(
                               'msg' => '分类编码必须为正整数' ,
                               'errorCode' => '391023',
                               'datas'  =>  null  ,
                           ));
                           throw  $e ;
                       }
                      $dataList = $catModel->getCatListBy($cat_id ,$searchKey, $inst_id) ;
                }
            }elseif ($nav_id == "4"){
                //判断是否有传入  cat_id  参数
                $brand_id = request()->get('brand_id') ;
                $brandModel =   model('Brand');
                if($brand_id == NULL ){
                        $dataList = $brandModel->getAllBrandList($searchKey , $inst_id) ;
                }else{
                        $dataList = $brandModel->getAllListBy( $brand_id ,$searchKey , $inst_id) ;
                }
            }elseif ( $nav_id == "1"){
                        $gModel =   model('Good') ;
                        $dataList =  $gModel->getRecomDataList($searchKey , $inst_id) ;
            }

        if(!empty($dataList)){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  $dataList   ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  null   ,
            ));
            throw  $e ;
        }
    }


    /*
     *  搜索接口
     *  @param    $page    int   :   当前页
     *  @param    $per_page  int  :  每页显示的条目数
     * */
    public  function  searchList($page = 0 , $per_page = 10 , $key = ""){

        $is_page_Inter =   isPageInteger($page) ;
        $is_per_page_Inter =   isPageInteger($per_page) ;

        if(!$is_page_Inter  || !$is_per_page_Inter){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
            ));
            throw  $e ;
        }


         $inst_id = request()->header('instid') ;
        if(isset($inst_id)) {
            $is_instid_Inter = isAppPositiveInteger($inst_id);
            if (!$is_instid_Inter) {
                $e = new  ParameterException(array(
                    'msg' => '机构编码必须为正整数',
                    'errorCode' => '391023',
                ));
                throw  $e;
            }
        }else{
            $inst_id =  0 ;
        }

         
      if(trim($key) == ""){
          $e = new  ParameterException(array(
              'msg' => '搜索内容为空' ,
              'errorCode' => '391040',
          ));
          throw  $e ;
      }else{
              $key = trim($key);
              $gModel =   model('Good') ;
              $searchKey =   model('SearchKey');
              $searchKey->setStartNum($page, $per_page) ;
              $data =  $gModel->searchGoodList($searchKey , $key , $inst_id) ;
              if(!empty($data)){
                 //如果搜索结果不为空，那么将搜索关键字写入数据表
                  //将key 的中文转为拼音
                  $this->searchFunc($key,count($data));
                 
                  $e = new  ParameterException(array(
                      'msg' => 'success' ,
                      'errorCode' => '0',
                      'datas'  =>  $data ,
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


     /*
     *  搜索关键字 入库
     *  @param   $key   string   :  搜索关键字
     */
    private   function   searchFunc($key,$count ){
         $searchArr = [
                      'goods_num' => $count ,
                  ];
                  //如果关键字（完全匹配）查询,存在这条记录，则更新，否则，插入
                  $searchData =  Db::table('tp_search_word')->where('keywords',$key)->field('id')->find() ;
                  if($searchData == null ){
                      $spell =  new HelperSpell() ;
                      $pinyin_full =  $spell::encode($key, 'all');
                      $pinyin_simple = $spell::encode($key) ;
                      $searchArr['keywords'] = $key ;
                      $searchArr['pinyin_full'] = $pinyin_full ;
                      $searchArr['pinyin_simple'] =  $pinyin_simple ;
                      $searchArr['search_num'] = 1 ;
                      Db::name('search_word')->insert($searchArr);
                  }else{
                      Db::name('search_word')->where('id',$searchData['id'])->update($searchArr);
                      Db::table('tp_search_word')->where('id',$searchData['id'])->setInc('search_num');
                  }
                  
    }
}
