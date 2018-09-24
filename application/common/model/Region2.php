<?php

namespace  app\common\model ;

use think\Model;

class  Region2 extends  Model{
    protected  $table = 'tp_region2' ;

    //根据关键词搜索count
    public  function getCount($where){
         return   $this->where($where)->count() ;
    }

    //获取省份数据
    public  function getProvinceData($where){
           $data =  $this->where($where)
                        ->where('parent_id' , 'eq', '0')
                        ->where('level', 'eq', '1')
                        ->select() ;
            return $data ;
    }

    /*
     * 递归调用
     * @param    array    $data
     * return   mixed
     */
    public  function  getRegionTree($data, $level = 1 ){
        $twoArr = [] ;    //二级数组
        $threeArr = [];   //三级数组
        $total = [];

         //根据传入的数组中的数据获取到下一级的数据
        foreach ($data as  $v  ){
            if($level == 4 ){
                return ;
            }

            $subData = $this->where('parent_id','eq',$v['id'])
                ->where('level', 'eq',$v['level'] + 1)
                ->select() ;
//            return $subData ;
//            $totalArr[$level]['sub'] = $subData ;
                if($level == 1){
                    $data['city'] = $subData ;
                }elseif ($level == 2 ){
                        $data['city']['region'] = $subData ;
                }
            $this->getRegionTree($twoArr['sub'], $level+1 ) ;
        }

        return $twoArr ;
    }
}

















