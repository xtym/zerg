<?php

namespace app\lib\exception;

use think\Exception;

class ParameterException extends BaseException{

    public $code=400;//HTTP状态码
    public $msg='参数错误';//错误消息
    public $errorCode=10000;//自定义错误码

}