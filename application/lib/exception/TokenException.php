<?php

namespace app\lib\exception;

class TokenException extends BaseException{

    public $code=401;//HTTP状态码
    public $msg='token已过期,或无效token';//错误消息
    public $errorCode=10001;//自定义错误码
}