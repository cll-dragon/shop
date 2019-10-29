<?php 
namespace app\index\model;
use think\Model;
use think\Db;
/**
* 商品模型
*/
class Order extends Model
{
	// 下单
	public function add($data)
	{
		// 1、向订单主表中写入内容
		// 计算订单号
		$data['order_sn'] = date('YmdHis').rand(100000,999999);
		// 取出用户的id
		$user_id = session('user_info')['id'];
		$data['user_id'] = $user_id;
		// 设置支付方式
		$data['pay_way'] = 1;
		Db::name('order')->insert($data);
		// 取出订单id
		$data['id'] = $order_id = Db::name('order')->getLastInsId();
		// 2、向订单详情表中写入内容
		// 组装订单详情表需要写入数据的格式
		$order_list = Db::name('cart')->where('user_id',$user_id)->select();
		foreach ($order_list as $key => $value) {
			$list[]=[
				'order_id'=>$order_id,
				'goods_id'=>$value['goods_id'],
				'goods_count'=>$value['goods_count'],
				'goods_attr_ids'=>$value['goods_attr_ids']
			];
		}
		Db::name('order_detail')->insertAll($list);
		// 清空购物车数据
		// Db::name(cart)->where('user_id',$user_id)->delete();
		return $data;
	}
}