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

//banner
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');
//主题
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');
//产品
Route::group('api/:version/product',function(){
    Route::get('/recent','api/:version.Product/getRecent');
    Route::get('/by_category','api/:version.Product/getAllInCategory');
    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']); 
});
// Route::get('api/:version/product/recent','api/:version.Product/getRecent');
// Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
// Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//分类
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');
//token
Route::post('api/:version/token/user','api/:version.Token/getToken');
//地址
Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
//订单
Route::post('api/:version/order','api/:version.Order/placeOrder');
//支付
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');