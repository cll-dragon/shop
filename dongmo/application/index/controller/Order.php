<?php
namespace app\index\controller;
use think\Request;
use think\Db;
class Order extends Common
{
	// 结算页的显示
	public function check()
	{
		// 1、检查用户是否登录
		$user_info = session('user_info');
		if(!$user_info){
			$this->error('先登录','user/login');
		}
		// 2、查询购物车数据
		$model = model('Cart');
		// 调用自定义模型方法获取购物车数据库
		$data = $model->listData();
		$this->assign('data',$data);
		// 调用方法计算总金额
		$total = $model->getTotal($data);
		$this->assign('total',$total);
		return $this->fetch();
	}
}