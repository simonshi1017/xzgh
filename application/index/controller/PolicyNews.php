<?php

namespace app\index\controller;

use page\Page;
use think\Controller;
use think\Db;
use think\Request;

class PolicyNews extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $listResult = db("policy_news")
                        ->order('id desc')
                        ->paginate();
        $this->assign('list', $listResult->items());
        $this->assign('list', $listResult);
        return $this->fetch();
    }

    public function detail()
    {
        $id   = $_GET['id'];
        $data = Db::name('policy_news')->find($id);
        $this->assign('data', $data);
        return $this->fetch();
    }

}
