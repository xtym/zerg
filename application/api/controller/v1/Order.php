<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\service\Token;

class Order extends BaseController{
    //1.用户在选择商品后,向api提交包含它所选商品的相关信息
    //2.api接到信息后,检查相关订单商品的库存量
    //3.有库存把订单信息存入数据库中=下单成功,返回客户端消息,告诉客户可以支付了
    //4.调用我们的支付接口进行支付
    //5.再次进行库存量检查(下单和支付可以相隔很多天)
    //6.服务器调用微信支付接口进行支付
    //7.微信返回给我们一个支付结果(异步)
    //8.成功也要进行库存量检查(支付之间可能有延时)
    //9.成功:进行库存扣除 


    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'placeOrder']
    ];

    /**
     * @url POST /order
     * @return 下单结果
     */
    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $oProducts = input('post.products/a');
        $uid = Token::getCurrentUID();
        $order=new OrderService();
        $status = $order->place($uid,$oProducts);
        return $status;
    }

}