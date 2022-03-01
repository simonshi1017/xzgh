<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '/'   => ['index/index', ['method' => 'get']],
    '[wechat]'     => [
        'index'   => ['WeChat/index', ['method' => 'get']],
        'get_code'   => ['WeChat/getCode', ['method' => 'get']],
        'code'   => ['WeChat/code', ['method' => 'get']],
        'get_access_token'   => ['WeChat/getAccessToken', ['method' => 'get']],
        'get_user_info'   => ['WeChat/getUserList', ['method' => 'get']],
    ],
    '[activity]'     => [
        'index'     => ['Activity/index', ['method' => 'get']],
        'detail'    => ['Activity/detail', ['method' => 'get']],
        'info'      => ['Activity/info', ['method' => 'get']],
        'save'      => ['Activity/save', ['method' => 'post']],
        'send_code' => ['Activity/sendSmsCode', ['method' => 'post']],
    ],
    '[captcha]'     => [
        ''   => ['index/captcha', ['method' => 'get']],
    ],
];
