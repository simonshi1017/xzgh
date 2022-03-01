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
namespace app\admin\model;

use think\Model;

class AdminModel extends Model
{
    // 确定链接表名
    protected $table = 'gh_admin';
    
    /**
     * 根据用户名获取管理员信息
     * @param $name
     */
    public function findUserByName($username)
    {
        return $this->where('username', $username)->find();
    }
    

}