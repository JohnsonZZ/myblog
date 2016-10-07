<?php
use think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//admin login route
Route::get('admin/login','admin/Login/index'); // 定义登录请求的路由规则
Route::get('admin/forgot','admin/Login/forgot'); // 定义忘记密码路由规则
Route::post('admin/login','admin/Login/check'); // 定义登录提交的路由规则
Route::get('admin/quit','admin/Login/quit'); // 定义登录提交的路由规则
//end route

//food route
Route::get('food$','Food/index');
Route::get('food/:id','Food/food');


//food route
Route::get('health$','Health/index');
Route::get('health/:id','Health/health');

Route::get('message$','Message/index');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
