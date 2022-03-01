<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**处理字符串
 * @param $str
 * @return string
 */
function get_value($str){
    return htmlspecialchars(trim($str));
}

/**处理时间
 * @param $the_time
 * @return string
 */
function time_tran($the_time,$short=false) {
    $now_time = date("Y-m-d H:i:s", time());
    //echo $now_time;
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        if($short){
            return date('m月d日',$show_time);
        }else{
            return $the_time;
        }
    } else {
        if ($dur < 60) { //1分钟内
            return $dur . '秒前';
        } else {
            if ($dur < 3600) { //1小时内
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) { //24 小时内
                    return floor($dur / 3600) . '小时前';
                } else {
              /*      if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {*/
                        if($short){
                            return date('m月d日',$show_time);
                        }else{
                            return $the_time;
                        }
                    //}
                }
            }
        }
    }
}

/**识别中文路径
 * @param $filepath
 * @return array
 */
function path_info($filepath){
    $path_parts = array();
    $path_parts ['dirname'] = rtrim(substr($filepath, 0, strrpos($filepath, '/')),"/")."/";
    $path_parts ['basename'] = ltrim(substr($filepath, strrpos($filepath, '/')),"/");
    $path_parts ['extension'] = substr(strrchr($filepath, '.'), 1);
    $path_parts ['filename'] = ltrim(substr($path_parts ['basename'], 0, strrpos($path_parts ['basename'], '.')),"/");
    return $path_parts;
}


/**处理时间
 * @param $the_time
 * @return string
 */
function time_create_tran($the_time,$create_date,$short=false) {
    $now_time = date("Y-m-d H:i:s", time());
    //echo $now_time;
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        if($short){
            if($show_time){
                return date('m月d日',$show_time);
            }else{
                return date('m月d日',strtotime($create_date));
            }
        }else{
            if($show_time){
                return $the_time;
            }else{
                return $create_date;
            }
        }
    } else {
        if ($dur < 60) { //1分钟内
            return $dur . '秒前';
        } else {
            if ($dur < 3600) { //1小时内
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) { //24 小时内
                    return floor($dur / 3600) . '小时前';
                } else {
              /*      if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {*/
                        if($short){
                            if($show_time){
                                return date('m月d日',$show_time);
                            }else{
                                return date('m月d日',strtotime($create_date));
                            }
                        }else{
                            if($show_time){
                                return $the_time;
                            }else{
                                return $create_date;
                            }
                        }
                    //}
                }
            }
        }
    }
}

/**数字大
 * @param $number
 * @return string
 */
function daxie($number){
    $number=substr($number,0,2);
    $arr=array("零","一","二","三","四","五","六","七","八","九");
    if(strlen($number)==1){
        $result=$arr[$number];
    }else{
        if($number==10){
            $result="十";
        }else{
            if($number<20){
                $result="十";
            }else{
                $result=$arr[substr($number,0,1)]."十";
            }
            if(substr($number,1,1)!="0"){
                $result.=$arr[substr($number,1,1)];
            }
        }
    }
    return $result;
}
/**删除所有空格
 * @param $str
 * @return mixed
 */
function trimall($str)//删除空格
{
    $qian = array(" ","　","\t","\n","\r","●","·","�");$hou=array("","","","","","","","");
    return str_replace($qian,$hou,$str);
}

// 过滤掉emoji表情
function filterEmoji($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);
    return stripslashes(str_replace('📣','',$str));
}

/**截取字符串
 * @param $str
 * @param $len
 * @param string $suffix
 * @return string
 */
function cut_str($str,$start=0,$len,$suffix="..."){
    if(function_exists('mb_substr')){
        if(mb_strlen($str) > $len){
            $str= mb_substr($str,$start,$len).$suffix;
        }else{
            $str= mb_substr($str,$start,$len);
        }
        return $str;
    }else{
        if(strlen($str) > $len){
            $str= substr($str,$start,$len).$suffix;
        }else{
            $str= substr($str,$start,$len);
        }
        return $str;
    }
}

/**
 * 统一返回信息
 * @param $code
 * @param $data
 * @param $msge
 */
function msg($code, $data, $msg)
{
    return json_encode(compact('code', 'data', 'msg'),JSON_UNESCAPED_UNICODE);
}


