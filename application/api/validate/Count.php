<?php

namespace app\api\validate;


class Count extends BaseValidator{
    protected $rule=[
        'count'=>'IsPositiveInteger|between:1,15'
    ];

    protected $message=[
        'count'=>'count必须在1到15之间'
    ];


}