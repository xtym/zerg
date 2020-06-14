<?php

namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product
{


    /**
     * @url /product/recent?count=1
     * @return 最新商品
     */
    public function getRecent($count = 15){
       (new Count())->goCheck();
       $products =ProductModel::getMostRecent($count);
       if($products->isEmpty()){
           throw new ProductException();
       }
       $products = $products->hidden(['summary']);
       return $products;
    }

    /**
     * @url /product/by_category?id=1
     * @return 根据分类id查找商品
     */
    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        return $products;
    }

    /**
     * @url /product/:id
     * @return 根据商品id返回商品详情
     */
    public function getOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
   }
   
}