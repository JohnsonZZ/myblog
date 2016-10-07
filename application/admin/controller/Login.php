<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Admins;
use app\admin\model\Logs;
use think\Loader;
class login extends Controller
{
    public function index()
    {
		$logs = new Logs();
		$log = $logs->paginate(13);
		return $this->fetch('',['log'=>$log]);
    }
	public function check()
    {
		$admins = new Admins();
		$data = Request::instance()->param();
		$validate = Loader::validate('Admins');

		if(!$validate->check($data)){
			$this->error($validate->getError());
		}
		
		
		$admin = $admins->field('id,name,pwd,salt')->where('name',$data['name'])->find();
		if($admin){
			if($admin['pwd'] == sha1($data['pwd'].$admin['salt'])){
				if(isset($data['remember'])){
					cookie('id',$admin['id'],604800);
					cookie('name',$admin['name'],604800);
				}
				session('id', $admin['id']);
				session('name', $admin['name']);
				addlog('登录成功');
				$this->redirect('/admin');
			}else{
				$this->error('密码不正确');
			}
				
		}else{
			$this->error('账号不存在');
		}
	
    }
	public function quit(){
		session(null, 'think');
		cookie(null, 'w_');
		$this->redirect('/admin/login');
	}
	public function forgot(){
		return view();
	}
}
