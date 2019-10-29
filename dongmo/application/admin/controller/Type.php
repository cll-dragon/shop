<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
/**
 * 类型的控制器
 */
// 继承控制器公共类
class Type extends Common{
    // 类型编辑
    public function edit(Request $request){
        $model = model('Type');
        $type_id=input('id');
        if($request->isGet()){
            // 获取原始数据
            $info=$model->get($type_id);
            $this->assign('info',$info);
            return $this->fetch();
        }
        // 获取修改数据
        $data=input();
        // 验证数据,用query类调用验证方法
        $result = $this->validate($data,'Type');
        if($result !== true){
            $this->error($result);
        }
        // 调用模型基类的方法写入数据
        $model->save($data,['id'=>$type_id]);
        $this->success('ok');
    }
    // 删除类型
    public function remove(){
        $type_id=input('id');
        // 调用自定义的模型类方法移除数据库数据
        model('Type')->remove($type_id);
        $this->success('ok','index');
    }
    // 类型列表显示
    public function index(){
        // 创建Type模型对象
        $model=model('Type');
        $data=$model->all();
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 类型添加方法
    public function add(Request $request){
        // 判断提交方式
        if($request->isGet()){
            return $this->fetch();
        }
        $model = model('Type');
        $data = input();
        // 使用验证器验证数据
        $result = $this->validate($data,'Type');
        if($result !== true){
            $this->error($result);
        }
        // 调用模型基类的方法写入数据
        $model -> save($data);
        $this->success('ok');
    }
}