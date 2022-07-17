<?php

namespace app\index\controller;

use page\Page;
use think\Controller;
use think\Request;

class Wish extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $map = [
            'status'=>2
        ];
        $pageSize = 10;
        $listResult = db("wish")->where($map)
                        ->order('created_at desc')
                        ->paginate($pageSize );
        $this->assign('list', $listResult->items());
        return $this->fetch();
    }

}
