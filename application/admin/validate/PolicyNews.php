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

class PolicyNews extends Validate
{
    protected $rule = [
        //'__token__' => 'token',
        'title'    => 'require',
        'pubdate'  => 'require',
    ];
    protected $message = [
        'title.require'    => '标题必须',
        'pubdate.require'  => '发布日期必须',
    ];

}