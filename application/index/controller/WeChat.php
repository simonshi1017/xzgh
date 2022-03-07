<?php
namespace app\index\controller;
use think\Controller;
use app\index\logic\WeChatLogic;
use think\Cookie;

define("TOKEN", "weixin");
class WeChat  extends Controller
{
    private  $token ='weixin';

    public function code(){
        $app_id      = config('wx_app.app_id');
        $redirecturl = urlencode("http://".request()->host()."/wechat/get_code");
        $url         = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.$redirecturl.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        $this->redirect($url);
    }
    public function getCode(){
        if($_GET['code']){
            $code = $_GET['code'];
            list($access_token,$openid,$unionid) =  WeChatLogic::getAccessToken($code);
            if(!$access_token){
                die('授权失败');
            }
            $res                                 =  WeChatLogic::getUserInfo($access_token,$openid,$unionid);
            if(is_array($res)){
                $wxUser = db('wx_user');
                $user_data = $wxUser->where('openid',$res['openid'])->find();
                $data = [
                    'nickname'   => $res['nickname'],
                    'openid'     => $res['openid'],
                    'language'   => isset($res['language']) ? $res['language'] : '',
                    'city'       => isset($res['city']) ? $res['city'] : '',
                    'country'    => isset($res['country']) ? $res['country'] : '',
                    'unionid'    => isset($res['unionid']) ? $res['unionid'] : '',
                    'updated_at' => date("Y-m-d H:i:s"),
                ];
                if($user_data){
                    $wxUser->where('openid',$res['openid'])->update($data);
                    $user_data = array_merge($user_data,$data);
                }else{
                    $data['created_at'] = date("Y-m-d H:i:s");
                    $id = $wxUser->where('openid',$res['openid'])->insertGetId($data);
                    $user_data = array_merge(['id'=>$id],$data);
                }
                Cookie::set('user_info',$user_data,3600*24);
                $this->redirect('/activity/index');
            }else{
                $this->error('授权失败');
                die('授权失败');
            }
        }
    }
    public function index(){
        if(isset($_GET['echostr'])){
            $this->valid();//微信验证
        }else{
            $this->responseMsg();//调用微信接口进行应答
        }
    }
    public function valid(){
        //valid signature , option
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function getToken()
    {
       echo $this->token;exit;
    }
    public function getUserInfo()
    {
        https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
    }
    public function responseMsg(){
        //get post data, May be due to the different environments
        $postStr = $_POST;
        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $event = trim($postObj->Event);
            $time = time();
            $message = M("Wx_auto_message");
            $postObj_encode = json_encode($postObj);
            $postObj_decode = json_decode($postObj_encode);
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Event><![CDATA[%s]]></Event>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if($event == 'subscribe'){
                $message_content = $this->return_auto_message_from_keywords($event,'',$postObj_decode);
                $msgType = "text";
                $contentStr = $message_content;
                $this->add_wxusers($postObj_decode); //插入数据库
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }elseif($event == 'unsubscribe'){
                $this->save_wxusers($postObj_decode,1); //插入数据库
            }
            if(!empty( $keyword ))
            {
                if($keyword == '人工' || $keyword == '客服'){
                    $textTpl = "<xml>
                                     <ToUserName><![CDATA[%s]]></ToUserName>
                                     <FromUserName><![CDATA[%s]]></FromUserName>
                                     <CreateTime>%s</CreateTime>
                                     <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                                 </xml>";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
                    echo $resultStr;
                }else{
                    $msgType = "text";
                    /*  $back_url='http://weixin.kpi100.com/index.php/Index/auth_login';
                      $auth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx400ea61a8cd2c7a0&redirect_uri='.$back_url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
                      $contentStr = "<a href='".$auth_url."'>点击生成</a>";*/
                    $message_content = $this->return_auto_message_from_keywords('text',$keyword,$postObj_decode);
                    if($message_content){
                        $contentStr = $message_content;
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }
                }
            }else{
                echo "Input something...";
            }
        }else {
            echo "";
            exit;
        }
    }
    private function checkSignature(){
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}
