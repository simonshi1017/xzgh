<?php

namespace app\admin\controller;

use \think\Db;
use app\admin\logic\page;
use think\Request;

class Admin extends Common
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function editpass(){
        $this->assign('admininfo',$this->admininfo);
        return $this->fetch();
    }

    function savepass(){
        $uid       = input('post.uid');
        $password  = input('post.password');
        $pwd       = input('post.pwd');
        $map       = [];
        if($password && $pwd){
            if($password != $pwd){
                $this->error("两次输入密码不一致");
            }
            if(strlen($password)  < 6 || strlen($password) > 20){
                $this->error("密码长度必须为6-20位");
            }
            $map['password'] = $this->get_password($password);
            $old_password = db('admin') -> where('id',$uid)  -> value("password");
            if($old_password == $map['password']){
                $this->error("新密码与旧密码相同！");
            }
        }
        if($map){
            $reslut = db('admin') -> where('id',$uid) -> update($map);
            if($reslut){
                $this->success("密码修改成功");
            }else{
                $this->error("密码修改失败,请刷新页面重试");
            }
        }else{
            $this->error("请输入密码,请刷新页面重试");
        }
    }

    private function get_password($password) {
        return md5(substr(md5('xzgh'.$password),0,30));
    }
}


    
