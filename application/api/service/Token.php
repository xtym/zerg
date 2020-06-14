<?php

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use Exception;
use think\Cache;
use think\Request;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;

class Token{


    public static function generateToken(){
        $randChars = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('secure.token_salt');
        //md5加密成令牌
        return md5($randChars.$timestamp.$salt);
    }

    public static function getCurrentTokenVar($key){
        $token = Request::instance()->header('token');
        $var = Cache::get($token);
        if(!$var){
            throw new TokenException();
        }
        if(!is_array($var)){
            $var = json_decode($var,true);
        }
        if(!array_key_exists($key,$var)){
            throw new Exception('尝试获取的token变量不存在');
        }
        return $var[$key];
    }

    public static function getCurrentUID(){
       return self::getCurrentTokenVar('uid');
    }

    public static function getCurrentScope(){
        return self::getCurrentTokenVar('scope');
    }

    public static function needPrimaryScope(){
        $scope = self::getCurrentScope();
    
        $scope = self::getCurrentScope();
        if(!$scope){
            throw new TokenException();
        }
        if($scope<ScopeEnum::user){ 
            throw new ForbiddenException();
        }
        return true;
    }

    public static function needExclusiveScope(){
        $scope = self::getCurrentScope();
        if(!$scope){
            throw new TokenException();
        }
        if($scope!=ScopeEnum::user){ 
            throw new ForbiddenException();
        }
        return true;
    }

    /**
     * 检测传入的uid是否和当前用户uid一致
     */
    public static function isValidOperate($checkedUID){
        if(!$checkedUID){
            throw new Exception('检查UID时,必须传入一个被检查的UID');
        }
        $currentOperateUID=self::getCurrentUID();
        if($currentOperateUID!=$checkedUID){
            return false;
        }
        return true;
    }
}