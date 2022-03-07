<?php
namespace app\index\controller;
use think\Controller;

class Index  extends Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->error('not found');
    }

    private  function _requestPost($url = '', $params = '',$headers='')
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
