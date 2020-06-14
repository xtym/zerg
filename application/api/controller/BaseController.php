<?php

namespace app\api\controller;

use think\Controller;
use app\api\service\Token as TokenService;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;

class BaseController extends Controller{

    protected function checkExclusiveScope(){
        return TokenService::needExclusiveScope();
    }

    protected function checkPrimaryScope(){
        return TokenService::needPrimaryScope();
     }
}