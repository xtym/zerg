<?php

namespace app\api\validate;

use think\Exception;

use think\Request;
use think\Validate;
use app\lib\exception\ParameterException;

class BaseValidator extends Validate{

    public function goCheck(){
        $params = Request::instance()->param();
        $result = $this->check($params);
        if(!$result){//验证不通过,抛出自定义异常
            $e = new ParameterException([
                'msg'=>$this->getError()
            ]);
            throw $e;
        }else{
            return true;
        }
    }


    //$data传入的验证参数数组,$field当前验证的字段名,$value当前该字段的值
    protected function IsPositiveInteger($value,$rule='',$data='',$field='')
    {
        if(is_numeric($value) && is_int($value+0) && ($value+0)>0){
            return true;
        }else{
            return false;
        }
    }

    //非空验证
    protected function IsNotEmpty($value,$rule='',$data='',$field=''){
        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }

    protected function IsMobile($value){
        $rule='^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule,$value);
        if($result){
            return true;
        }else{
            return false;
        }
    }

    public function getDatasByRule($array){
        if(array_key_exists('user_id',$array) | array_key_exists('uid',$array)){
            throw new ParameterException([
                'msg' => '参数中包含uid或user_id恶意参数'
            ]);
        }

        foreach($this->rule as $key=>$value ){
            $newArray[$key]=$array[$key];
        }
        return $newArray;
    }
}