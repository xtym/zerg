<?php

namespace app\lib\exception;

class BannerMissException extends BaseException{
    public $code = 404;
    public $msg = 'banner不存在';
    public $errorCode = 40000;
}