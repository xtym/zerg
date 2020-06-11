<?php

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidator
{
    protected $rule=[
        'id'=>'require|IsPositiveInteger'
    ];

    protected $message=[
        'id' => 'id必须是正整数'
    ];
}