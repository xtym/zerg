<?php
namespace app\lib\exception;

class SuccessMessage extends BaseException{
    public $code = 201;
    public $msg = '成功';
    public $errorCode = 0;
}