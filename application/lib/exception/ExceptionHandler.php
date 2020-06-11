<?php

namespace app\lib\exception;

use think\exception\Handle;
use think\exception\HttpException;
use Exception;
use think\Request;
use think\Log;

class ExceptionHandler extends Handle{

    private $code;
    private $msg;
    private $errorCode;

    public function render(Exception $e){

        if($e instanceof BaseException){//自定义异常
            $this->code = $e->code; 
            $this->msg = $e->msg; 
            $this->errorCode = $e->errorCode; 
        }else{
            if(config('app_debug')){//如果是调试模式就返回tp5默认错误信息
                return parent::render($e);
            }else{
                $this->code = 500;
                $this->msg = '服务器内部错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
            
        }
        $requestUrl=Request::instance()->url();
        $result = [
            'msg' =>$this->msg,
            'error_code'=>$this->errorCode,
            'request_url'=>$requestUrl
        ];
        return json($result,$this->code);

    }


    private function recordErrorLog(Exception $e){
        if(!config('app_debug')){//如果是开发环境,就单独开启内部错误的日志
            Log::init([
                'type'  => 'File',
                // 日志保存目录
                'path'  => LOG_PATH,
                // 日志记录级别
                'level' => ['error'],
            ]);
        }
        Log::record($e->getMessage(),'error');
    }

}