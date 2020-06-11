<?php

namespace app\api\controller\v1;

// Request::instance()->get();//获取所有get形式得参数
// Request::instance()->route();//获取路径里得参数
// Request::instance()->post();//获取post形式的参数
// Request::instance()->param();//获取所有参数
// input('param.');//助手函数获取所有param参数
use think\Request;
use think\Validate;
use think\Exception;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;


class Banner
{

    /**
     * @url /banner/:id
     * @http GET
     * @$id 
     */
    public function getBanner($id){
        (new IDMustBePositiveInt())->goCheck();
        $banner = BannerModel::getBannerByID($id);
        if(!$banner){
            throw new BannerMissException();
        }
        $c = config('setting.img_prefix');
        return $banner;
    }
}