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


//路由表达式,路由地址,请求类型,路由参数(数组),变量规则(数组)
//路由地址三段式:模块名+控制器名+方法名

use think\Route;


Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
