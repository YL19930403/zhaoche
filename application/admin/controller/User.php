<?php


namespace app\admin\controller;
use app\admin\logic\OrderLogic;
use think\AjaxPage;
use think\Page;
use think\Verify;
use think\Db;
use app\admin\logic\UsersLogic;
use think\Loader;

class User extends Base {

    public function index(){
        $condition = array();

        if(is_numeric($_GET['search']))
        {
            I('search') ? $condition['c.mobile'] = I('search') : false;
        }else{
            I('search') ? $condition['c.nickname']  = I('search')  : false;
        }

        $admin_info = getAdminInfo(session('admin_id'));
        if($admin_info['admin_id'] == 1){
            $count = M('users c')->where($condition)->count();
            $Page = new Page($count,10);

            $userList = M('users c')->where($condition)->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            $condition['a.plat_admin_id'] =$admin_info['admin_id'];
            $condition['a.open_status'] = 1;
            $count = M('inst a')
                ->join('users c','a.inst_id = c.inst_id',RIGHT)
                ->where($condition)
                ->count();
            $Page  = new Page($count,10);

            $userList =M('inst a')
                ->join('users c','a.inst_id = c.inst_id',RIGHT)
                ->where($condition)
                ->order('c.reg_time desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
        $user_id_arr = get_arr_column($userList, 'user_id');

        if(!empty($user_id_arr)) {
            //遍历$userlist
            foreach ($userList as $k => $v) {
                if ($v['uid'] == NULL) {
                    $userList[$k]['mobile_uid'] = 0;
                } else {
                    $userList[$k]['mobile_uid'] = $v['uid'] + BUSINESS_ID_BASE;
                }
                $userList[$k]['mobile_id'] = $v['user_id'] + BUSINESS_ID_BASE;

                $userList[$k]['count'] = M('users')
                    ->where('uid', '=', $v['user_id'])
                    ->count();
            }
        }
        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);
        return $this->fetch();
    }

    /*
     * 会员认证列表
     * */
    public  function ajaxmlist(){
        // 搜索条件
        $condition = array();
        I('mobile') ? $condition['mobile'] = I('mobile') : false;
        I('email') ? $condition['email'] = I('email') : false;
//        $sort_order = I('order_by').' '.I('sort');
        $condition['statu'] = 1 ;
        $condition['review'] =   0  ;

        $sort_order = 'update_time desc' ;
        $model = M('users');
//        var_dump($condition) ; die ;
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        trace("yuliang");
        trace($condition);
        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        $user_id_arr = get_arr_column($userList, 'user_id');

        //根据 $userList  中的  $user_id   去 cart表中查询出车型 和 车的价格
        if(!empty($userList)){
            foreach ($userList as $k=>$v ){
                $cartList =  M('cart')->where('user_id', $v['user_id'])->field('goods_name,goods_price')->find() ;
                if(!empty($cartList)){
                    $userList[$k]['goods_name'] = $cartList['goods_name'];
                    $userList[$k]['goods_price'] = $cartList['goods_price'];
                }
            }
        }



        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);
        return $this->fetch();
    }


