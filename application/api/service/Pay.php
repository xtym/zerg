<?php

namespace app\api\service;

use Exception;
use app\api\model\Order as OrderModel;
use app\lib\exception\TokenException;
use app\lib\exception\OrderException;
use app\lib\enum\OrderStatusEnum;
class Pay{

    private $orderID;
    private $orderNO;

    function construct($orderID){
        if(!$orderID){
            throw new Exception('订单号不允许为null');
        }
        $this->orderID=$orderID;
    }

    //支付
    public function pay(){
        $this->checkOrderValid();
        $orderService = new Order();
        $status = $orderService->checkOrderStock($this->orderID);//进行库存量检测
        if(!$status['pass']){//如果订单状态不通过则返回
            return $status;
        }

    }

    //微信预订单
    private function makeWXPreOrder(){

    }

    //检测订单是否合法
    private function checkOrderValid(){
        $order = OrderModel::where('id','=',$this->orderID)->find();
        if(!$order){//订单号可能根本不存在
            throw new OrderException();
        }
        if(!Token::isValidOperate($order->user_id)){//订单号确实存在,但和当前用户不匹配
            throw new TokenException([
                'msg'=>'订单和用户不匹配',
                'errorCode'=>'10003'
            ]);
        }
        if($order->status !=OrderStatusEnum::UNPAID){//订单号已经支付过了
            throw new OrderException([
                'msg'=>'订单状态异常',
                'errorCode'=>'80003',
                'code'=>400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }
}