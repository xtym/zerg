<?php
namespace app\api\service;

use app\api\model\Product;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct as OrderProductModel;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use Exception;
use think\Db;

// use app\api\model\Product as ProductModel;

class Order{

    protected $oProducts;//用户传入的购买商品
    protected $products;//数据库查询出的商品
    protected $uid;

    public function place($uid,$oProducts){//下单
        $this->oProducts=$oProducts;
        $this->products=$this->getProductsByOrder($oProducts);
        $this->uid=$uid;
        $status = $this->getOrderStatus();
        if(!$status['pass']){
            $status['order_id']=-1;
            return $status;
        }
        //订单快照
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass']=true;
        return $order;
    }

    public function checkOrderStock($orderID){
        $oProducts = OrderProductModel::where('order_id','=','$orderID')->select();
        $this->oProducts=$oProducts;
        $this->Products =$this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

    //生成订单
    public function createOrder($snap){
        Db::startTrans();
        try{
            $orderNo=$this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id=$this->uid;
            $order->order_no=$orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();
    
            $orderId= $order->id;
            foreach($this->oProducts as &$p){
                $p['order_id']=$orderId;
            }
            $orderProduct = new OrderProductModel();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no'=>$orderNo,
                'order_id'=>$orderId,
                'create_time'=>$order->create_time
            ];
        }catch(Exception $e){
            Db::rollback();
            throw $e;
        }
    }

    //生成订单编号
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }


    //生成订单快照
    private function snapOrder($status){
        $snap = [
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatus'=>[],
            'snapAddress'=>null,
            'snapName'=>'',
            'snapImg'=>''
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if(count($this->products)>1){
            $snap['snapName']=$snap['snapName'].'等';
        }
        return $snap;
    }

    /**
     * 获取用户地址
     */
    private function getUserAddress(){
        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001,
            ]);
        }
        return $userAddress->toArray();
    }

    /**
     * 获取订单状态
     */
    private function getOrderStatus(){
        $status = [
            'pass'=>true,
            'orderPrice' => 0,
            'totalCount'=>0,
            'pStatusArray' =>[]
        ];
        foreach ($this->oProducts as $oProduct){
            $pstatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'],$this->products
            );
            if(!$pstatus['haveStock']){
                $status['pass']=false;
            }
            $status['orderPrice']+=$pstatus['totalPrice'];
            $status['totalCount']+=$pstatus['count'];
            array_push($status['pStatusArray'],$pstatus);
        }
        return $status;
    }

    /**
     * 获取商品状态
     */
    private function getProductStatus($oPID,$oCount,$products){

        $pIndex =-1;
        $pStatus=[
            'id'=>null,
            'haveStock'=>false,
            'count'=>0,
            'name'=>'',
            'totalPrice'=>0
        ];
        for($i=0;$i<count($products);$i++){
            if($oPID == $products[$i]['id']){
                $pIndex = $i;
            }
        }
        if($pIndex==-1){//如果数据库中找不到订单中的商品
            throw new OrderException([
                'msg' =>'id为'.$oPID.'的商品不存在,创建订单失败'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price']*$oCount;
            if($product['stock']-$oCount >=0){
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }


    /**
     * 根据用户传入订单信息去数据库查找对应商品信息
     */
    private function getProductsByOrder($oProducts){
        $oPIDs=[];
        foreach($oProducts as $item){
            array_push($oPIDs,$item['product_id']);
        };
        $products= Product::all($oPIDs)
        ->visible(['id','price','stock','name','main_img_url'])->toArray();
        return $products;
        
    }
}