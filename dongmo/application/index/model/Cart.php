<?php 
namespace app\index\model;
use think\Model;
use think\Db;
/**
* 商品模型
*/
class Cart extends Model
{
	// cookie总数据转移到数据库下
	public function cookie2db()
	{
		// 1、读取用户信息
		$user_info = session('user_info');
		if(!$user_info){
			return false;
		}
		// 2、读取cookie中的购物车数据
		$cartlist = cookie('cartlist')?cookie('cartlist'):[];
		// 3、针对cookie中每一条数据写入到数据库下	。如果数据库中存在以cookie数量为准
		foreach ($cartlist as $key => $value) {
			// 判断相同的商品信息是否存在
			$tmp = explode('-',$key);
			$map = [
				'goods_id'=>$tmp[0],
				'goods_attr_ids'=>$tmp[1],
				'user_id'=>$user_info['id']
			];
			if(Db::name('cart')->where($map)->find()){
				Db::name('cart')->where($map)->setField('goods_count',$value);
			}else{
				$map['goods_count'] = $value;
				Db::name('cart')->where($map)->insert($map);
			}
		}
		// 4、清空cookie内容
		cookie('cartlist',null);
	}
	public function delCart($goods_id,$goods_attr_ids)
	{
		$user_info = session('user_info');
		if($user_info){
			// 已经登录状态
			$map = [
				'goods_id'=>$goods_id,
				'goods_attr_ids'=>$goods_attr_ids,
				'user_id'=>$user_info['id']
			];
			Db::name('Cart')->where($map)->delete();
		}else{
			$cartlist = cookie('cartlist')?cookie('cartlist'):[];
			// 组装下标的名称
			$key = $goods_id.'-'.$goods_attr_ids;
			
			unset($cartlist[$key]);

			cookie('cartlist',$cartlist,3600*24*7); 
		}
	}
	public function getTotal($data)
	{
		$money = $number = 0;
		foreach ($data as $key => $value) {
			$number += $value['goods_count'];//购买商品的总数
			$money += $value['goods_count']*$value['goods_info']['shop_price'];
		}
		return ['money'=>$money,'number'=>$number];
	}
	// 获取用户的购物车数据
	public function listData()
	{
		// 1、判断用户是否登录
		$user_info = session('user_info');
		if($user_info){
			// 已经登录
			$cart = Db::name('cart')->where('user_id',$user_info['id'])->select();
		}else{
			$cartlist = cookie('cartlist')?cookie('cartlist'):[];
			// 将数据格式转换
			foreach ($cartlist as $key => $value) {
				$tmp= explode('-',$key);
				
				$cart[]=[
					'goods_id'=>$tmp[0],
					'goods_count'=>$value,
					'goods_attr_ids'=>$tmp[1],
				];
			}
		}
		// 根据购物车数据获取到商品的信息及属性
		foreach ($cart as $key => $value) {
			// 获取商品的基本信息
			$cart[$key]['goods_info'] = Db::name('goods')->find($value['goods_id']);
			// 获取商品属性信息
			$cart[$key]['attrs']= Db::name('goods_attr')->alias('a')->join('shop_attribute b','a.attr_id=b.id','left')->field('a.attr_value,b.attr_name')->where('a.id','in',$value['goods_attr_ids'])->select();
		}
		return $cart;
	}
	// 商品加入购物车
	public function addCart($goods_id,$goods_count,$goods_attr_ids)
	{
		// 1、判断用户是否登录
		$user_info = session('user_info');
		if($user_info){
			// 已经登录
			// 先判断加入的商品在购物车中是否存在
			$query = Db::name('cart');
			$map = [
				'user_id'=>$user_info['id'],
				'goods_id'=>$goods_id,
				'goods_attr_ids'=>$goods_attr_ids
			];
			if($query->where($map)->find()){
				// 存在相同的商品 需要累加数量
				// setInc为query类下的方法用于增加数量。会在数据已有的基础之上增加 第一个参数指定字段名称。第二个参数指定加多少个
				$query->where($map)->setInc('goods_count',$goods_count);
			}else{
				// 不存在相同的商品写入数据
				$map['goods_count'] = $goods_count;
				$query->insert($map);
			}
		}else{
			// 没有登录状态
			// 1、取出cookie中已有的购物车数据
			$cart = cookie('cartlist')?cookie('cartlist'):[];
			// 2、判断是否存在相同的商品
			// 组装购物车商品的下标
			$key = $goods_id.'-'.$goods_attr_ids;
			if(array_key_exists($key, $cart)){
				// 存在相同的商品
				$cart[$key] += $goods_count;
			}else{
				$cart[$key] = $goods_count;
			}
			// 3、将数据保存到cookie中
			cookie('cartlist',$cart,3600*24*7);
		}
	}
}