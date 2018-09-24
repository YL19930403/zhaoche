<?php


namespace app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;
use think\Db;

class   Version extends  Base {

    public  function  checkVersion($type){
/*
        $total = [];

        $a = Db::table('tp_version')->query('select  id, name   from tp_region where level = 1') ;

        foreach ($a as  $k => $v ){
            $b =  Db::table('tp_version')->query('select id, name from tp_region where parent_id = "'.$v['id'].'" and level = 2');

            foreach ($b as  $k2=>$v2 ){
                   $c =  Db::table('tp_version')->query('select id, name from tp_region where parent_id = "'.$v2['id'].'" and level = 3 ') ;
                   $v2['district'] = $c ;
                   $b[$k2] = $v2 ;
            }

            $v['city'] = $b ;
            $a[$k] = $v ;
        }
        $total['province'] = $a ;
        $d = json_encode($total,JSON_UNESCAPED_UNICODE) ;
        var_dump($d  ) ; die ;
        die ;
*/
       $is_type_Inter =    isAppPositiveInteger($type) ;

       if(!$is_type_Inter){
           $e = new  ParameterException(array(
               'msg' => '参数必须为正整数' ,
               'errorCode' => '391023',
           ));
           throw  $e ;
       }

       if($type != 1  &&  $type != 2 ){
           $e = new  ParameterException(array(
               'msg' => '客户端类型错误' ,
               'errorCode' => '391024',
           ));
           throw  $e ;
       }

        $versonData =   Db::table('tp_version')
                            ->where('type',$type)
                            ->field('version_id, version, url , is_update')
                            ->order('update_time desc')
                            ->limit(1)
                            ->find();

        if(!empty($versonData)){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => $versonData
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => null
            ));
            throw  $e ;
        }

    }

}