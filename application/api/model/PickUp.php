<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;
use think\Cache;

class  PickUp  extends  Model {

    protected  $table = 'tp_pick_up' ;

    public   function  getRegionList(){
        // return    Db::table('tp_pick_up')
        //                 ->alias('p')
        //                 ->where('p.is_delete',1)
        //                 ->join('tp_region r', 'r.id = p.province_id', 'left')
        //                 ->join('tp_region r2', 'r2.id = p.city_id', 'left')
        //                 ->join('tp_region r3', 'r3.id = p.district_id', 'left')
        //                 ->field('p.pickup_id ,p.province_id  ,r.name  province_name , p.city_id , r2.name   city_name , p.district_id , r3.name  district_name')
        //                 ->select() ;
         $count =   Db::table('tp_pick_up')
                ->where('is_delete', 1)
                ->count() ;

        $cache_count  =  Cache::get('areacount');
        if($cache_count == $count){
            $data = Cache::get('areadata');
        }else{
            $count2 =   Db::table('tp_pick_up')
                ->where('is_delete', 1)
                ->count() ;
            //将这个$count2 写入缓存
            Cache::set('areacount' , $count2 , 0 ) ;

            //筛选出所有的省份
            $p_data =    Db::table('tp_pick_up')
                ->alias('p')
                ->where('p.is_delete', 1)
                ->join('tp_region r', 'r.id = p.province_id ', 'left')
                ->distinct('true')
                ->field('p.province_id id , r.name ')
                ->select() ;
            foreach ($p_data as $k => $v ){
                $c_data =   Db::table('tp_pick_up')
                    ->alias('p')
                    ->where('p.is_delete', 1)
                    ->where('p.province_id', $v['id'])
                    ->join('tp_region r', 'r.id = p.city_id', 'left')
                    ->distinct('true')
                    ->field('p.city_id id , r.name ')
                    ->select() ;
                $p_data[$k]['city'] = $c_data ;
            }

            foreach ( $p_data as $k => $v ) {
                foreach ($v['city'] as $k2 => $v2 ){
                    $d_data =  Db::table('tp_pick_up')
                        ->alias('p')
                        ->where('p.is_delete' , 1)
                        ->where('p.province_id', $v['id'])
                        ->where('p.city_id', $v2['id'])
                        ->join('tp_region r', 'r.id = p.district_id','left')
                        ->distinct('true')
                        ->field('p.district_id id , r.name ')
                        ->select() ;
                    $p_data[$k]['city'][$k2]['district'] = $d_data ;
                }
            }
            $data['province'] = $p_data ;
            Cache::set('areadata' , $data , 0) ;
        }
        return $data ;
    }
}

