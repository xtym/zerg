<?php

namespace app\api\service;

use think\Loader;
use WxPayNotify;

Loader::import('WXPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends WxPayNotify{

    public function NotifyProcess($objData, $config, $msg){
        //1.检查库存量
        //2.更新订单的status状态
        //3.减库存
        //4.如果成功处理,我们返回微信成功处理的信息.否则,我们需要返回没有成功处理.
    }
}