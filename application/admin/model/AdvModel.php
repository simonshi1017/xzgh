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
class AdvModel extends Model
{
    public function uploadFile($file){
        $img_patch = 'public/static/index/images/adv_logo';
        if(!is_dir($img_patch)){
            @mkdir($img_patch,0777);
        }
        $info = $file->move(ROOT_PATH . $img_patch);
        if($info){
            return ['code'=>0,'msg'=>'success','data'=>['file_path'=>$info->getSaveName()]];
        }else{
            // 上传失败获取错误信息
            return ['code'=>0,'msg'=>$file->getError(),'data'=>''];
        }
    }
}