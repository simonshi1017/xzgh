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

class Adv extends Validate
{
    protected $rule = [
        //'__token__' => 'token',
        'name'  => 'require',
        'url'   => 'require',
    ];
    protected $message = [
        'name.require' => '名称必须',
        'url.require'  => '跳转链接必须',
    ];

}