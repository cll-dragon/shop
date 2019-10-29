<?php
namespace app\admin\validate;
use think\Validate;
/**
 * 添加商品验证器
 */
class Goods extends Validate{
    // 指定验证规则
    protected $rule=[
        'goods_name|商品名'=>'require',
        'shop_price|本店售价'=>'require|checkMarketPrice|gt:0',
        'cate_id|分类'=>'require|gt:0'
    ];
    // 设置错误提示信息
    protected $message=[
        'shop_price.gt'=>'本店售价必须大于0',
        'cate_id.gt'=>'分类必须选择'
    ];
    // 检查价格的自定义规则
    public function checkMarketPrice($value,$rule,$data){
        if($value>$data['market_price']){
            return FALSE;
        }
        return TRUE;
    }
}