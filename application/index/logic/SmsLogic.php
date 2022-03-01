<?php

namespace app\index\logic;

class SmsLogic
{
    public static function sendCode($mobile,$code)
    {

        $data    = [
            'username' => config('sms.account'),
            'password' => config('sms.password'),
            'content'  => '@1@='.$code,
            'mobile'   => $mobile,
            'tempid'   => config('sms.tplId'),
            'veryCode' => config('sms.veryCode'),
            'msgtype'  => 2,
            'code'     => 'utf-8',
        ];
        $headers = [
            'Content-type:application/x-www-form-urlencoded',
            'Accept:text/plain',
        ];
        $res     = self::requestPostCode(config('sms.httpUrl'), $data, $headers);
        return $res;
    }

    public static function requestPostCode($url = '', $params = '', $headers = '')
    {
        // 初始化curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // post提交方式
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($params));
        // 运行curl
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }
}