<?php

namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;

class  FeedBack  extends   Base{

    public  function  feedback(){
//         var_dump($this->post) ;

        if(!isset($this->post['msg_content'])){
            $e = new  ParameterException(array(
                'msg' => '反馈内容不能为空' ,
                'errorCode' => '391016',
                'datas' =>  null  ,
            ));
            throw  $e ;
        }


          if(isset($this->post['mobile'])){
            $mobile = $this->post['mobile'] ;
            $is_mobile =  isAppMobile($mobile) ;

            if(!$is_mobile){
                $e = new  ParameterException(array(
                    'msg' => '手机号码格式不正确' ,
                    'errorCode' => '391014',
                    'datas' =>  null  ,
                ));
                throw  $e ;
            }
            $map['mobile'] = $mobile  ;
        }

        // $token = request()->header('token') ;
        $m_data =   $this->jsondata ;


        if(!empty($m_data)){

             $user_id = $m_data[1] ;
             $uModel =   model('User');
             $nickname =   $uModel->getUserValueBy($user_id , 'nickname') ;
             $map['user_id'] =  $user_id ;
             $map['user_name'] = $nickname ;
             
        }else{
          
        }



         $map['msg_content'] = $this->post['msg_content'] ;
         $map['msg_time']  = time() ;
         $fModel =   model('FeedBack');
         $res =   $fModel->save($map) ;

         if($res){
             $e = new  ParameterException(array(
                 'msg' => 'success' ,
                 'errorCode' => '0',
                 'datas' =>  null  ,
             ));
             throw  $e ;
         }else{
             $e = new  ParameterException(array(
                 'msg' => '意见反馈失败' ,
                 'errorCode' => '391041',
                 'datas' =>  null  ,
             ));
             throw  $e ;
         }
    }

}


