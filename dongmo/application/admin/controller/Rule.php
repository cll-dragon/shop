<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
/**
 * 权限控制器
 */
class Rule extends Common{
    // 权限列表显示
    public function index(){
        // 获取所有的权限数据
        $rules = model('Rule')->getRules();
        // dump($rules);exit;
        $this->assign('rules',$rules);
        return $this->fetch();
    }
    // 添加权限
    public function add(Request $request){
        $model = model('Rule');
        if($request->isGet()){
            // 获取所有的权限信息,调用自定义的模型类方法
            $rules = model('Rule')->getRules();
            $this->assign('rules',$rules);
            return $this->fetch();
        }
        // 调用自定义的模型类方法实现数据写入
        $result = $model->insert(input());
        if($result === false){
            $this->error($model->getError());
        }
        $this->success('ok');
    }
}