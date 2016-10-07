<?php
namespace app\admin\validate;

use think\Validate;

class Admins extends Validate
{
    protected $rule = [
        'name'  =>  'require',
        'pwd' =>  'require',
		'__token__'  => 'require|token',
    ];
	protected $message  =   [
        'name.require' => '账号必须',
        'pwd.require'   => '密码必须',
        '__token__.token' => '系统错误，请重新登录',
    ];

}