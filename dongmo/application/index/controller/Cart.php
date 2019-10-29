<?php
namespace app\index\controller;
use think\Request;

class Cart extends Common
{
	// 购物车商品删除
	public function remove()
	{
		// 商品标识
		$goods_id = input('goods_id');
		// 属性值的id组合
		$goods_attr_ids = input('goods_attr_ids','');

		model('Cart')->delCart($goods_id,$goods_attr_ids);
		echo 'ok';
	}
	// 购物车列表
	public function index()
	{
		$model = model('Cart');
		// 调用自定义模型方法获取购物车数据库
		$data = $model->listData();
		$this->assign('data',$data);
		// 调用方法计算总金额
		$total = $model->getTotal($data);
		$this->assign('total',$total);
		return $this->fetch();
	}
	// 商品加入购物车
	public function addCart()
	{
		// 商品标识
		$goods_id = input('goods_id');
		// 数量
		$goods_count = input('goods_count/d');
		// 属性值的id组合
		$goods_attr_ids = input('goods_attr_ids/a',[]);
		// 将属性值的id组合转换为字符串格式
		$goods_attr_ids = implode(',', $goods_attr_ids);
		model('Cart')->addCart($goods_id,$goods_count,$goods_attr_ids);
		echo 'ok';
	}
	// public function test()
	// {
	// 	dump(cookie('cartlist'));
	// }
}