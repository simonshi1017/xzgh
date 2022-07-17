<?php

namespace app\admin\controller;

use \think\Db;
use app\admin\logic\page;

class Meeting extends Common
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
        $name = $this->request->param('name');
        $map  = [];
        if ($name) {
            $map['name'] = ['like', "%{$name}%"];
        }
        $count      = db("meeting")->where($map)->count();
        $p          = new Page ($count, 30);
        $listResult = db("meeting")->where($map)->order('created_at desc')->limit($p->firstRow . ',' . $p->listRows)->select();
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
        $this->assign('name', $name);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        $token = $this->request->token('__token__', 'sha1');
        $this->assign('token', $token);
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function edit()
    {
        if ($this->request->isGet()) {
            $id                          = $this->request->param('id');
            $meetingData                = Db::name('meeting')->where(['id' => $id])->find();
            $meetingData['form_fields'] = explode(',', $meetingData['form_fields']);
            $token                       = $this->request->token('__token__', 'sha1');
            $this->assign('token', $token);
            $this->assign('meetingData', $meetingData);
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
            $validate = validate('meeting');

            if ($id = $this->request->post('id')) {
                $data = $this->request->post();
                if (!$validate->check($data)) {
                    return json(['data' => '', 'code' => -1, 'msg' => $validate->getError(), 'url' => url('index')]);
                } else {
                    $form_fields = $data['form_fields'];

                    $data        = [
                        'name'        => $this->request->post('name'),
                        'begin_at'    => $this->request->post('begin_at'),
                        'end_at'      => $this->request->post('end_at'),
                        'is_enter'    => $this->request->post('is_enter'),
                        'person_num'  => $this->request->post('person_num'),
                        'race_notice'   => $this->request->post('race_notice'),
                        'rule_info'   => $this->request->post('rule_info'),
                        'join_info'   => $this->request->post('join_info'),
                        'detail_info' => $this->request->post('detail_info'),
                        'personal_item' => $this->request->post('personal_item'),
                        'group_item' => $this->request->post('group_item'),
                        'extra_info' => $this->request->post('extra_info'),
                        'form_fields' => is_array($form_fields) ? implode(',', $form_fields) : '',
                        'updated_at'   => date("Y-m-d H:i:s"),
                    ];

                    $img_url = request()->file('img_url');
                    if ($img_url) {
                        $result = model('meetingModel')->uploadFile($img_url);
                        if ($result['code'] == 0) {
                            $data['img_url'] = "meeting_logo\\" . $result['data']['file_path'];
                        }
                    }
                    $rule_img_url = request()->file('banner_url');
                    if ($rule_img_url) {
                        $result = model('MeetingModel')->uploadFile($rule_img_url);
                        if ($result['code'] == 0) {
                            $data['banner_url'] = "meeting_logo\\" . $result['data']['file_path'];
                        }
                    }
                    if (Db::name('meeting')->where(['id' => $id])->update($data)) {
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
                    if (Db::name('meeting')->where(['name' => $this->request->post('name')])->count() > 0) {
                        return json(['data' => '', 'code' => -1, 'msg' => '活动已存在', 'url' => url('index')]);
                    } else {
                        $form_fields = $data['form_fields'];
                        $data        = [
                            'name'        => $this->request->post('name'),
                            'begin_at'    => $this->request->post('begin_at'),
                            'end_at'      => $this->request->post('end_at'),
                            'is_enter'    => $this->request->post('is_enter'),
                            'person_num'  => $this->request->post('person_num'),
                            'race_notice'   => $this->request->post('race_notice'),
                            'rule_info'   => $this->request->post('rule_info'),
                            'join_info'   => $this->request->post('join_info'),
                            'detail_info' => $this->request->post('detail_info'),
                            'personal_item' => $this->request->post('personal_item'),
                            'group_item' => $this->request->post('group_item'),
                            'extra_info' => $this->request->post('extra_info'),
                            'form_fields' => is_array($form_fields) ? implode(',', $form_fields) : '',
                            'updated_at'  => date("Y-m-d H:i:s"),
                            'created_at'  => date("Y-m-d H:i:s"),
                        ];
                        $img_url     = request()->file('img_url');
                        if ($img_url) {
                            $result = model('meetingModel')->uploadFile($img_url);
                            if ($result['code'] == 0) {
                                $data['img_url'] = "meeting_logo\\" . $result['data']['file_path'];
                            }
                        }
                        $rule_img_url = request()->file('banner_url');
                        if ($rule_img_url) {
                            $result = model('MeetingModel')->uploadFile($rule_img_url);
                            if ($result['code'] == 0) {
                                $data['banner_url'] = "meeting_logo\\" . $result['data']['file_path'];
                            }
                        }
                        $insertId = Db::name('meeting')->insertGetId($data);
                        return json(['data' => $insertId, 'code' => 1, 'msg' => '添加成功', 'url' => url('index')]);
                    }
                }
            }
        } else {
            return json(['data' => '', 'code' => -2, 'msg' => '', 'url' => url('index')]);
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        if ($this->request->isGet()) {
            $id = $this->request->get('id');
            if (Db::name('meeting')->where(['id' => $id])->delete()) {
                return json(['data' => 0, 'code' => 1, 'msg' => '删除成功', 'url' => url('index')]);
            } else {
                return json(['data' => 0, 'code' => -1, 'msg' => '删除失败', 'url' => url('index')]);
            }
        } else {
            return json(['data' => 0, 'code' => -2, 'msg' => '', 'url' => url('index')]);
        }
    }

    /**
     * 删除
     */
    public function userlist()
    {
        $realname = $this->request->param('realname');
        $map  = [];
        if ($realname) {
            $map['realname'] = ['like', "%{$realname}%"];
        }
        $count      = db("user_form")->where($map)->count();
        $p          = new Page ($count, 30);
        $listResult = db("user_form")->where($map)->order('created_at desc')->limit($p->firstRow . ',' . $p->listRows)->select();
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
        $meetingList = db("meeting")->field(['id','name'])->select();
        $actArr = [];
        foreach($meetingList  as $item){
            $actArr[$item['id']] = $item['name'];
        }
        foreach($listResult  as &$item){
            $item['meeting_name'] = isset($actArr[$item['meeting_id']]) ? $actArr[$item['meeting_id']] : '';
        }
        $this->assign('listResult', $listResult);
        $this->assign('realname', $realname);
        return $this->fetch();
    }
}