    /*
    * 会员认证
    * */
    public  function  memberAuth(){
        $dataList =  M('users')->where('statu', 1)->where('review',0)->field('user_id, nickname,level, mobile')->select() ;
        $count = count($dataList);
//       var_dump($count) ; die ;
        $this->assign('count', $count) ;
        $this->assign('mlist', $dataList) ;
        return  $this->fetch() ;
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        I('mobile') ? $condition['c.mobile'] = I('mobile') : false;
        I('nickname') ? $condition['c.nickname']  = I('nickname')  : false;

        I('first_leader') && ($condition['first_leader'] = I('first_leader')); // 查看一级下线人有哪些
        I('second_leader') && ($condition['second_leader'] = I('second_leader')); // 查看二级下线人有哪些
        I('third_leader') && ($condition['third_leader'] = I('third_leader')); // 查看三级下线人有哪些

        $admin_info = getAdminInfo(session('admin_id'));
        if($admin_info['admin_id'] == 1){
            $count = M('users c')->where($condition)->count();
            $Page  = new AjaxPage($count,10);
            //  搜索条件下 分页赋值
            foreach($condition as $key=>$val) {
                $Page->parameter[$key]   =   urlencode($val);
            }
            $userList = M('users c')->where($condition)->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            $condition['a.plat_admin_id'] =$admin_info['admin_id'];
            $condition['a.open_status'] = 1;
            $count = M('inst a')
                ->join('users c','a.inst_id = c.inst_id',RIGHT)
                ->where($condition)
                ->count();
            $Page  = new AjaxPage($count,10);
            //  搜索条件下 分页赋值
            foreach($condition as $key=>$val) {
                $Page->parameter[$key]   =   urlencode($val);
            }
            $userList =M('inst a')
                ->join('users c','a.inst_id = c.inst_id',RIGHT)
                ->where($condition)
                ->order('c.reg_time desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
        $user_id_arr = get_arr_column($userList, 'user_id');

        if(!empty($user_id_arr))
        {
            //遍历$userlist
            foreach($userList as  $k=>$v ){
                if($v['uid'] == NULL ){
                    $userList[$k]['mobile_uid'] = 0  ;
                }else{
                    $userList[$k]['mobile_uid'] = $v['uid'] + BUSINESS_ID_BASE ;
                }
                $userList[$k]['mobile_id'] = $v['user_id'] + BUSINESS_ID_BASE ;

                $userList[$k]['count']= M('users')
                    ->where('uid','=',$v['user_id'])
                    ->count();
            }
        }

        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);
        return $this->fetch();
    }

    public function parent(){

        $admin_info = getAdminInfo(session('admin_id'));
        $condition['c.uid']=$_GET['uid'];
        if($admin_info['admin_id'] == 1){
            $count = M('users c')->where($condition)->count();
            $userList = M('users c')->where($condition)->order('reg_time desc')->select();
        }else{
            $condition['a.plat_admin_id'] =$admin_info['admin_id'];
            $condition['a.open_status'] = 1;
            $count = M('inst a')
                ->join('users c','a.inst_id = c.inst_id',RIGHT)
                ->where($condition)
                ->count();
            //  搜索条件下 分页赋值
            $userList =M('inst a')
                ->join('users c','a.inst_id = c.inst_id',RIGHT)
                ->where($condition)
                ->order('c.reg_time desc')
                ->select();
        }
        $user_id_arr = get_arr_column($userList, 'user_id');

        if(!empty($user_id_arr))
        {
            //遍历$userlist
            foreach($userList as  $k=>$v ){
                if($v['uid'] == NULL ){
                    $userList[$k]['mobile_uid'] = 0  ;
                }else{
                    $userList[$k]['mobile_uid'] = $v['uid'] + BUSINESS_ID_BASE ;
                }
                $userList[$k]['mobile_id'] = $v['user_id'] + BUSINESS_ID_BASE ;

                $userList[$k]['parent_count']= M('users')
                    ->where('uid','=',$v['user_id'])
                    ->count();
            }
        }

        $names = $this->getParentuserList($_GET['uid']);
        if(count($names) > 0){
            $names = array_reverse($names);
            $parent_path = implode($names, '>');
        }
        $this->assign('parent_path',$parent_path);
        $this->assign('userList',$userList);
        $this->assign('count',$count);//总共
        return $this->fetch();
    }

    function getParentuserList($parent_id){
        $names = array();
        $users =  M('users')->where(array('user_id'=>$parent_id))->find();
        array_push($names,$users['nickname']);
        if($users['uid'] != 0){
            $nregion = $this->getParentuserList($users['uid']);
            if(!empty($nregion)){
                $names = array_merge($names, $nregion);
            }
        }
        return $names;
    }


    /*
     * 会员认证信息详情
     * */
    public  function  authdetail(){
        $uid = I('get.id');
        $model =  M('image') ;

        $idata = $model->where('user_id', $uid)->field('license,identi_front,identi_back,credit_front,credit_back')->find() ;

        if($idata != NULL ){
//             foreach ($idata as  $k => $v ) {
//                    $v =   'localhost'  . $v ;
//                    $idata[$k] = $v ;
//             }
            $idata['user_id'] = $uid ;
            $this->assign( 'idata',$idata) ;
        }

        return  $this->fetch() ;
    }


    public  function  refuseCheck(){
        $uid =   request()->get('id') ;
        if($uid){
            $res =  M('users')->where(array('user_id' => $uid))->save(array('review' => 0, 'statu' => 0)) ;
            if($res){
                $data = [
                    'status' => 0 ,
                    'msg'   =>  '拒绝成功'
                ] ;
                echo  json_encode($data , true ) ;
            }else{
                $data = [
                    'status' => 1 ,
                    'msg'   =>  '拒绝失败'
                ] ;
                echo  json_encode($data , true ) ;
            }
        }else{
            $data = [
                'status' => 2 ,
                'msg'   =>  '用户不存在'
            ] ;
            echo  json_encode($data , true ) ;
        }
    }

    //通过审核
    public  function agreeCheck(){
        $uid =  request()->get('id') ;
        if($uid){
            $res =  M('users')->where(array('user_id' => $uid))->save(array('review' => 1)) ;
            if($res){
                $data = [
                    'status' => 0 ,
                    'msg'   =>  '修改成功'
                ] ;
                echo  json_encode($data , true ) ;
            }else{
                $data = [
                    'status' => 1 ,
                    'msg'   =>  '修改失败'
                ] ;
                echo  json_encode($data , true ) ;
            }
        }else{
            $data = [
                'status' => 2 ,
                'msg'   =>  '用户不存在'
            ] ;
            echo  json_encode($data , true ) ;
        }


    }

    /**
     * 会员详细信息查看
     */
    public function detail(){
        $admin_info = getAdminInfo(session('admin_id'));
        $users = M('inst')
            ->where('plat_admin_id','=',$admin_info['admin_id'])
            ->find();
        //机构
        $sql = M('inst')->where('open_status','=',1)->select();
        $uid = I('get.id');
        $useres = D('users a')->where(array('a.user_id'=>$uid))->find();
        if($useres !=NULL){
            $user  = $useres;
        }else{
            $user = D('users ')->where(array('user_id'=>$uid))->find();
        }
        if(!$user)
            exit($this->error('会员不存在'));
        if(IS_POST){
            //  会员信息编辑
            $password = I('post.password');
            $password2 = I('post.password2');
            if($password != '' && $password != $password2){
                exit($this->error('两次输入密码不同'));
            }
            if($password == '' && $password2 == ''){
                unset($_POST['password']);
            }else{
                $arr['password'] = encrypt($_POST['password']);
            }

            if(!empty($_POST['mobile']))
            {   $mobile = trim($_POST['mobile']);
                $c = M('users')->where("user_id != $uid and mobile = '$mobile'")->count();
                $c && exit($this->error('手机号不得和已有用户重复'));
            }

            if(!empty($_POST['leader_mobile'])){
                $leader_mobile =trim($_POST['leader_mobile']);
                $c= M('users') ->where('mobile','=',$leader_mobile)->find();
                $arr['uid']  = $c['user_id'];
            }

            $arr['mobile']=$_POST['mobile'];
            $arr['sex']   =$_POST['sex'];
            $arr['leader_mobile'] = $_POST['leader_mobile'];
            $arr['nickname'] = $_POST['nickname'];
            $arr['is_agent'] = $_POST['is_agent'];
            if($_POST['is_agent'] >0){
                if($_POST['inst_id'] == NULL)
                    exit($this->error('请选择代理所属机构'));
            }
            if($_POST['inst_id'] != NILL){
                $inst['inst_id'] =$_POST['inst_id'];
                $ar = M('users')->where(array('user_id'=>$uid))->save($inst);
            }
            $row = M('users')->where(array('user_id'=>$uid))->save($arr);
            if($row||$ar)
                exit($this->success('修改成功'));
            exit($this->error('未作内容修改或修改失败'));
        }
//        if(strlen($_user['id_card']) ==15){
//            $_user['id_card'] =substr_replace($_user['id_card'],"******",6,6);
//        }
//
//        if(strlen($_user['id_card']) ==18){
//            $_user['id_card'] =substr_replace($_user['id_card'],"******",8,6);
//        }
        $user_data = M('users')
            ->where(" user_id = {$user['user_id']}")
            ->find();

        $ar['user_id'] =$user_data['uid'];
        $us = M('users')->where($ar)->find();
        $user['leader_mobile'] = $us['mobile'];
        $user['first_leader'] =$us['user_id'];
        $user['first_name']  = $us['nickname'];
        $user['first_lower'] = M('users')->where("uid = {$user['user_id']}")->count();
        $this->assign('sql',$sql);
        $this->assign('user',$user);
        $this->assign('users',$users);
        return $this->fetch();
    }

    public function add_user(){
        $admin_info = getAdminInfo(session('admin_id'));
        $users = M('inst')
               ->where('plat_admin_id','=',$admin_info['admin_id'])
               ->find();
        if(IS_POST){
            if($users['inst_id'] == 1){
                $data=[
                    'nickname'=>$_POST['nickname'],
                    'password'=>$_POST['password'],
                    'mobile'  =>$_POST['mobile'],
                    'id_card' =>$_POST['id_card'],
                    'sex'      =>$_POST['sex'],
                    'is_agent'=>$_POST['is_agent'],
                ];
                if($_POST['is_agent'] >0){
                    if($_POST['inst_id'] == NULL)
                        exit($this->error('请选择代理所属机构'));
                }
                if($_POST['inst_id'] !=null){
                    $data['inst_id']=$_POST['inst_id'];
                }
            }else{
                $data=[
                    'nickname'=>$_POST['nickname'],
                    'password'=>$_POST['password'],
                    'mobile'  =>$_POST['mobile'],
                    'id_card' =>$_POST['id_card'],
                    'sex'      =>$_POST['sex'],
                    'is_agent'=>0,
                ];
            }


            $user_obj = new UsersLogic();
            $res = $user_obj->addUser($data);
            if($res['status'] == 1 ){
                $this->success('添加成功',U('User/index'));exit;
            }else{
                $this->error('添加失败,'.$res['msg'],U('User/index'));
            }

        }
        $p = M('region')->where(array('parent_id'=>0,'level'=> 1))->select();
        //机构
        $sql = M('inst')->where('open_status','=',1)->select();
        $this->assign('sql',$sql);
        $this->assign('province',$p);
        $this->assign('users',$users);
        return $this->fetch();
    }


    public function export_user(){
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">会员ID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">会员昵称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员等级</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">手机号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">邮箱</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">注册时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">最后登陆</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">余额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">积分</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">累计消费</td>';
        $strTable .= '</tr>';
        $count = M('users')->count();
        $p = ceil($count/5000);
        for($i=0;$i<$p;$i++){
            $start = $i*5000;
            $end = ($i+1)*5000;
            $userList = M('users')->order('user_id')->limit($start.','.$end)->select();
            if(is_array($userList)){
                foreach($userList as $k=>$val){
                    $strTable .= '<tr>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['user_id'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['nickname'].' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['level'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['email'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i',$val['reg_time']).'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i',$val['last_login']).'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_money'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_points'].' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['total_amount'].' </td>';
                    $strTable .= '</tr>';
                }
                unset($userList);
            }
        }
        $strTable .='</table>';
        downloadExcel($strTable,'users_'.$i);
        exit();
    }

    /**
     * 用户收货地址查看
     */
    public function address(){
        $uid = I('get.id');
        $lists = D('user_address')->where(array('user_id'=>$uid))->select();
        $regionList = get_region_list();
        $this->assign('regionList',$regionList);
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 删除会员
     */
    public function delete(){
        $uid = I('get.id');
        $row = M('users')->where(array('user_id'=>$uid))->delete();
        if($row){
            $this->success('成功删除会员');
        }else{
            $this->error('操作失败');
        }
    }
    /**
     * 删除会员
     */
    public function ajax_delete(){

        $uid = I('id');
        $count =M('users')->where("uid=".$uid)->count("user_id");
        if($count == 0){
            $row = M('users')->where(array('user_id'=>$uid))->delete();
            if($row !== false){
                $this->ajaxReturn(array('status' => 1, 'msg' => '删除成功', 'data' => ''));
            }else{
                $this->ajaxReturn(array('status' => 0, 'msg' => '删除失败', 'data' => ''));
            }
        }else{
            $this->ajaxReturn(array('status' => 0, 'msg' => '该会员下面有下级', 'data' => ''));
        }
    }

    /**
     * 账户资金记录
     */
    public function account_log(){
        $user_id = I('get.id');
        //获取类型
        $type = I('get.type');
        //获取记录总数
        $count = M('account_log')->where(array('user_id'=>$user_id))->count();
        $page = new Page($count);
        $lists  = M('account_log')->where(array('user_id'=>$user_id))->order('change_time desc')->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('user_id',$user_id);
        $this->assign('page',$page->show());
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 账户资金调节
     */
    public function account_edit(){
        $user_id = I('user_id');
        if(!$user_id > 0) $this->ajaxReturn(['status'=>0,'msg'=>"参数有误"]);
        $user = M('users')->field('user_id,user_money,frozen_money,pay_points,is_lock')->where('user_id',$user_id)->find();
        if(IS_POST){
            $desc = I('post.desc');
            if(!$desc)
                $this->ajaxReturn(['status'=>0,'msg'=>"请填写操作说明"]);
            //加减用户资金
            $m_op_type = I('post.money_act_type');
            $user_money = I('post.user_money/f');
            $user_money =  $m_op_type ? $user_money : 0-$user_money;
            //加减用户积分
            $p_op_type = I('post.point_act_type');
            $pay_points = I('post.pay_points/d');
            $pay_points =  $p_op_type ? $pay_points : 0-$pay_points;
            //加减冻结资金
            $f_op_type = I('post.frozen_act_type');
            $revision_frozen_money = I('post.frozen_money/f');
            if( $revision_frozen_money != 0){    //有加减冻结资金的时候
                $frozen_money =  $f_op_type ? $revision_frozen_money : 0-$revision_frozen_money;
                $frozen_money = $user['frozen_money']+$frozen_money;    //计算用户被冻结的资金
                if($f_op_type==1 and $revision_frozen_money > $user['user_money'])
                {
                    $this->ajaxReturn(['status'=>0,'msg'=>"用户剩余资金不足！！"]);
                }
                if($f_op_type==0 and $revision_frozen_money > $user['frozen_money'])
                {
                    $this->ajaxReturn(['status'=>0,'msg'=>"冻结的资金不足！！"]);
                }
                $user_money = $f_op_type ? 0-$revision_frozen_money : $revision_frozen_money ;    //计算用户剩余资金
                M('users')->where('user_id',$user_id)->update(['frozen_money' => $frozen_money]);
            }
            if(accountLog($user_id,$user_money,$pay_points,$desc,0))
            {
                $this->ajaxReturn(['status'=>1,'msg'=>"操作成功",'url'=>U("Admin/User/account_log",array('id'=>$user_id))]);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>"操作失败"]);
            }
            exit;
        }
        $this->assign('user_id',$user_id);
        $this->assign('_user',$user);
        return $this->fetch();
    }

    public function recharge(){
        $timegap = I('timegap');
        $nickname = I('nickname');
        $admin_info = getAdminInfo(session('admin_id'));
        $map = array();
        $map['user_id'] = $admin_info['agency_id'];
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $map['nickname'] = array('like',"%$nickname%");
        }
        $count = M('recharge')->where($map)->count();
        $page = new Page($count);
        $lists  = M('recharge')->where($map)->order('ctime desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('pager',$page);
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    public function level(){
        $act = I('get.act','add');
        $this->assign('act',$act);
        $level_id = I('get.level_id');
        if($level_id){
            $level_info = D('user_level')->where('level_id='.$level_id)->find();
            $this->assign('info',$level_info);
        }
        return $this->fetch();
    }

    public function levelList(){
        $Ad =  M('user_level');
        $p = $this->request->param('p');
        $res = $Ad->order('level_id')->page($p.',10')->select();
        if($res){
            foreach ($res as $val){
                $list[] = $val;
            }
        }
        $this->assign('list',$list);
        $count = $Ad->count();
        $Page = new Page($count,10);
        $show = $Page->show();
        $this->assign('page',$show);
        return $this->fetch();
    }

    /**
     * 会员等级添加编辑删除
     */
    public function levelHandle()
    {
        $data = I('post.');
        $userLevelValidate = Loader::validate('UserLevel');
        $return = ['status' => 0, 'msg' => '参数错误', 'result' => ''];//初始化返回信息
        if ($data['act'] == 'add') {
            if (!$userLevelValidate->batch()->check($data)) {
                $return = ['status' => 0, 'msg' => '添加失败', 'result' => $userLevelValidate->getError()];
            } else {
                $r = D('user_level')->add($data);
                if ($r !== false) {
                    $return = ['status' => 1, 'msg' => '添加成功', 'result' => $userLevelValidate->getError()];
                } else {
                    $return = ['status' => 0, 'msg' => '添加失败，数据库未响应', 'result' => ''];
                }
            }
        }
        if ($data['act'] == 'edit') {
            if (!$userLevelValidate->scene('edit')->batch()->check($data)) {
                $return = ['status' => 0, 'msg' => '编辑失败', 'result' => $userLevelValidate->getError()];
            } else {
                $r = D('user_level')->where('level_id=' . $data['level_id'])->save($data);
                if ($r !== false) {
                    $return = ['status' => 1, 'msg' => '编辑成功', 'result' => $userLevelValidate->getError()];
                } else {
                    $return = ['status' => 0, 'msg' => '编辑失败，数据库未响应', 'result' => ''];
                }
            }
        }
        if ($data['act'] == 'del') {
            $r = D('user_level')->where('level_id=' . $data['level_id'])->delete();
            if ($r !== false) {
                $return = ['status' => 1, 'msg' => '删除成功', 'result' => ''];
            } else {
                $return = ['status' => 0, 'msg' => '删除失败，数据库未响应', 'result' => ''];
            }
        }
        $this->ajaxReturn($return);
    }

    /**
     * 搜索用户名
     */
    public function search_user()
    {
        $search_key = trim(I('search_key'));
        if(strstr($search_key,'@'))
        {
            $list = M('users')->where(" email like '%$search_key%' ")->select();
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['email']}</option>";
            }
        }
        else
        {
            $list = M('users')->where(" mobile like '%$search_key%' ")->select();
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['mobile']}</option>";
            }
        }
        exit;
    }

    /**
     * 分销树状关系
     */
    public function ajax_distribut_tree()
    {
        $list = M('users')->where("first_leader = 1")->select();
        return $this->fetch();
    }

    /**
     *
     * @time 2016/08/31
     * @author dyr
     * 发送站内信
     */
    public function sendMessage()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $users = M('users')->field('user_id,nickname')->where(array('user_id' => array('IN', $user_id_array)))->select();
        }
        $this->assign('users',$users);
        return $this->fetch();
    }

    /**
     * 发送系统消息
     * @author dyr
     * @time  2016/09/01
     */
    public function doSendMessage()
    {
        $call_back = I('call_back');//回调方法
        $text= I('post.text');//内容
        $type = I('post.type', 0);//个体or全体
        $admin_id = session('admin_id');
        $users = I('post._user/a');//个体id
        $message = array(
            'admin_id' => $admin_id,
            'message' => $text,
            'category' => 0,
            'send_time' => time()
        );

        if ($type == 1) {
            //全体用户系统消息
            $message['type'] = 1;
            M('Message')->add($message);
        } else {
            //个体消息
            $message['type'] = 0;
            if (!empty($users)) {
                $create_message_id = M('Message')->add($message);
                foreach ($users as $key) {
                    M('user_message')->add(array('user_id' => $key, 'message_id' => $create_message_id, 'status' => 0, 'category' => 0));
                }
            }
        }
        echo "<script>parent.{$call_back}(1);</script>";
        exit();
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 发送邮件
     */
    public function sendMail()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $user_where = array(
                'user_id' => array('IN', $user_id_array),
                'email' => array('neq', '')
            );
            $users = M('users')->field('user_id,nickname,email')->where($user_where)->select();
        }
        $this->assign('smtp', tpCache('smtp'));
        $this->assign('users', $users);
        return $this->fetch();
    }

    /**
     * 发送邮箱
     * @author dyr
     * @time  2016/09/03
     */
    public function doSendMail()
    {
        $call_back = I('call_back');//回调方法
        $message = I('post.text');//内容
        $title = I('post.title');//标题
        $users = I('post._user/a');
        $email= I('post.email');
        if (!empty($users)) {
            $user_id_array = implode(',', $users);
            $users = M('users')->field('email')->where(array('user_id' => array('IN', $user_id_array)))->select();
            $to = array();
            foreach ($users as $user) {
                if (check_email($user['email'])) {
                    $to[] = $user['email'];
                }
            }
            $res = send_email($to, $title, $message);
            echo "<script>parent.{$call_back}({$res['status']});</script>";
            exit();
        }
        if($email){
            $res = send_email($email, $title, $message);
            echo "<script>parent.{$call_back}({$res['status']});</script>";
            exit();
        }
    }

    /**
     * 提现申请记录
     */
    public function withdrawals()
    {
        $this->get_withdrawals_list();
        return $this->fetch();
    }

    public function get_withdrawals_list($status=''){
        $admin_info = getAdminInfo(session('admin_id'));
        // 搜索条件
        $user_id = I('user_id/d');
        $realname = I('realname');
        $bank_card = I('bank_card');
        $create_time = I('create_time');
        $create_time = str_replace("+"," ",$create_time);
        $create_time2 = $create_time  ? $create_time  : date('Y-m-d',strtotime('-1 year')).' - '.date('Y-m-d',strtotime('+1 day'));
        $create_time3 = explode(' - ',$create_time2);
        $this->assign('start_time',$create_time3[0]);
        $this->assign('end_time',$create_time3[1]);
        $where['w.create_time'] =  array(array('gt', strtotime(strtotime($create_time3[0])), array('lt', strtotime($create_time3[1]))));
        $status = empty($status) ? I('status') : $status;
        if(empty($status) || $status === '0'){
            $where['w.status'] =  array('lt',1);
        }
        if($status === '0' || $status > 0) {
            $where['w.status'] = $status;
        }
//    	$_user=M('users')->where('uid','=',$admin_info['agency_id'])->select();
//    	foreach ($_user as $key=>$value){
//    	    $where['user_id'] = $value['user_id'];
//        }
        $user_id && $where['u.user_id'] = $user_id;
        $realname && $where['w.realname'] = array('like','%'.$realname.'%');
        $bank_card && $where['w.bank_card'] = array('like','%'.$bank_card.'%');
        $export = I('export');
        if($export == 1){
            $strTable ='<table width="500" border="1">';
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">申请人</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="100">提现金额</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">银行名称</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">银行账号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">开户人姓名</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">申请时间</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">提现备注</td>';
            $strTable .= '</tr>';
            $remittanceList = Db::name('withdrawals')->alias('w')->field('w.*,u.nickname')->join('__USERS__ u', 'u.user_id = w.user_id', 'INNER')->where($where)->order("w.id desc")->select();
            if(is_array($remittanceList)){
                foreach($remittanceList as $k=>$val){
                    $strTable .= '<tr>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">'.$val['nickname'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['money'].' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['bank_name'].'</td>';
                    $strTable .= '<td style="vnd.ms-excel.numberformat:@">'.$val['bank_card'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['realname'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s',$val['create_time']).'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['remark'].'</td>';
                    $strTable .= '</tr>';
                }
            }
            $strTable .='</table>';
            unset($remittanceList);
            downloadExcel($strTable,'remittance');
            exit();
        }
        $count = Db::name('withdrawals')->alias('w')->join('__USERS__ u', 'u.user_id = w.user_id', 'INNER')->where($where)->count();
        $Page  = new Page($count,20);
        $list = Db::name('withdrawals')->alias('w')->field('w.*,u.nickname')->join('__USERS__ u', 'u.user_id = w.user_id', 'INNER')->where($where)->order("w.id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('create_time',$create_time2);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->assign('pager',$Page);
        C('TOKEN_ON',false);
    }

    /**
     * 删除申请记录
     */
    public function delWithdrawals()
    {
        $model = M("withdrawals");
        $model->where('id ='.$_GET['id'])->delete();
        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn($return_arr);
    }


    public function del(){
        M('withdrawals')->where('id','=',$_GET['id'])->delete();
    }

    /**
     * 修改编辑 申请提现
     */
    public  function editWithdrawals(){
        $id = I('id');
        $model = M("withdrawals");
        $withdrawals = $model->find($id);
        $user = M('users')->where("user_id = {$withdrawals[user_id]}")->find();
        if($user['nickname'])
            $withdrawals['user_name'] = $user['nickname'];
        elseif($user['email'])
            $withdrawals['user_name'] = $user['email'];
        elseif($user['mobile'])
            $withdrawals['user_name'] = $user['mobile'];

        $this->assign('_user',$user);
        $this->assign('data',$withdrawals);
        return $this->fetch();
    }

    /**
     *  处理会员提现申请
     */
    public function withdrawals_update(){
        $id = I('id/a');
        $data['status']=$status = I('status');
        $data['remark'] = I('remark');
        if($status == 1) $data['check_time'] = time();
        if($status != 1) $data['refuse_time'] = time();
        $r = M('withdrawals')->where('id in ('.implode(',', $id).')')->update($data);
        if($r){
            $this->ajaxReturn(array('status'=>1,'msg'=>"操作成功"),'JSON');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>"操作失败"),'JSON');
        }
    }

    public function transfer(){
        $id = I('selected/a');
        if(empty($id))$this->error('请至少选择一条记录');
        $atype = I('atype');
        if(is_array($id)){
            $withdrawals = M('withdrawals')->where('id in ('.implode(',', $id).')')->select();
        }else{
            $withdrawals = M('withdrawals')->where(array('id'=>$id))->select();
        }
        $alipay['batch_num'] = 0;
        $alipay['batch_fee'] = 0;
        foreach($withdrawals as $val){
            $user = M('users')->where(array('user_id'=>$val['user_id']))->find();
            if($user['user_money'] < $val['money'])
            {
                $data = array('status'=>-2,'remark'=>'账户余额不足');
                M('withdrawals')->where(array('id'=>$val['id']))->save($data);
                $this->error('账户余额不足');
            }else{
                $rdata = array('type'=>1,'money'=>$val['money'],'log_type_id'=>$val['id'],'user_id'=>$val['user_id']);
                if($atype == 'online'){
                    header("Content-type: text/html; charset=utf-8");
                    exit();
                }else{
                    accountLog($val['user_id'], ($val['money'] * -1), 0,"管理员处理用户提现申请");//手动转账，默认视为已通过线下转方式处理了该笔提现申请
                    M('withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time()));
                    expenseLog($rdata);//支出记录日志
                }
            }
        }
        if($alipay['batch_num']>0){
            //支付宝在线批量付款
            include_once  PLUGIN_PATH."payment/alipay/alipay.class.php";
            $alipay_obj = new \alipay();
            $alipay_obj->transfer($alipay);
        }
        $this->success("操作成功!",U('remittance'),3);
    }

    /**
     *  转账汇款记录
     */
    public function remittance(){
        $status = I('status',1);
        $this->assign('status',$status);
        $this->get_withdrawals_list($status);
        return $this->fetch();
    }



}