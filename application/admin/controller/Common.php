<?php
namespace app\admin\controller;
use think\Controller;
use think\Cookie;
class Common extends Controller
{
    protected $admininfo;
    protected $authCheck;
    function __construct()
    {
        parent::__construct();
        if(Cookie::get(config('cookie.prefix').'admin_auth')){
            $this->admininfo = Cookie::get('admininfo');
            $this->assign("admininfo",$this->admininfo);
        }else{
            exit('<script>top.location.href="/admin/login"</script>');
        }
    }
}