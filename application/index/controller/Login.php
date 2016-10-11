<?php
namespace app\index\controller;
use think\Controller;
use think\Loader;
use think\Cookie;
use app\index\model\Users;
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
                $result = $github->call('user');
                if($result){
					$Users =  new Users();
					$user = $Users->field('id,nick,avatar,update_time')->where('openid',$result['id'])->find(); 
					if($user){
						$user->avatar = $result['avatar_url'];
						$Users->save();
					}else{
						$data['nick'] = $result['login'];
						$data['type'] = $type;
						$data['openid'] = $result['id'];
						$data['avatar'] = $result['avatar_url'];
						$user->data($data);
						$Users->save();
					}
					Cookie::init(['prefix'=>'d_','expire'=>604800]);
					Cookie::set('id',$user->id);
					Cookie::set('nick',$result['login']);
					Cookie::set('avatar',$result['avatar_url']);
					$this->redirect('/');
                } else {
                    exception("获取github用户信息失败：{$data['msg']}");
                }
            }
        }
    }
}
