<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];
// 引入路由类
use think\Route;
//后台
// 注册路由到模块admin下的Login控制器下的index方法
// 后台管理员登录页面
// 路由分组
// Route::group(['prefix' => 'admin'], function () {

//     //登录
//     Route::rule('login','admin/Login/index');
// });

//后台登录
Route::rule('login','admin/Login/index');

// 前台首页
// Route::rule('index','index/Index/index');

