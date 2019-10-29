<?php 
namespace app\index\model;
use think\Model;
use think\Db;
/**
* 用户模型
*/
class User extends Model
{
	// 用户注册
	public function regist($data)
	{
		$query = Db::name('user');
		// 1、检查用户名重复问题
		if($query->where('username',$data['username'])->find()){
			$this->error = '用户名重复';
			return FALSE;
		}
		// 2、生成密码使用的盐
		$data['salt'] = rand(100000,999999);
		// 3、计算密码
		$data['password']=md5(md5($data['password']).$data['salt']);
		// 4、数据写入
		$query->insert($data);
	}
	// 用户登录
	public function login($data)
	{
		$query = Db::name('user');
		// 1、查看用户的信息
		$user_info = $query->where('username',$data['username'])->find();
		if(!$user_info){
			$this->error = '用户名错误';
			return FALSE;
		}
		// 2、比对密码 根据用户提交的密码按照相同的规则生成密文与数据库比对
		if($user_info['password'] != md5(md5($data['password']).$user_info['salt'])){
			$this->error = '密码错误';
			return FALSE;
		}
		// 3、保存登录状态
		session('user_info',$user_info);
		// 购物车数据转移
		model('Cart')->cookie2db();
	}
}