<?php
namespace app\admin\controller;
use \think\Controller;
use think\Db;
use think\Cookie;
use app\admin\model\AdminModel;
use think\Request;

class Login extends Controller
{
    /**
     * 登录
     */
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 登录方法
     */
    public function checkLogin(){
        if($_POST['username']){
            $username = $_POST['username'];
        }else{
            $this->error("请输入用户名！");
        }
        if($_POST['password']){
            $password = $_POST['password'];
        }else{
            $this->error("请输入密码！");
        }
        $userModel = new AdminModel();
        $hasUser = $userModel->findUserByName($username);

        if(empty($hasUser)){
            $this->error("管理员不存在！");

        }
        if($hasUser['password'] != $this->get_password($password)){
            $this->error("密码错误！");
        }
        if($hasUser['status'] != 1){
            $this->error("该账户已经被禁用！");
        }
        //更新登录状态
        $map = [
            'last_login_ip' => request()->ip(),
            'last_login_time' => time()
        ];
        $userUpdate = db("admin") -> where('id='.$hasUser['id']) -> update($map);
        $map_result = [
            'username' => $username,
            'password' => $this->get_password($password)
        ];
        $admininfo = db("admin") -> where($map_result) -> find();
        if($admininfo){
            //登录成功后跳转
            Cookie::set( config('cookie.prefix'). 'admin_auth', md5($admininfo['id'] ."\t" . $username));
            Cookie::set( 'admin_uid', $admininfo['id']);
            Cookie::set('admininfo',$admininfo);
            $this->success('登陆成功','/admin');
        }else{
            $this->error("您输入的用户名或密码有误！");
        }
    }
    
    private function get_password($password) {
        return md5(substr(md5('xzgh'.$password),0,30));
    }

    /**
     * 登出
     */
    public function logout(){
        Cookie::delete(config('cookie.prefix').'admin_auth');
        Cookie::delete('admininfo');
        Cookie::delete('admin_uid');
        $this->redirect(url('/admin/login'));
    }
}
