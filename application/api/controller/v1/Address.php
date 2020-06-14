<?php
namespace app\api\controller\v1;

use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use app\api\controller\BaseController;

class Address extends BaseController{

    protected $beforeActionList = [
        'checkPrimaryScope'=>['only'=>'createOrUpdateAddress']
    ];
    
    public function createOrUpdateAddress(){
        //通过token获取uid
        //通过uid检验是否有该用户
        //如果有该用户就获取用户上传的地址信息
        //判断用户地址信息是否存在,存在就更新,不存在就新增
        //返回结果
        $validator = new AddressNew();
        $validator->goCheck();
        $uid = TokenService::getCurrentUID();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        $dataArray = $validator->getDatasByRule(input('post.'));
        $address = $user->address;
        if(!$address){//新增
            $user->address()->save($dataArray);
        }else{
            $user->address->save($dataArray);
        }
        
        return  json(new SuccessMessage(),201);
    }
}