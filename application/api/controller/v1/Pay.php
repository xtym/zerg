<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;

class Pay extends BaseController{
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'placeOrder']
    ];

    /**
     * @url POST /pay/pre_order
     * @return 订单预支付信息
     */
    public function getPreOrder($id){
        (new IDMustBePositiveInt())->goCheck();

    }
}