<?php

namespace app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;
use think\Db;

class   Task   extends   Base {

    public  function  gettasklist($user_id , $page=0 , $per_page=10){

         $is_Inter =   isAppPositiveInteger($user_id) ;
         if(!$is_Inter){
             $e = new  ParameterException(array(
                 'msg' => '参数必须为正整数' ,
                 'errorCode' => '391023',
                 'datas'  =>  null  ,
             ));
             throw  $e ;
         }


        $isPageInter =   isPageInteger($page) ;
        $isperPageInter =   isPageInteger($per_page);
        if(!$isperPageInter  ||  !$isPageInter){
            $e = new  ParameterException(array(
                'msg' => '分页参数必须为整数' ,
                'errorCode' => '391022',
                'datas'  =>  null  ,
            ));
            throw  $e ;
        }

          $taskModel =   model('Task');
          $taskData =   $taskModel->getTaskListBy($user_id, $page , $per_page);
//          var_dump($taskData) ; die ;

        if(!empty($taskData)){
                $e = new  ParameterException(array(
                    'msg' => 'success' ,
                    'errorCode' => '0',
                    'datas'  =>  $taskData
                ));
                throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas'  =>  null
            ));
            throw  $e ;
        }
    }

    /*
     *  根据 任务编码 查询任务详情
     *   @param  $task_id  int : 任务编码
     *   @return    Array
     * */
    public  function   getdetail( $user_id , $task_id){

        $is_uid_Inter =   isAppPositiveInteger($user_id) ;
        if(!$is_uid_Inter){
            $e = new  ParameterException(array(
                'msg' => '用户编码必须为正整数' ,
                'errorCode' => '391023',
            ));
            throw  $e ;
        }
        $is_tid_Inter =  isAppPositiveInteger($task_id) ;
        if(!$is_tid_Inter){
            $e = new  ParameterException(array(
                'msg' => '任务编码必须为正整数' ,
                'errorCode' => '391023',
            ));
            throw  $e ;
        }

        $tModel =  model('Task');

       $taskData =  $tModel->getDetailBy($user_id , $task_id) ;

       if(!empty($taskData)){
           $e = new  ParameterException(array(
               'msg' => 'success' ,
               'errorCode' => '0',
               'datas'      =>  $taskData ,
           ));
           throw  $e ;
       }else{
           $e = new  ParameterException(array(
               'msg' => '该用户任务不存在' ,
               'errorCode' => '391025',
               'datas'      =>  null  ,
           ));
           throw  $e ;
       }
    }

}

