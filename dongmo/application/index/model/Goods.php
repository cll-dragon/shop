<?php
namespace app\index\model;
use think\Db;
use think\Model;
/**
 * 商品模型
 */
class Goods extends Model{
    // 根据商品id获取商品信息
    public function getGoodsInfo($goods_id){
        // 保存所有的商品信息
        $goods=[];
        $goods['goods_info'] = $this->where('id',$goods_id)->find();
        // 获取相册
        $goods['img']=Db::name('goods_img')->where('goods_id',$goods_id)->select();
        // 获取商品的属性信息
		$sql = "SELECT a.*,b.attr_name,b.attr_type from shop_goods_attr a LEFT JOIN shop_attribute b ON a.attr_id=b.id WHERE a.goods_id=?";
        $attrs = Db::query($sql,[$goods_id]);
        // dump($attrs);die;
		foreach ($attrs as $key => $value) {
			if($value['attr_type'] == 1){
				// 唯一属性
				$goods['radio'][]=$value;
			}else{
				// 单选属性，单选属性需要将相同的属性合并为一组。转换为三维数组。第一个维度的下标采用属性id作为下标
				$goods['sigle'][$value['attr_id']][]=$value;
			}
		}
        return $goods;
    }

    public function getDbObj(){
		return Db::name('goods');
	}

    // 获取推荐状态的商品，$field为推荐的字段,is_del表示是否伪删除
    public function getRecGoods($field){
        $map=[
            $field=>1,
            'is_del'=>0
        ];
        // limit(5)表示找五个,order('addtime')根据添加时间排序
        return $this->getDbObj()->where($map)->limit(5)->order('addtime')->select();
    }
}