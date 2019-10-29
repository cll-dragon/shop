<?php
namespace app\index\controller;
use think\Request;

class User extends Common
{
	// 用户注册
	public function regist(Request $request)
	{
		if($request->isGet()){
			return $this->fetch();
		}
		$model = model('User');
		$result = $model->regist(input());
		if($result === FALSE){
			$this->error($model->getError());
		}
		$this->success('ok','login');
	}
   	// 用户登录
	public function login(Request $request)
	{
		if($request->isGet()){
			return $this->fetch();
		}
		$model = model('User');
		$result = $model->login(input());
		if($result === FALSE){
			$this->error($model->getError());
		}
		$this->redirect('index/index/index');
	}
	// 退出
	public function logout()
	{
		session('user_info',null);
		$this->redirect('login');
	}
}