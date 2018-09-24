<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;

class   Task  extends  Model{

      public  function   getTaskListBy($user_id , $page  , $per_page  ){
          $page = $page * $per_page ;
          return    Db::table('tp_user_task')
                        ->alias('ut')
                        ->where('ut.user_id', $user_id)
                        ->join('tp_task t', 't.task_id = ut.task_id' , 'left')
                        ->join('tp_goods g', 'g.goods_id = t.goods_id', 'left')
                        ->field('ut.id, ut.task_id,t.name, ut.task_number , ut.complete_number ,ut.status ,
                                    FROM_UNIXTIME( ut.start_time , "%Y-%m-%d %H:%i:%s") start_time   ,
                                     FROM_UNIXTIME( ut.end_time , "%Y-%m-%d %H:%i:%s") end_time ,
                                      g.goods_id, g.goods_name , g.shop_price , CONCAT("'.BASE_PATH.'" , g.original_img)  original_img  ')
                        ->page($page, $per_page)
                        ->select() ;
    }


    /*
     *  根据 用户编码  和  任务编码  查询任务详情编码
     *  @param     $user_id    int   :   用户编码
     *  @param     $task_id    int   :   任务编码
     *  @return   Array
     * */
    public  function  getDetailBy($user_id , $task_id){

        $firstData =    Db::table('tp_user_task')
                                            ->alias('ut')
                                            ->where('ut.task_id' , $task_id)
                                            ->where('ut.user_id' , $user_id)
                                            ->join('tp_task t' , 't.task_id = ut.task_id' , 'left')
                                            ->field('ut.id, ut.task_id,t.name, ut.task_number , ut.complete_number ,ut.status ,
                                                                     FROM_UNIXTIME( ut.start_time , "%Y-%m-%d %H:%i:%s") start_time ,
                                                                     FROM_UNIXTIME( ut.end_time , "%Y-%m-%d %H:%i:%s") end_time')
                                            ->find() ;


        if(!empty($firstData)){
            //查询出我的已完成任务的下级
            $secondData =     $taskjournal =   Db::table('tp_users')
                                    ->alias('u')
                                    ->where('u.uid' , $user_id)
                                    ->join('tp_user_task ut', 'ut.user_id = u.user_id and ut.status = 1 ' )
                                    ->join('tp_task t', 't.task_id = ut.task_id' )
                                    ->join('tp_goods g', 't.goods_id = g.goods_id' )
                                    ->field('u.user_id ,ut.id  sub_id ,  u.nickname, u.mobile ,  t.goods_id, g.goods_name,CONCAT("'.BASE_PATH.'", g.original_img) original_img,
                                                FROM_UNIXTIME( ut.complete_time , "%Y-%m-%d %H:%i:%s") complete_time ')
                                    ->order('complete_time  desc ')
                                    ->select() ;

            if(!empty($secondData)){
                $firstData['taskjournal'] = $secondData ;
            }

        }
        return  $firstData ;
    }

}



