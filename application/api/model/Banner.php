<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Banner extends BaseModel{

    protected $hidden=['delete_time','update_time'];

    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerByID($id){
        
        return self::with(['items','items.img'])->find($id);
        // $DB=Db::connect();
        // $result = $DB->query('select * from banner_item where banner_id = ?',[$id]);
        // $result = $DB->table('banner_item')->where('banner_id','=',$id)->select();
        // return $result;
    }
}