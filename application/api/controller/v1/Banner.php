<?php

namespace app\api\controller\v1;

// Request::instance()->get();//获取所有get形式得参数
// Request::instance()->route();//获取路径里得参数
// Request::instance()->post();//获取post形式的参数
// Request::instance()->param();//获取所有参数
// input('param.');//助手函数获取所有param参数

use app\api\validate\IDMustBePositiveInt;
use think\Request;
use think\Validate;

class Banner
{

    /**
     * @url /banner/:id
     * @http GET
     * @$id 
     */
    public function getBanner($id){

        $validator = new IDMustBePositiveInt();
        $validator->goCheck();
        
        
    }
}