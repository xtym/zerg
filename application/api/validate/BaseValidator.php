<?php

namespace app\api\validate;

use think\Request;
use think\Validate;
use think\Exception;

class BaseValidator extends Validate{

    public function goCheck(){
        $params = Request::instance()->param();
        $result = $this->check($params);
        if(!$result){
            $error=$this->getError();
            throw new Exception($error);
        }else{
            return true;
        }
    }
}