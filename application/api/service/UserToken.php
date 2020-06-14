<?php

namespace app\api\service;

use Exception;
use WeChatException;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;

class UserToken extends Token{


    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxCode;
    protected $LoginUrl;


    function __construct($code)
    {
        $this->wxCode = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->LoginUrl = sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$code);
    }

    public function get(){
        $result = curl_get($this->LoginUrl);
        $wxResult = json_decode($result,true);
        if(empty($wxResult)){//openId获取失败
            throw new Exception('获取session_key或openID时异常,微信内部错误');
        }else{
            $loginFail = array_key_exists('errcode',$wxResult);
            if($loginFail){
                $this->proccessLoginError($wxResult);
            }else{
                return $this->grantToken($wxResult);
            }
        }
          
    }

    private function grantToken($wxResult){
        $openid=$wxResult['openid'];
        $user= UserModel::where('openid','=',$openid)->find();
        if($user){
            $uid= $user->id;
        }else{
            $uid = $this->newUser($openid);
        }
        //生成缓存value
        $cachedValue= $this->prepareCachedValue($wxResult,$uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }


    private function saveToCache($cachedValue){
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $result = cache($key,$value,$expire_in);
        if(!$result){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode' =>10005
            ]);
        }
        return $key;
    }

    private function newUser($openid){
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    private function prepareCachedValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] =$uid;
        $cachedValue['scope'] =ScopeEnum::user;
        return $cachedValue;
    }


    private function proccessLoginError($wxResult){
        throw new WeChatException([
            'errorCode'=>$wxResult['errcode'],
            'msg'=>$wxResult['errmsg']
        ]);
    }

}