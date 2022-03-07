<?php
namespace app\index\logic;
use think\Cookie;

class OrcLogic
{


    public static function getAccessToken()
    {
        $file_path = 'access_token/baidu_token.json';
        $token_str = file_get_contents($file_path);
        $token  = '';
        if($token_str){
            $token_arr = json_decode($token_str,JSON_UNESCAPED_UNICODE);
            $expire_time = isset($token_arr['expire_time']) ? $token_arr['expire_time'] : "";
            $token = isset($token_arr['access_token']) ? $token_arr['access_token'] : "";
            if($expire_time > time()){
                $token = isset($token_arr['access_token']) ? $token_arr['access_token'] : "";
            }
        }
        if(!$token){
            $url = 'https://aip.baidubce.com/oauth/2.0/token';
            $post_data['grant_type']       = 'client_credentials';
            $post_data['client_id']        = config('baidu_aip.api_key');
            $post_data['client_secret']   = config('baidu_aip.secret_key');
            $o = "";
            foreach ( $post_data as $k => $v )
            {
                $o.= "$k=" . urlencode( $v ). "&" ;
            }
            $post_data = substr($o,0,-1);
            $res       = request_post($url, $post_data);
            $token_arr = json_decode($res,JSON_UNESCAPED_UNICODE);
            $token_arr['expire_time'] = $token_arr['expires_in'] +time() - 3600*24;
            file_put_contents($file_path,json_encode($token_arr,JSON_UNESCAPED_UNICODE));
            $token = $token_arr['access_token'];
        }
        return $token;
    }
    /** 获取access
     * @return array|mixed
     */
    public static function getWordsBool($imgUrl,$check_words)
    {
        $token  = self::getAccessToken();
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/accurate_basic?access_token=' . $token;
        $img = file_get_contents($imgUrl);
        $img = base64_encode($img);
        $bodys = array(
            'image' => $img
        );
        $res   = request_post($url, $bodys);
        $result = json_decode($res,JSON_UNESCAPED_UNICODE);
        $bool = false;
        if($check_words){
            foreach ($result['words_result'] as $item){
                if(strpos($item['words'],$check_words) !== false){
                    $bool = true;
                    break;
                }
            }
        }else{
            $bool = true;
        }
        return $bool;
    }


}