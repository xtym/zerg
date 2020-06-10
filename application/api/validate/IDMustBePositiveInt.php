<?php

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidator
{
    protected $rule=[
        'id'=>'require|IsPositiveInteger'
    ];

    //$data传入的验证参数数组,$field当前验证的字段名,$value当前该字段的值
    protected function IsPositiveInteger($value,$rule='',$data='',$field='')
    {
        if(is_numeric($value) && is_int($value+0) && ($value+0)>0){
            return true;
        }else{
            return $field.'必须是正整数';
        }
    }
}