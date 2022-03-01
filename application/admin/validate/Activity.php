<?php
// +----------------------------------------------------------------------
// | xz
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class Activity extends Validate
{
    protected $rule = [
        //'__token__' => 'token',
        'name'       => 'require',
        'join_info'  => 'require',
        'rule_info'  => 'require',
        'begin_at'   => 'require',
        'end_at'     => 'require',
        'person_num' => 'require',
        'card_num'   => 'require',
    ];
    protected $message = [
        'name.require'       => '活动名称必须',
        'rule_info.require'  => '活动规则必须',
        'join_info.require'  => '参与方式必须',
        'person_num.require' => '参与人数必须',
        'card_num.require'   => '优惠券数量必须',
        'begin_at.require'   => '开始时间必须',
        'end_at.require'     => '结束时间必须',
        'begin_at.date'      => '开始时间必须为时间格式',
        'end_at.date'        => '结束时间必须为时间格式',
    ];

}