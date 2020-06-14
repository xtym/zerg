<?php

namespace app\api\validate;


class TokenGet extends BaseValidator{
    protected $rule=[
        'code'=>'require|IsNotEmpty'
    ];

    protected $message=[
        'code'=>'code不能为空哦'
    ];


}