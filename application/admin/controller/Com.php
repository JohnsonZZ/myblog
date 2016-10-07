<?php
namespace app\admin\controller;
use think\Controller;
class Com extends Controller
{
    public function _initialize(){
		if(session('id') && session('name')){
			
		}elseif(cookie('id') && cookie('name')){
			session('id',cookie('id')) ;
			session('name',cookie('name')) ;
		}else{
			$this->redirect('/admin/login');
		}
    }

}