/**过滤英文标点符号 过滤中文标点符号
 * @param $text
 * @return string
 */
function filter_mark($text){
    $text = filterEmoji(trim($text));
    if(trim($text)=='')return '';
    $text=preg_replace("/[[:punct:]\s]/",' ',$text);
    $text=urlencode($text);
    $text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",' ',$text);
    $text=urldecode($text);
    $de = array(" ","　"," ","\t","\n","\r");
    return str_replace($de,'', $text);
}
/**
 * 取微博数据的话题
 */
function get_weibo_topic($title,&$topic_arr){
    preg_match('/#(.*?)#/msi',$title, $mc);
    if(count($mc) > 0 ){
        array_push($topic_arr,$mc[0]);
        $topic_arr = array_unique($topic_arr);
        $title = str_replace($mc[0],'',$title);
        return get_weibo_topic($title,$topic_arr);
    }
}
/**检查账号是不是有效
 * @param int $uid
 * @return bool
 * @throws \think\Exception
 * @throws \think\exception\DbException
 */
function check_user_valid($uid){
    $map = ['uid'=>$uid];
    try{
        $user_vaild_data = db('user')->where($map)->field('is_lock,valid_sdate,valid_edate,is_monitor_login')->find();
        if($user_vaild_data['is_lock'] ==1){ //用户被锁定后不能登录
            return false;
        }
        if($user_vaild_data['is_monitor_login'] == 0 ){ //用户已不能登录
            return false;
        }
        $expire_status = 0;
        if($user_vaild_data['valid_sdate'] && $user_vaild_data['valid_edate']){
            if($user_vaild_data['valid_edate'] < time()){
                $expire_status = 1; //有效期已过
            }elseif($user_vaild_data['valid_sdate'] > time()){
                $expire_status = 2; //未生效
            }
        }elseif($user_vaild_data['valid_sdate']){ //只有账号开始时间
            if($user_vaild_data['valid_sdate'] > time()){
                $expire_status = 2; //未生效
            }
        }elseif($user_vaild_data['valid_edate']){ //只有账号结束时间
            if($user_vaild_data['valid_edate'] < time()){
                $expire_status = 1; //有效期已过
            }
        }
        if($expire_status == 1){
            return false;
        }elseif ($expire_status == 2){
            return false;
        }
        return true;
    }catch (Exception $e){
        return false;
    }
}

/**账号有效期
 * @param int $uid
 * @return bool
 * @throws \think\Exception
 * @throws \think\exception\DbException
 */
function show_user_valid($userinfo,$channel='all'){
    try{
        if($channel == 'business'){
            return ceil(($userinfo['business_valid_edate']-time())/86400);
        }else{
            return ceil(($userinfo['valid_edate']-time())/86400);
        }
    }catch (Exception $e){
        return 0;
    }
}

/**自定义排序
 * @param $arrays
 * @param $sort_key
 * @param int $sort_order
 * @param int $sort_type
 * @return bool
 */
function custom_array_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    $sort_array = array_column($arrays,$sort_key);
    array_multisort($sort_array,$sort_order,$arrays);
    return $arrays;
}

 function curl_http($url,$method='get',$params=array(), $timeout=10, $log=1){
    $ch = curl_init();
    if($method == 'get'){
        if (is_array($params) & $params ) {
            $url = $url . '?' . http_build_query($params);
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // 允许 cURL 函数执行的最长秒数
    if($method == 'post') {
         curl_setopt($ch, CURLOPT_POST, 1); //POST
         curl_setopt($ch, CURLOPT_POSTFIELDS, $params); //post数据
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data,JSON_UNESCAPED_UNICODE);
}


/**
 * 发起http post请求(REST API), 并获取REST请求的结果
 * @param string $url
 * @param string $param
 * @return - http response body if succeeds, else false.
 */
function request_post($url = '', $param = '',$headers='')
{
    if (empty($url) || empty($param)) {
        return false;
    }

    $postUrl = $url;
    $curlPost = $param;
    // 初始化curl
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $postUrl);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    if($headers){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    // 要求结果为字符串且输出到屏幕上
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // post提交方式
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    // 运行curl
    $data = curl_exec($curl);
    curl_close($curl);

    return $data;
}