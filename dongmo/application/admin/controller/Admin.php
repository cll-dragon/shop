<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
/**
 * 管理员的控制器
 */
class Admin extends Common{
    // 管理员信息编辑
    public function edit(Request $request){
        $model = model('Admin');
        $admin_id = input('id');
        // 超级管理员不能被修改
        if($admin_id<=1){
            $this->error('参数错误');
        }
        if($request->isGet()){
            // 获取所有的角色信息
            $roles = model('Role')->all();
            $this->assign('roles',$roles);
            // 获取当前要修改的用户信息
            $this->assign('info',$model->get($admin_id));
            return $this->fetch();
        }
        // 获取表单提交的修改数据，调用自定义的模型类方法进行修改
        $result=$model->edit(input());
        if($result === false){
            $this->error($model->getError());
        }
        $this->success('ok');
    }
    // 管理员列表显示
    public function index(){
        $model=model('Admin');
        // field()要查找的字段
        $data=$model->alias('a')->field('a.*,b.role_name')->join('shop_role b','a.role_id=b.id','left')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 管理员用户添加
    public function add(Request $request){
        if($request->isGet()){
            // 获取所有的角色信息
            $roles=model('role')->all();
            $this->assign('roles',$roles);
            return $this->fetch();
        }
        $model = model('Admin');
        $result = $model->add(input());
        if($result === false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
}