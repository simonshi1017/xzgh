<?php

namespace app\index\controller;

use page\Page;
use think\Controller;
use think\Request;

class Adv extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $listResult = db("adv")
                        ->order('sort asc')
                        ->select();
        $this->assign('list', $listResult);
        return $this->fetch();
    }

}
