<?php

namespace    app\api\model  ;

use think\Db;
use think\Model;

class  User  extends Model {

    protected   $table = 'tp_users' ;

    //手机号码  密码 登录
    public  function  identiMobilePass($postdata){
     
     //         $data  =    Db::table('tp_users')->alias('u')
     //                ->where('u.mobile' ,$postdata['mobile'] )
     //                ->where('u.password' ,encrypt($postdata['password']) )
     //                ->field(' u.user_id , u.uid ')
     //                ->find() ;

     // if(!empty($data)){
     //     if( ($data['uid'] == 0 )   ||  ($data['uid'] == null ) ){
     //           $inst_id =   Db::table('tp_referrer')
     //                             ->where('user_id' , $data['user_id'])
     //                             ->where('open_status', '1')
     //                             ->field('inst_id')
     //                             ->find() ;
     //         if($inst_id == null ){
     //             $inst_id = 1 ;
     //         }else{
     //             $inst_id = $inst_id['inst_id'];
     //         }
     //    }else{
     //         $inst_id =     Db::table('tp_referrer')
     //                            ->where('user_id' , $data['uid'])
     //                            ->where('open_status', '1')
     //                            ->field('inst_id')
     //                            ->find() ;

     //         if($inst_id == null ){
     //              $inst_id = 1 ;
     //         }else{
     //             $inst_id = $inst_id['inst_id'];
     //         }
     //     }

     //     $resdata =   Db::table('tp_users')->alias('u')
     //         ->where('u.mobile', $postdata['mobile'])
     //         ->where('u.password', encrypt($postdata['password']))
     //         ->join('tp_users u2', 'u2.user_id = u.uid','left')
     //         ->join('tp_region r', 'r.id = u.province' , 'left')
     //         ->join('tp_region r2', 'r2.id = u.city' , 'left')
     //         ->join('tp_region r3', 'r3.id = u.district' , 'left')
     //         ->field('u.user_id, u.mobile, u.sex,u.birthday, u.nickname, CONCAT("'.BASE_PATH.'" , u.head_pic) head_pic ,
     //                    u.statu,u.id_card,u.is_agent,u.is_distribut,u.uid,  u2.nickname  recom_name     ')
     //         ->find() ;
   
     //   $resdata['inst_id'] = $inst_id ;
     // return $resdata ;
     //    }


         $sql = ' select u.user_id, u.mobile, u.sex,u.birthday, u.nickname,u.username, CONCAT("' . BASE_PATH . '" , u.head_pic) head_pic ,
                        u.statu,u.id_card,u.is_agent,u.is_distribut,u.uid,  u2.nickname  recom_name , u.inst_id  ,
                         CONCAT(IFNULL(r.name,\'\'),IFNULL(r2.name,\'\'),IFNULL(r3.name,\'\'))  region    from  tp_users u '
                . ' left join  tp_users u2 on u2.user_id = u.uid '
                . ' left join  tp_region r on r.id = u.province '
                . ' left join  tp_region r2 on r2.id = u.city '
                . ' left join  tp_region r3 on r3.id = u.district '
                . ' where  u.mobile = "' . $postdata['mobile'] . '"  and  u.password = "' . encrypt($postdata['password']) . '" ';

            $resdata = $this->query($sql);
             return $resdata[0];
    }


    //token  登录
    public  function getUserTokenInfo($user_id){

       


                $sql  =  ' select u.user_id, u.mobile, u.sex,u.birthday, u.nickname, u.username,u.user_money, u.statu, CONCAT("'.BASE_PATH.'" , u.head_pic) head_pic,
                            u.id_card,u.is_agent,u.is_distribut,u.uid, u2.nickname  recom_name  , u.inst_id , 
                             CONCAT(IFNULL(r.name,\'\'),IFNULL(r2.name,\'\'),IFNULL(r3.name,\'\')) region      from  tp_users u  '
                . ' left join  tp_users u2 on u2.user_id = u.uid '
                . ' left join  tp_region r on r.id = u.province '
                . ' left join  tp_region r2 on r2.id = u.city '
                . ' left join  tp_region r3 on r3.id = u.district '
                . ' where  u.user_id = '.$user_id.' '  ;

            $resdata = $this->query($sql) ;

            return  $resdata[0] ;


        //         $data =   Db::table('tp_users')
        //                 ->where(array('user_id' => $user_id ))
        //                 ->field('user_id , uid')
        //                 ->find() ;

        // if(!empty($data)){
        //     if( ($data['uid'] == 0 )   ||  ($data['uid'] == null ) ){
        //         $inst_id =   Db::table('tp_referrer')
        //             ->where('user_id' , $data['user_id'])
        //             ->where('open_status', '1')
        //             ->field('inst_id')
        //             ->find() ;
        //     }else{
        //         $inst_id =     Db::table('tp_referrer')
        //             ->where('user_id' , $data['uid'])
        //             ->where('open_status', '1')
        //             ->field('inst_id')
        //             ->find() ;
        //         if($inst_id == null ){
        //             $inst_id = 1 ;
        //         }
        //     }

        //     $resdata =    Db::table('tp_users')->alias('u')
        //                     ->where('u.user_id',$user_id)
        //                     ->join('tp_users u2', 'u2.user_id = u.uid','left')
        //                     ->join('tp_region r', 'r.id = u.prvince', 'left')
        //                     ->join('tp_region r2', 'r2.id = u.city', 'left')
        //                     ->join('tp_region r3', 'r3.id = u.district', 'left')
        //                     ->field('u.user_id, u.mobile, u.sex,u.birthday, u.nickname,  u.statu, CONCAT("'.BASE_PATH.'" , u.head_pic) head_pic,
        //                     u.id_card,u.is_agent,u.is_distribut,u.uid, u2.nickname  recom_name , CONCAT_WS(r.name, r2.name,. r3.name )  region  ')
        //                     ->find() ;
        //     $resdata['inst_id'] = $inst_id ;
        //     return  $resdata ;
        // }


    }

    /*
     * 根据用户编码查询出该用户的所有下级用户
     * @param   $user_id    int   :用户编码
     * @return    Array
     * */
    public  function  getSubordinators($user_id){
        return   Db::table('tp_users')
                        ->where('uid', $user_id)
                        ->field('user_id, mobile, nickname ,FROM_UNIXTIME( reg_time , "%Y-%m-%d %H:%i:%s") reg_time  , CONCAT("'.BASE_PATH.'" , head_pic)  head_pic'  )
                        ->select() ;
    }

    /*
     *  根据用户编码获取用户信息
     *  @param   $user_id    int   :  用户编码
     *  @return   Array
     * */
    public  function  getUserInfoBy($user_id){
        return   Db::table('tp_users')
                            ->alias('u')
                            ->where('u.user_id' , $user_id)
                            ->join('tp_region r', 'r.id = u.province', 'left')
                            ->join('tp_region r2', 'r2.id = u.city','left')
                            ->field('u.user_id, u.nickname ,u.mobile,u.sex ,u.username , 
                                        CONCAT("'.BASE_PATH.'"  , u.head_pic)  head_pic ,
                                        CONCAT(r.name, r2.name)  region  ')
                            ->find() ;
    }



    /*
     * 根据用户编码 获取 某个值
     * @param   $user_id    :   用户编码
     * @param   $column   :  字段名
     * */
    public  function  getUserValueBy($user_id , $column){
         $data =  Db::table('tp_users')
                    ->where('user_id' , $user_id)
                    ->value("$column") ;

         return  $data ;
    }


    public  function  identiMobilePass2($postdata)
    {
         // var_dump($postdata['mobile']) ;  var_dump($postdata['password']) ; die ;
        $sql = ' select u.user_id, u.mobile, u.sex,u.birthday, u.nickname, u.username,u.user_money,CONCAT("' . BASE_PATH . '" , u.head_pic) head_pic ,
                        u.statu,u.id_card,u.is_agent,u.is_distribut,u.uid,  u2.nickname  recom_name , u.inst_id  ,
                         CONCAT(IFNULL(r.name,\'\'),IFNULL(r2.name,\'\'),IFNULL(r3.name,\'\'))  region    from  tp_users u '
            . ' left join  tp_users u2 on u2.user_id = u.uid '
            . ' left join  tp_region r on r.id = u.province '
            . ' left join  tp_region r2 on r2.id = u.city '
            . ' left join  tp_region r3 on r3.id = u.district '
            . ' where  u.mobile = "' . $postdata['mobile'] . '"  and  u.password = "'.$postdata['password'].'" ';

        $resdata = $this->query($sql);
        return $resdata[0];
    }

}