<?php
namespace app\admin\controller;
class Index extends Common
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * 首页
     */
    public function index()
    {
        return view();
        
    }
    
    
    
}
