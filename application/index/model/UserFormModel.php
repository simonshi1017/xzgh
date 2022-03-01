<?php
namespace app\index\model;
use think\Log;
use think\Model;
class UserFormModel extends Model
{
    public function uploadFile($file){
        $img_patch = 'public/upload/user_form';
        $info = $file->move(ROOT_PATH . $img_patch);
        if($info){
            return ['code'=>0,'msg'=>'success','data'=>['file_path'=>$info->getSaveName()]];
        }else{
            return ['code'=>2,'msg'=>$file->getError(),'data'=>''];
        }
    }
}