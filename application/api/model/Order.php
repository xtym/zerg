<?php

namespace app\api\model;

class Order extends BaseModel{
    protected $hidden=['update_time','delete_time'];
    protected $autoWriteTimestamp=true;


}