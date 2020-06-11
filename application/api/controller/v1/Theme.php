<?php

namespace app\api\controller\v1;

use think\Request;
use think\Validate;
use think\Exception;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\api\validate\IDCollection;
use app\lib\exception\BannerMissException;
use app\api\model\Theme as ThemeModel;
use app\lib\exception\ThemeException;

class Theme
{

    /**
     * @url /theme?ids=id1,id2,id3....
     * @return 一组主题模型
     */
    public function getSimpleList($ids){
        (new IDCollection())->goCheck();
        $ids= explode(',',$ids);
        $result = ThemeModel::with(['topicImg','headImg'])->select($ids);
        if(!$result){
            throw new ThemeException();
        }
        return $result;
    }
   
}