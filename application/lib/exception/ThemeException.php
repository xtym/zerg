<?php

namespace app\lib\exception;

class ThemeException extends BaseException{
    public $code = 404;
    public $msg = '查询的theme主题不存在';
    public $errorCode = 30000;
}