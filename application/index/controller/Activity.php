<?php

namespace app\index\controller;

use app\index\logic\OrcLogic;
use app\index\logic\SmsLogic;
use Cassandra\Date;
use think\Controller;
use think\Cookie;
use think\Db;

class Activity extends Controller
{
    function __construct()
    {
        parent::__construct();
        $user_info = Cookie::get('user_info');
         if (!$user_info) {
             $this->redirect('/wechat/code');
         }
    }

    public function index()
    {
        //活动列表
        $items  = Db::name('activity')
            ->order(['created_at' => 'desc'])
            ->select();
        $now    = date('Y-m-d H:i:s');
        $list   = [];
        $list_1 = []; //未开始
        $list_2 = []; //进行中
        $list_3 = []; //已结束
        foreach ($items as &$item) {
            $begin_at           = $item['begin_at'];
            $item['created_at'] = date('Y-m-d', strtotime($item['created_at']));
            if ($begin_at > $now) { //未开始
                $item['status'] = '未开始';
                $list_1[]       = $item;
            } elseif ($begin_at <= $now && $item['end_at'] > $now) {
                $item['status'] = '进行中';
                $list_2[]       = $item;
            } else {
                $item['status'] = '已结束';
                $list_3[]       = $item;
            }
        }
        $list = array_merge($list, $list_2, $list_1, $list_3);
        $this->assign('list', $list);
        return $this->fetch('index');
    }

    public function detail()
    {
        $id   = $_GET['id'];
        $data = Db::name('activity')->find($id);
        if (!$data['detail_info']) {
            return $this->info();
        } else {
            $this->assign('data', $data);
            return $this->fetch('detail');
        }
    }

    public function info()
    {
        $id                   = $_GET['id'];
        $user_info            = Cookie::get('user_info');
        $openid               = isset($user_info['openid']) ? $user_info['openid'] : 0;
        $data                 = Db::name('activity')->find($id);
        $data['form_fields']  = explode(',', $data['form_fields']);
        $now                  = date('Y-m-d H:i:s');
        $data['is_available'] = 1;
        $data['is_exist']     = 0;
        if (($data['begin_at'] > $now || $data['end_at'] < $now)) {
            $data['is_available'] = 0;
        }
        if ($this->getExistFormUserOfOpenid($id,$openid)) {
            $data['is_exist']     = 1;
        }
        $this->assign('data', $data);
        return $this->fetch('info');
    }

    public function save()
    {
        $user_info = Cookie::get('user_info');
        if ($this->request->isPost()) {
            if (isset($_POST['sms_code'])) {
                $mobile   = $this->request->post('mobile');
                $sms_code = $this->request->post('sms_code');
                if (Db::name('sms_code')
                        ->where('mobile', $mobile)
                        ->where('code', $sms_code)
                        ->count() == 0) {
                    //验证失败
                    return json(['data' => 0, 'code' => 0, 'msg' => '短信验证码有误！']);
                };
            }
            $openid                  = isset($user_info['openid']) ? $user_info['openid'] : 0;
            $activity_id             = $this->request->post('activity_id');
            $activity                = Db::name('activity')->find($activity_id);
            $activity['form_fields'] = explode(',', $activity['form_fields']);
            $check_fields            = $this->checkFormFields();
            foreach ($check_fields as $item) {
                if (in_array($item['id'], $activity['form_fields'])) {
                    $field_val = $this->request->post($item['field_name']);
                    if (!$field_val) {
                        return json(['data' => 0, 'code' => 0, 'msg' => $item['desc'] . '不能为空']);
                    }
                }
            }
            $data = [
                'realname'    => $this->request->post('realname'),
                'sex'         => $this->request->post('sex'),
                'code'        => $this->request->post('code'),
                'email'       => $this->request->post('email'),
                'address'     => $this->request->post('address'),
                'mobile'      => $this->request->post('mobile'),
                'words'       => $this->request->post('words'),
                'union_name'  => $this->request->post('union_name'),
                'activity_id' => $activity_id,
                'openid'      => $openid,
                'unionid'     => isset($user_info['unionid']) ? $user_info['unionid'] : 0,
                'created_at'  => date("Y-m-d H:i:s"),
            ];
            if ($this->getExistFormUserOfOpenid($activity_id,$openid)) {
                return json(['data' => 0, 'code' => 0, 'msg' => '您已参加过活动！']);
            }
            /*   $mobile = $this->request->post('mobile');
               if ($mobile && Db::name('user_form')
                       ->where('activity_id', $activity_id)
                       ->where('mobile', $mobile)
                       ->count() > 0
               ) {
                   return json(['data' => 0, 'code' => 0, 'msg' => '您已参加过活动！']);
               }*/
            $total = Db::name('user_form')->where('activity_id', $activity_id)->count();
            if ($activity['person_num'] <= $total) {
                return json(['data' => 0, 'code' => 0, 'msg' => '很抱歉，参与人数已满，下次早点来哦~']);
            }
            if ($activity['is_checked'] == '是') {
                if ($activity['check_method'] == '文字') {
                    $check_words = request()->post('words');
                    if (!$check_words) {
                        return json(['data' => 0, 'code' => 0, 'msg' => '请输入核验文字']);
                    }
                    if (strpos($check_words, $activity['check_words']) === false) {
                        return json(['data' => 0, 'code' => 0, 'msg' => '验证未通过，请重新提交！']);
                    }
                } else {
                    $img_url = request()->file('img_url');
                    if ($img_url) {
                        $result = model('UserFormModel')->uploadFile($img_url);
                        if ($result['code'] == 0) {
                            $data['img_url'] = "user_form\\" . $result['data']['file_path'];
                            $img_url         = getcwd() . "\\upload\\" . $data['img_url'];
                            $img_url         = str_replace('\\', '/', $img_url);
                            if (!OrcLogic::getWordsBool($img_url, $activity['check_words'])) {
                                return json(['data' => 0, 'code' => 0, 'msg' => '验证未通过，请重新提交！']);
                            }
                        } else {
                            return json(['data' => 0, 'code' => 0, 'msg' => $result['msg']]);
                        }
                    } else {
                        return json(['data' => 0, 'code' => 0, 'msg' => '请上传图片']);
                    }
                }
            }
            Db::name('user_form')->insertGetId($data);
            $card_url = $activity['card_url'] ?: url('index');
            return json(['data' => 0, 'code' => 1, 'msg' => '提交成功', 'url' => $card_url]);
        } else {
            return json(['data' => '', 'code' => -2, 'msg' => 'error']);
        }
    }

