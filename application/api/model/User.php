<?php

namespace app\api\model;

use think\Db;
use think\Model;

class User extends BaseModel{

    public function address(){
        return $this->hasOne('UserAddress','user_id','id');
    }

    
}