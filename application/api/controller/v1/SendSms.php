<?php

namespace  app\api\controller\v1 ;
use  app\lib\exception\ParameterException ;

class SendSms {

    private $user_id = 'JS6208';
    private $password = '456852';


    //发送验证码
    public function sendCode222()
    {
        $postdata = request()->post() ;
        $scene = $postdata['scene'];    //发送短信验证码使用场景  1注册 2 找回密码
        $mobile = $postdata['mobile'];

        //注册
        //判断是否存在验证码
        $data = M('sms_log')->where(array('mobile' => $mobile,  'status' => 1))->order('id DESC')->find();
        //获取时间配置
        $sms_time_out = '60';
        //60秒以内不可重复发送
        if ($data && (time() - $data['add_time']) < $sms_time_out) {
            $e = new  ParameterException(array(
                'msg' => $sms_time_out . '秒内不允许重复发送',
                'errorCode' => '391020',
            ));
            throw  $e;
        }
        //随机一个验证码
        $code = mt_rand(100000, 999999);
        if ($scene == 1) {
            $msg = '验证码：' . $code . ',您正在注册成为找车网用户, 请勿告诉他人，感谢您的支持!';
        }
        if ($scene == 2) {
            $msg = '验证码：' . $code . ',用于密码找回，如非本人操作，请及时检查账户安全';
        }

        //发送短信
        $resp = sendSms($mobile, $msg, $code, $scene, $needstatus = 'true');

        if ($resp['status'] == 1) {
            //发送成功, 修改发送状态位成功
            $e = new  ParameterException(array(
                'msg' => '验证码发送成功',
                'errorCode' => '0',
            ));
            throw  $e;
        } else {
            $e = new  ParameterException(array(
                'msg' => '验证码发送失败',
                'errorCode' => '391036',
            ));
            throw  $e;
        }
    }

    public function sendCode()
    {
        $postdata = request()->post() ;

        $mobile = $postdata['mobile'];
        $data = M('sms_log')->where(array('mobile' => $mobile,  'status' => 1))->order('id DESC')->find();
        //获取时间配置
        $sms_time_out = '60';
        //60秒以内不可重复发送
        if ($data && (time() - $data['add_time']) < $sms_time_out) {
            $e = new  ParameterException(array(
                'msg' => $sms_time_out . '秒内不允许重复发送',
                'errorCode' => '391020',
            ));
            throw  $e;
        }


        $scene = $postdata['scene'];    //发送短信验证码使用场景  1注册 2 找回密码
//        $mobile = $postdata['mobile'];
        //南方短信节点url地址
//        $url = 'http://api01.monyun.cn:7901/sms/v2/std/';
        $url = 'http://TSU3.800CT.COM:8086/sms/v2/std/';  //联通
//        $url = 'http://TSC3.800CT.COM:8086/sms/v2/std/';  //电信
        //北方短信节点url地址
        //$url = 'http://api02.monyun.cn:7901/sms/v2/std/';
        $smsSendConn = new \app\common\logic\SmsSendConn($url);
        $data=array();
        //设置账号(必填)
        $data['userid'] = $this->user_id;
        //设置密码（必填.填写明文密码,如:1234567890）

        $data['pwd'] = $this->password ;
        // 设置手机号码 此处只能设置一个手机号码(必填)
        $data['mobile'] = $postdata['mobile'];
        //设置发送短信内容(必填)
        $code = mt_rand(100000, 999999);
        if ($scene == 1) {
            $data['content'] = '验证码：' . $code . ',您正在注册成为找车网用户, 请勿告诉他人，感谢您的支持!';
        }
        if ($scene == 2) {
            $data['content'] = '验证码：' . $code . ',用于密码找回，如非本人操作，请及时检查账户安全';
        }
//        $data['content'] = '验证码：6666，打死都不要告诉别人哦！';
        // 业务类型(可选)
        $data['svrtype']='';
        // 设置扩展号(可选)
        $data['exno']='';
        //用户自定义流水编号(可选)
        $data['custid']='';
        // 自定义扩展数据(可选)
        $data['exdata']='';

        try {
            $result = $smsSendConn->singleSend($data);
            if ($result['result'] === 0) {
                $session_id = session_id();
                $log_id = M('sms_log')->insertGetId(array('mobile' => $mobile, 'code' => $code, 'add_time' => time(), 'session_id' => $session_id, 'status' =>1,'scene'=>$scene,  'msg' => $data['content']));
                $e = new  ParameterException(array(
                    'msg' => '验证码发送成功',
                    'errorCode' => '0',
                ));
                throw  $e;
            } else {
//                print_r("单条信息发送失败，错误码：" . $result['result']);
                $e = new  ParameterException(array(
                    'msg' => '验证码发送失败',
                    'errorCode' => '391036',
                ));
                throw  $e;
            }
        }catch (Exception $e) {
            print_r($e->getMessage());//输出捕获的异常消息，请根据实际情况，添加异常处理代码
            return false;
        }


    }


}