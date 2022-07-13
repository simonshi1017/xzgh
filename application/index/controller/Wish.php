<?php

namespace app\index\controller;

use page\Page;
use think\Controller;

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
        $count = db("wish")->where($map)->count();
        $p = new Page($count, 30);
        $listResult = db("wish")->where($map)->order('created_at desc')->limit($p->currentPage() . ',' . $p->listRows())->select();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li>
		<span>共<font color='#009900'><b>%totalRow%</b></font>条数据 30条/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('count_sum', $p->listRows());
        $this->assign('listResult', $listResult);
        return $this->fetch();
    }

}
