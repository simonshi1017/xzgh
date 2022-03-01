<?php
namespace app\index\logic;
use think\Cookie;

class WeChatLogic
{
    /** 获取access
     * @return array|mixed
     */
    public static function getAccessToken($code)
    {
        $app_id     = config('wx_app.app_id');
        $app_secret = config('wx_app.app_secret');
        $url        = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$app_id}&secret={$app_secret}&code={$code}&grant_type=authorization_code";
        $res = curl_http($url);
        //获取用户信息
        $access_token    = isset($res['access_token']) ? $res['access_token']: '';
        $openid    = isset($res['openid']) ? $res['openid']: '';
        $unionid    = isset($res['unionid']) ? $res['unionid']: '';
        return [$access_token,$openid,$unionid];
    }


    /** 获取access
     * @return array|mixed
     */
    public static function getUserInfo($access_token,$openid,$unionid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $res = curl_http($url,'get');
        return $res;
    }
}