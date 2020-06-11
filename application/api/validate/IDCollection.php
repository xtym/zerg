<?php

namespace app\api\validate;


class IDCollection extends BaseValidator
{
    protected $rule=[
        'ids'=>'require|checkIDs'
    ];

    protected $message=[
        'ids'=>'ids必须是以逗号分割的正整数'
    ];

    protected function checkIDs($value){
        $values=explode(',',$value);
        if(empty($values)){
            return false;
        }
        foreach ($values as $id){
            if(!$this->IsPositiveInteger($id)){
                return false;
            }
        }
        return true;
    }

    
}