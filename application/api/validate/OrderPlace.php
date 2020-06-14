<?php
namespace app\api\validate;

use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidator{

    protected $rule=[
        'products' => 'checkProducts'
    ];
    protected $singleRule=[
        'product_id' => 'require|IsPositiveInteger',
        'count'=>'require|IsPositiveInteger'
    ];
    protected function checkProducts($values){
        if(!is_array($values)){
            throw new ParameterException([
                'msg'=>'参数必须是数组'
            ]);
        }
        if(empty($values)){
            throw new ParameterException([
                'msg'=>'参数不能为空'
            ]);
        }
        foreach($values as $value){
           $this->checkProduct($value);
        }
        return true;
    }
    protected function checkProduct($value){
        $validator = new BaseValidator($this->singleRule);
        if(!$validator->check($value)){
            throw new ParameterException([
                'msg'=>'商品参数不正确'
            ]);
        }
        return true;

    }
}