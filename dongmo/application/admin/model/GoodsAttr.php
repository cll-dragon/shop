<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 商品属性值模型
 */
class GoodsAttr extends Model
{
    // 数据的批量写入
    public function addAll($goods_id,$attr_ids,$attr_values){
        // 保存最终的二维数据
        $list=[];
        // 保存不重复的数据的一维数组，只是用于过滤数据的重复
        $tmp=[];
        foreach($attr_ids as $key=>$value){
            // 防止属性id与属性值重复，去掉重复的数据
            $string=$value.'-'.$attr_values[$key];
            if(in_array($string,$tmp)){
                // 数据重复
                continue;
            }
            // 不重复向tmp变量中增加不重复的数据的标识
            $tmp[]=$string;
            $list[]=[
                'goods_id'=>$goods_id,
                'attr_id'=>$value,
                'attr_value'=>$attr_values[$key]
            ];
        }
        // dump($list);exit;
        // 将数据写入属性值表中
        if($list){
            $this->insertAll($list);
        }
    }

    // 根据商品id获取完整的属性信息
    public function getAttrByGoodsId($goods_id)
    {
        // 1、获取到当前商品的属性信息
        $data = Db::name('goods_attr')->alias('a')->field(' a.*,b.attr_name,b.attr_type,b.attr_input_type,b.attr_values')
        ->join('shop_attribute b', 'a.attr_id=b.id', 'left')->where('a.goods_id', $goods_id)->select();
        // 2、对属性中的默认值进行格式化
        // shop_attribute是属性表，shop_goods_attr是属性值表
        // dump($data);exit; 得到的是一个二维数组，根据下标获取里面的值
        foreach ($data as $key => $value) {
            if ($value['attr_input_type'] ==2) {
                // select选择,把单选的多个属性值拆开，explode()将字符串分隔为数组，implode()将数组变为字符串
                $data[$key]['attr_values'] = explode(',', $value['attr_values']);
            }
        }
        // dump($data);exit;  
        // 3、再将结果进行格式化,以属性下标作为分组  
        $list = [];//保存最终的结果
        foreach ($data as $key => $value) {
            // 向list变量中增加元素 下标为$value['attr_id'] 值为数组格式
            // 将属性下标拿出来作为新数组的下标  
            $list[$value['attr_id']][]=$value;
        }
        return $list;
    }
}