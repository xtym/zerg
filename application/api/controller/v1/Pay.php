<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController{
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'placeOrder']
    ];

    /**
     * @url POST /pay/pre_order
     * @return 预订单信息
     */
    public function getPreOrder($id){
        (new IDMustBePositiveInt())->goCheck();
        $pay=new PayService($id);
        return $pay->pay();
    }

    /**
     * @url POST /pay/notify
     * @return 
     */
    public function receiveNotify(){
        
        //特点:POST方式返回xml格式的数据

    }
}