<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 15:34
 */

namespace app\admin\controller;
use app\admin\logic\OrderLogic;
use think\AjaxPage;
use think\Page;
use think\Verify;
use think\Db;
use app\admin\logic\UsersLogic;
use think\Loader;


class Version extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function ajaxindex(){
        $count = M('version')->count();
        $Page  = new AjaxPage($count,10);
        $ver = M('version')->limit($Page->firstRow.','.$Page->listRows)->select() ;
        $this->assign('ver',$ver);
        return $this->fetch();
    }

    public function add_version(){
            return $this->fetch();
    }

    public function add(){
        $admin_info = getAdminInfo(session('admin_id'));
        if(IS_POST){
            $version['type']      = $_POST['type'];
            $version['version']  = $_POST['version'];
            $version['url']       = $_POST['url'];
            $version['operator'] = $admin_info['user_name'];
            $version['add_time'] = time();

            if($version['type'] == '' )
                $this->error('客户端类型不能为空',U('Version/add_version'));

            if($version['version'] == '')
                $this->error('版本号不能为空',U('Version/add_version'));

            if($version['url'] == '')
                $this->error('下载地址不能为空',U('Version/add_version'));
        }
         $add =  M('version')->add($version);
         if($add){
             $this->success('添加成功',U('Version/index'));exit;
         }else{
             $this->error('添加失败',U('Version/index'));
         }

    }

    public function detail(){
        $version_id = I('get.id');
        $ver = M('version')->where('version_id','=',$version_id)->find() ;
        if(IS_POST){
            //  版本号信息编辑
            $row = M('version')->where(array('version_id'=>$version_id))->save($_POST);
            if($row)
              $this->success('修改成功',U('Version/index'));
              $this->error('未作内容修改或修改失败');
        }
        $this->assign('ver',$ver);
        return $this->fetch();
    }


    public function delete(){
        $version_id = I('get.id');
        if($version_id){
            $row = M('version')->where(array('version_id'=>$version_id))->delete();
            if($row !== false){
                $this->ajaxReturn(array('status' => 1, 'msg' => '删除成功', 'data' => ''));
            }else{
                $this->ajaxReturn(array('status' => 0, 'msg' => '删除失败', 'data' => ''));
            }
        }else{
            $this->ajaxReturn(array('status' => 0, 'msg' => '删除失败', 'data' => ''));
        }

    }
}