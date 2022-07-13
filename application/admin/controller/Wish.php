<?php

namespace app\admin\controller;

use \think\Db;
use app\admin\logic\page;

class Wish extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 列表
     */
    public function index()
    {
        $username = $this->request->param('username');
        $map = [];
        if ($username) {
            $map['username'] = ['like', "%{$username}%"];
        }
        $count = db("policy_news")->where($map)->count();
        $p = new Page ($count, 30);
        $listResult = db("wish")->where($map)->order('created_at desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $p->setConfig('prev', '上一页');
        $p->setConfig('header', '篇文章');
        $p->setConfig('first', '首 页');
        $p->setConfig('last', '末 页');
        $p->setConfig('next', '下一页');
        $p->setConfig('theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li>
		<span>共<font color='#009900'><b>%totalRow%</b></font>条数据 30条/每页</span></li>");
        $this->assign('page', $p->show());
        $this->assign('count_sum', $p->firstRow);
        $this->assign('listResult', $listResult);
        $this->assign('username', $username);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function detail()
    {
        if ($this->request->isGet()) {
            $id = $this->request->param('id');
            $data = Db::name('wish')->where(['id' => $id])->find();
            $token = $this->request->token('__token__', 'sha1');
            $this->assign('token', $token);
            $this->assign('data', $data);
            return $this->fetch();
        } else {
            return $this->error('操作有误', url('index'));
        }

    }

    /**
     * 添加
     */
    public function save()
    {
        if ($this->request->isPost()) {
            //修改
            $validate = validate('PolicyNews');

            if ($id = $this->request->post('id')) {
                $data = $this->request->post();
                if (!$validate->check($data)) {
                    return json(['data' => '', 'code' => -1, 'msg' => $validate->getError(), 'url' => url('index')]);
                } else {
                    $data = [
                        'title'     => $this->request->post('title'),
                        'pubdate'    => $this->request->post('pubdate'),
                        'content'    => $this->request->post('content'),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ];

                    $img_url = request()->file('img_url');
                    if ($img_url) {
                        $result = model('PolicyNewsModel')->uploadFile($img_url);
                        if ($result['code'] == 0) {
                            $data['img_url'] = "news_logo\\" . $result['data']['file_path'];
                        }
                    }
                    if (Db::name('policy_news')->where(['id' => $id])->update($data)) {
                        return json(['data' => 1, 'code' => 1, 'msg' => '修改成功', 'url' => url('index')]);
                    } else {
                        return json(['data' => '', 'code' => -1, 'msg' => '修改失败', 'url' => url('index')]);
                    }
                }
            } else {
                $data = $this->request->post();
                if (!$validate->check($data)) {
                    return json(['data' => '', 'code' => -1, 'msg' => $validate->getError(), 'url' => url('index')]);
                } else {
                    //活动名称去重
                    if (Db::name('policy_news')->where(['title' => $this->request->post('title')])->count() > 0) {
                        return json(['data' => '', 'code' => -1, 'msg' => '标题重复', 'url' => url('index')]);
                    } else {
                        $data = [
                            'title'     => $this->request->post('title'),
                            'pubdate'    => $this->request->post('pubdate'),
                            'content'    => $this->request->post('content'),
                            'updated_at' => date("Y-m-d H:i:s"),
                            'created_at' => date("Y-m-d H:i:s"),
                        ];
                        $img_url = request()->file('img_url');
                        if ($img_url) {
                            $result = model('PolicyNewsModel')->uploadFile($img_url);
                            if ($result['code'] == 0) {
                                $data['img_url'] = "news_logo\\" . $result['data']['file_path'];
                            }
                        }
                        $insertId = Db::name('policy_news')->insertGetId($data);
                        return json(['data' => $insertId, 'code' => 1, 'msg' => '添加成功', 'url' => url('index')]);
                    }
                }
            }
        } else {
            return json(['data' => '', 'code' => -2, 'msg' => '操作有误', 'url' => url('index')]);
        }
    }

    /**
     * 删除
     */
    public function review()
    {
        if ($this->request->isPost()) {
            $id     = $this->request->post('id');
            $status = $this->request->post('status');
            $update = [
                'updated_at' => date('Y-m-d H:i:s'),
                'status'     => $status,
            ];
            if ($res = Db::name('wish')->where(['id' => $id,'status'=>0])->update($update)) {
                return json(['data' => 0, 'code' => 1, 'msg' => '操作成功', 'url' => url('index')]);
            } else {
                return json(['data' => $res, 'code' => -1, 'msg' => '操作失败', 'url' => url('index')]);
            }
        } else {
            return json(['data' => 0, 'code' => -2, 'msg' => '操作有误', 'url' => url('index')]);
        }
    }
}
