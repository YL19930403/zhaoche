<?php

namespace   app\api\controller\v1 ;

use  app\lib\exception\ParameterException ;

class   Ad  extends  Base {


      //广告页接口
    public  function   getlist($orderby){
          //判断
          $is_empty =  isAppNotEmpty($orderby) ;
          if(!$is_empty){
              $e = new  ParameterException(array(
                  'msg' => '广告位参数不能为空' ,
                  'errorCode' => '391016',
              ));
              throw  $e ;
          }

          $is_Int =  isAppPositiveInteger($orderby) ;
          if(!$is_Int){
              $e = new  ParameterException(array(
                  'msg' => '广告位参数必须为正整数' ,
                  'errorCode' => '391042',
              ));
              throw  $e ;
          }

        //获取当前的时间戳
        $adModel =   model('Ad');
        $data =  $adModel->getAdList($orderby) ;
        if(!empty($data)){
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => $data ,
            ));
            throw  $e ;
        }else{
            $e = new  ParameterException(array(
                'msg' => 'success' ,
                'errorCode' => '0',
                'datas' => null ,
            ));
            throw  $e ;
        }
    }

    //      获取广告页接口
    public  function  getadlist(){
        //获取当前的时间戳
        $currtimes = time() ;
//        var_dump($currtimes) ; die ;
        $adlist =  M('ad')->where('start_time' ,'<' , $currtimes)
            ->where('end_time' , '>', $currtimes)
            ->where('enabled','=', '1')
            ->order('orderby desc ')
            ->limit(4)
            ->field('ad_id, ad_code')
            ->select() ;
        if(!empty($adlist)){
            foreach ($adlist as  $k=>$v ) {
                $v['ad_code'] = BASE_PATH . $v['ad_code'];
                $adlist[$k]['ad_code'] = $v['ad_code'];
            }
        }

        $e = new  ParameterException(array(
            'msg' => 'success' ,
            'errorCode' => '0',
            'datas' => $adlist ,
        ));
        throw  $e ;
    }


    public  function  getad(){
        $currtimes = time() ;
        $adlist =  M('ad')->where('start_time' ,'<' , $currtimes)
            ->where('end_time' , '>', $currtimes)
            ->where('enabled','=', '1')
            ->where('orderby', '=' , '100')
            ->limit(3)
            ->field('ad_id, ad_code')
            ->select() ;
        if(!empty($adlist)){
            foreach ($adlist as  $k=>$v ) {
                $v['ad_code'] = BASE_PATH . $v['ad_code'];
                $adlist[$k]['ad_code'] = $v['ad_code'];
            }
        }

        $e = new  ParameterException(array(
            'msg' => 'success' ,
            'errorCode' => '0',
            'datas' => $adlist ,
        ));
        throw  $e ;

    }



}

