<?php
namespace app\admin\controller;
use think\Request;
use think\Db;

/**
 * 角色控制器
 */
class Role extends Common{
    // 给角色分配权限
    public function disfetch(){
        $role_id=input('id');
        if($role_id<=1){
            // 超级管理员角色不需要分配权限
            $this->error('参数错误');
        }
        // 使用request()助手函数
        if(request()->isGet()){
            // 获取当前角色具备的权限id
            $role_info = Db::name('role')->where('id',$role_id)->find();
            // 开始应该是没有的，为空
            $hasRules=$role_info['rule_ids'];
            $this->assign('hasRules',$hasRules);
            // 获取所有的权限信息,调用Rule模型类里面的方法
            $rules = model('Rule')->getRules();
            $this->assign('rules',$rules);
            return $this->fetch();
        }
        // dump(input('rule/a'));exit;
        // 接收分配到的权限id,rule是数组格式的数据，要使用/a添加接收
        $rules = input('rule/a');
        // 数组转换为字符串格式 以逗号,分隔
        $rule_ids = implode(',',$rules);
        // setField() 更新某个字段的数据
        Db::name('role')->where('id',$role_id)->setField('rule_ids',$rule_ids);
        $this->success('ok');
    }
    // 删除角色
    public function remove(){
        $role_id=input('id');
        if($role_id<=1){
            // 超级管理员角色不容许删除
            $this->error('参数错误');
        }
        // 调用自定义的模型类删除方法
        model('Role')->remove($role_id);
        $this->success('ok','index');
    }
    // 角色列表显示
    public function index(){
        $model = model('Role');
        $data = $model->all();
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 角色添加
    public function add(Request $request){
        if ($request->isGet()) {
            return $this->fetch();
        }
        $model = model('Role');
        // 调用自定义的模型类方法
        $result=$model->add(input());
        if($result === false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
}