<?php

namespace app\api\model;

class ProductImage extends BaseModel{
    protected $hidden=['create_time','update_time','delete_time'];

    public function img(){
        return $this->belongsTo('Image','img_id','id');
    }
}