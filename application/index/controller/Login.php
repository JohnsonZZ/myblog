<?php
namespace app\index\controller;
use think\Controller;
use think\Loader;
class Login extends Controller
{
	public function login($type = null)
    {
		Loader::import('ThinkSDK.ThinkOauth',EXTEND_PATH,'.class.php');
        empty($type) && $this->error('参数错误');

        $sns = \ThinkOauth::getInstance($type);
        $this->redirect($sns->getRequestCodeURL());
    }
	public function callback($type = null, $code = null){
        (empty($type) || empty($code)) && $this->error('参数错误');
        
        //加载ThinkOauth类并实例化一个对象
        Loader::import('ThinkSDK.ThinkOauth',EXTEND_PATH,'.class.php');
        $sns  = \ThinkOauth::getInstance($type);
		$extend = null;
        $token = $sns->getAccessToken($code , $extend);
        //获取当前登录用户信息
        if(is_array($token)){
            if($type=='github')
            {
                $github   = \ThinkOauth::getInstance('github', $token);
                $data = $github->call('user');

                if($data['ret'] == 0){
                    echo("<h1>恭喜！使用 {$type} 用户登录成功</h1><br>");
                    echo("授权信息为：<br>");
                    dump($token);
                    echo("当前登录用户信息为：<br>");
                    dump($data);
                    //$this->calllogin('qq',$token['openid'],$data['nickname'],$data['figureurl_2']);
                } else {
                    exception("获取github用户信息失败：{$data['msg']}");
                }
            }
        }
    }
}