    /** 获取access
     * @return array|mixed
     */
    public function sendSmsCode()
    {
        if ($this->request->isPost()) {
            $mobile       = $this->request->post('mobile');
            $activity_id  = $this->request->post('activity_id');
            $captcha_code = $this->request->post('captcha_code');
            if (!captcha_check($captcha_code)) {
                //验证失败
                return json(['data' => 0, 'code' => 0, 'msg' => '验证码有误']);
            };
            $bool = preg_match("/^1((34[0-8]\d{7})|((3[0-3|5-9])|(4[5-7|9])|(5[0-3|5-9])|(66)|(7[2-3|5-8])|(8[0-9])|(9[1|8|9]))\d{8})$/", $mobile);
            if (!$bool) {
                return json(['data' => 0, 'code' => 0, 'msg' => '请输入有效的手机号', 'url' => '']);
            }
            if (!$activity_id) {
                return json(['data' => 0, 'code' => 0, 'msg' => '参数有误', 'url' => '']);
            }
            $code     = str_pad(mt_rand(0, 9999), 4, 0, STR_PAD_LEFT);
            $datetime = date('Y-m-d H:i:s', time() - 1 * 60);
            $sms_code = Db::name('sms_code')->where('mobile', $mobile)->find();
            if ($sms_code) {
                if ($sms_code['updated_at'] > $datetime) {
                    return json(['data' => 0, 'code' => 0, 'msg' => '请1分钟后再试', 'url' => '']);
                } else {
                    //发送验证码
                    $res  = SmsLogic::sendCode($mobile, $code);
                    $data = [
                        "code"       => $code,
                        "updated_at" => date('Y-m-d H:i:s')
                    ];
                    Db::name('sms_code')->where('mobile', $mobile)->update($data);
                }
            } else {
                //发送验证码
                $res  = SmsLogic::sendCode($mobile, $code);
                $data = [
                    "code"        => $code,
                    "mobile"      => $mobile,
                    "activity_id" => $activity_id,
                    "updated_at"  => date('Y-m-d H:i:s'),
                    "created_at"  => date('Y-m-d H:i:s')
                ];
                Db::name('sms_code')->insertGetId($data);
            }
            return json(['data' => 0, 'code' => 1, 'msg' => '发送成功', 'url' => '', 'result' => $res]);
        } else {
            return json(['data' => '', 'code' => -2, 'msg' => '']);
        }
    }

    private function checkFormFields()
    {
        return $fields_arr = [
            [
                'id'         => 1,
                'field_name' => 'realname',
                'desc'       => '姓名',
            ], [
                'id'         => 2,
                'field_name' => 'sex',
                'desc'       => '性别',
            ],
            [
                'id'         => 3,
                'field_name' => 'code',
                'desc'       => '身份证号',
            ],
            [
                'id'         => 4,
                'field_name' => 'email',
                'desc'       => '邮箱',
            ],
            [
                'id'         => 5,
                'field_name' => 'address',
                'desc'       => '地址',
            ],
            [
                'id'         => 6,
                'field_name' => 'company',
                'desc'       => '工作单位',
            ],
            [
                'id'         => 7,
                'field_name' => 'mobile',
                'desc'       => '手机号',
            ],
        ];
    }

    private function getExistFormUserOfOpenid($activity_id, $openid)
    {
        $num = Db::name('user_form')
            ->where('activity_id', $activity_id)
            ->where('openid', $openid)
            ->count();
        return $num > 0 ? true : false;
    }

}
