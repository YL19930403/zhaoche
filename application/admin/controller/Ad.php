<?php


namespace app\admin\controller;
use think\Page;
use think\Db;

class Ad extends Base{
    public function ad(){

        $act = I('get.act','add');
        $ad_id = I('get.ad_id/d');
        $ad_info = array();
        if($ad_id){
            $ad_info = D('ad')->where('ad_id',$ad_id)->find();
            $ad_info['start_time'] = date('Y-m-d',$ad_info['start_time']);
            $ad_info['end_time'] = date('Y-m-d',$ad_info['end_time']);            
        }
        if($act == 'add')          
           $ad_info['pid'] = $this->request->param('pid');
        $position = D('ad_position')->select();
        $this->assign('info',$ad_info);
        $this->assign('act',$act);
        $this->assign('position',$position);
        return $this->fetch();
    }





    public function adList(){

        delFile(RUNTIME_PATH.'html'); // 先清除缓存, 否则不好预览
            
        $Ad =  M('ad');         
        $pid = I('pid',0);

        if($pid){
            $where['pid'] = $pid;
        	$this->assign('pid',I('pid'));
        }
        $keywords = I('keywords/s',false,'trim');
        if($keywords){
            $where['ad_name'] = array('like','%'.$keywords.'%');
        }
        $count = $Ad->where($where)->where('is_delete', 1)->count();// 查询满足要求的总记录数
        $Page = $pager = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $res = $Ad->where($where)->where('is_delete',1)->order('pid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $list = array();
        if($res){
        	$media = array('图片','文字','flash');
        	foreach ($res as $val){
        		$val['media_type'] = $media[$val['media_type']];        		
        		$list[] = $val;
        	}
        }
                                     
        $ad_position_list = M('AdPosition')->where('is_delete',1)->getField("position_id,position_name,is_open");

        $this->assign('ad_position_list',$ad_position_list);//广告位 
        $show = $Page->show();// 分页显示输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$pager);        
        return $this->fetch();
    }
    
    public function position(){

        $act = I('get.act','add');
        $position_id = I('get.position_id/d');
        $info = array();
        if($position_id){
            $info = D('ad_position')->where('position_id',$position_id)->where('is_delete', 1)->find();
        }
        $this->assign('info',$info);
        $this->assign('act',$act);
        return $this->fetch();
    }

    public function positionList()
    {

        $count = Db::name('ad_position')->where('is_delete', 1)->count();// 查询满足要求的总记录数
        $Page = $pager = new Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('ad_position')->where('is_delete',1)->order('position_id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $show = $Page->show();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('pager', $Page);
        return $this->fetch();
    }


/*
 * 删除 “广告位置”
 * */
    public  function   delPosiAd(){
        $position_id = I('post.del_id/d') ;
        if($position_id){
            $info =  D('ad_position')->where('position_id', $position_id)->where('is_delete', 1)->save(['is_delete' => 0]);
            if($info){
                $this->ajaxReturn(['status'=>1,'msg'=>"操作成功",'url'=>U('Admin/Ad/positionList')]);
            }
        }
        $this->ajaxReturn(['status'=>-1,'msg'=>"操作失败"]);
    }


    
    public function adHandle(){

    	$data = I('post.');
    	$data['start_time'] = strtotime($data['begin']);
    	$data['end_time'] = strtotime($data['end']);
    	
    	if($data['act'] == 'add'){
    		$r = D('ad')->add($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = D('ad')->where('ad_id', $data['ad_id'])->save($data);
    	}
    	
    	if($data['act'] == 'del'){
            $r = D('ad')->where('ad_id', $data['del_id'])->where('is_delete', 1)->save(['is_delete'=>0]) ;
            if($r){
                $this->ajaxReturn(['status'=>1,'msg'=>"操作成功",'url'=>U('Admin/Ad/adList')]);
            }else{
                $this->ajaxReturn(['status'=>-1,'msg'=>"操作失败"]);
            }
    	}
    	$referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Admin/Ad/adList');
        // 不管是添加还是修改广告 都清除一下缓存
        delFile(RUNTIME_PATH.'html'); // 先清除缓存, 否则不好预览
        \think\Cache::clear();
    	if($r){
    		$this->success("操作成功",U('Admin/Ad/adList'));
    	}else{
    		$this->error("操作失败",$referurl);
    	}
    }
    
    public function positionHandle(){
        $data = I('post.');
        if($data['act'] == 'add'){
            $r = M('ad_position')->add($data);
        }
        
        if($data['act'] == 'edit'){
        	$r = M('ad_position')->where('position_id',$data['position_id'])->save($data);
        }
        
        if($data['act'] == 'del'){
        	if(M('ad')->where('pid',$data['position_id'])->count()>0){
        		$this->error("此广告位下还有广告，请先清除",U('Admin/Ad/positionList'));
        	}else{
        		$r = M('ad_position')->where('position_id', $data['position_id'])->delete();
        		if($r) exit(json_encode(1));
        	}
        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Admin/Ad/positionList');
        if($r){
        	$this->success("操作成功",$referurl);
        }else{
        	$this->error("操作失败",$referurl);
        }
    }
    
    public function changeAdField(){
        $field = $this->request->request('field');
    	$data[$field] = I('get.value');
    	$data['ad_id'] = I('get.ad_id');
    	M('ad')->save($data); // 根据条件保存修改的数据
    }
    
    /**
     * 编辑广告中转方法
     */
    public function editAd()
    {
        \think\Cache::clear();        
        $request_url = urldecode(I('request_url'));
        $request_url = U($request_url,array('edit_ad'=>1));
        echo "<script>location.href='".$request_url."';</script>";
        exit;                
    }

    public function newsList(){
        $new = M('news')->select();
        $this->assign('new',$new);
        return $this->fetch();
    }

    public function newsAdd(){
        $admin_info = getAdminInfo(session('admin_id'));
        if(IS_POST){
            $arr=[
                'title' => $_POST['title'],
                'is_delete' => $_POST['is_delete'],
                'creator' => $admin_info['user_name'],
                'message' => $_POST['content'],
                'create_time'      =>time()

            ];
            $a=M('news')->add($arr);
            if($a){
                $this->success('操作成功',U('Admin/Ad/newsList'));
            }else{
                $this->success('操作失败',U('Admin/Ad/newsList'));
            }
        }
        return $this->fetch();
    }

    public function newsEdit(){
        $article=M('news')->where('id','=',$_GET['id'])->find();
        if(IS_POST){
            $arr=[
                'title' => $_POST['title'],
                'is_delete' => $_POST['is_open'],
                'message' => $_POST['content'],
                'is_delete' => $_POST['is_delete']
            ];
            $a=M('news')->where('id','=',$_GET['id'])->save($arr);
            if($a){
                $this->success('操作成功',U('Admin/Ad/newsList'));
            }else{
                $this->success('操作失败',U('Admin/Ad/newsList'));
            }
        }
        $this->assign('article',$article);
        return $this->fetch();
    }

    public function message(){
        $message = M('feedback')->where('parent_id','=',0)->select();
        $this->assign('feed',$message);
        return $this->fetch();
    }

    public function messageEdit(){
        $msg_id = $_GET['msg_id'];
        $msg = M('feedback')->where('msg_id','=',$msg_id)->find();
        if(IS_POST){
            $add['parent_id'] = $msg_id;
            $add['msg_content'] = I('post.msg_content');
            $add['msg_time'] = time();
            $add['user_name'] = 'admin';
            $row =  M('feedback')->add($add);
            if($row){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
            exit;

        }
        $reply = M('feedback')->where(array('parent_id'=>$msg_id))->select(); // 评论回复列表
        $this->assign('reply',$reply);
        $this->assign('msg',$msg);
        return $this->fetch();
    }
}