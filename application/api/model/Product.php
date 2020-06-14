<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Product extends BaseModel{

    //隐藏字段器
    protected $hidden=['pivot','create_time','delete_time','update_time','category_id','from'];

    //读取器
    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

    //关联器
    public function imgs(){//关联productImage表
        return $this->hasMany('ProductImage','product_id','id');
    }
    public function properties(){//关联ProductProperty表
        return $this->hasMany('ProductProperty','product_id','id');
    }

    //业务处理
    public static function getMostRecent($count){
        return self::limit($count)->order('create_time desc')->select();
    }
    
    public static function getProductsByCategoryID($id){
        return self::where('category_id','=',$id)->select();
    }

    public static function getProductDetail($id){
        return self::with([
            'imgs'=>function($query){
                $query->with(['img'])->order('order','asc');
            }
        ])
        ->with(['properties'])
        ->find($id);
    }
}