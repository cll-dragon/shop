<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 登录模型类
 */
class Admin extends Model{
    public function edit($data){
        // 设置查找条件，用户名相同，但是不是本身，id不同
        // 查询除了本身之外是否还有与用户名相同的数据
        $map=[
            'username'=>$data['username'],
            'id'=>['neq',$data['id']]
        ];
        // $this->where($map)->find()调用query方法查询，
        if($this->get($map)){
            $this->error('用户名重复');
            return false;
        }
        // 处理密码
        if($data['password']){
            $data['password']=md5($data['password']);
        }else{
            // 不提交密码，不修改，数据库中密码不变
            unset($data['password']);
        }
        // 入库修改
        $this->where('id',$data['id'])->update($data);
    }
    // 管理员用户的添加
    public function add($data){
        // 检查用户名是否重复问题
        if($this->get(['username'=>$data['username']])){
            $this->error('用户名重复');
            return false;
        }
        // 生成密码,进行md5加密
        $data['password'] = md5($data['password']);
        // dump($data);exit;
        // 写入数据库
        $this->save($data);
    }
    public function login($username,$password,$is_remember=false){
        // 比对账号信息
        $map=[
            'username'=>$username,
            'password'=>md5($password)
        ];
        // 根据$map条件查询数据库表中数据是否存在
        $user_info=$this->where($map)->find();
        // $user_info获取到的是对象
        // dump($user_info->toArray());exit;
        if(!$user_info){
            $this->error("账号信息错误");
            return false;
        }
        // 根据是否记住密码保存登录状态
        // 默认cookie的有效时间为关闭浏览器失效
        $expire=0;
        if($is_remember){
            // 给cookie设置销毁时限，7天免登录
            $expire=7*24*3600;
        }
        // $expire 时限,$user_info->toArray将对象转换为数组保存到cookie中
        cookie('user_info',$user_info->toArray(),$expire);
    }
} 