<?php

namespace app\api\service;

use Exception;
use app\api\model\Order as OrderModel;
use app\lib\exception\TokenException;
use app\lib\exception\OrderException;
use app\lib\enum\OrderStatusEnum;
use think\Loader;
use think\Log;

use WxPayApi;
use WxPayJsApiPay;
use WxPayUnifiedOrder;

Loader::import('WXPay.WxPay',EXTEND_PATH,'.Api.php');

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
        return $this->makeWXPreOrder($status['orderPrice']);
    }

    //微信预订单
    private function makeWXPreOrder($totalPrice){
        $openid=Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData = new WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);//订单编号
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url('');
        return $this->getPaySignature($wxOrderData);
    }

    //获取微信签名
    private function getPaySignature($wxOrderData){
        $wxOrder= WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code']!='SUCCESS' || $wxOrder['result_code']!='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        $this->recordPreOrder($wxOrder);//更新订单preOrder_id字段
        $signature=$this->sign($wxOrder);
        return $signature;
    }

    //生成微信签名
    private function sign($wxOrder){
        $jsApiPayData = new WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand=md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues=$jsApiPayData->GetValues();
        $rawValues['paySign']=$sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    //每次发起预支付请求后,成功则更新preOrder_id
    private function recordPreOrder($wxOrder){
        OrderModel::where('id','=',$this->orderID)
        ->update(['prepare_id'=>$wxOrder['prepare_id']]);
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