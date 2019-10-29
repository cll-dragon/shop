<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
/**
 * 属性的控制器
 */
class Attribute extends Common{
    // 删除属性信息
    public function remove(){
        $model = model('Attribute');
        $result=$model->remove(input('id'));
        if($result===false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    // 编辑属性信息,依赖注入$request对象
    public function edit(Request $request){
        $model=model('Attribute');
        if($request->isGet()){
            // 获取所有的类型信息
            $type=model('Type')->all();
            // 将类型信息赋值给模板
            $this->assign('type',$type);
            // 获取所要编辑的属性信息
            $attr_id=input('id');
            $info = $model->get($attr_id);
            // dump($info);exit;
            // 将该属性信息赋值给模板
            $this->assign('info',$info);
            return $this->fetch();
        }
        // 编辑属性信息完毕之后点击提交，提交表单方式为post
        $data=input();
        // dump($data);exit;
        $result=$this->validate($data,'Attribute');
        // echo $result;exit; 输出属性名不能为空
        if($result !== true){
            $this->error($result);
        }
        // 判断属性值的输入方式，1位input输入，2为下拉选框
        if ($data['attr_input_type']==2) {
            // select选择,为下拉选框时，
            if (empty($data['attr_values'])) {
                $this->error('select选择必须有默认');
            }
        } else {
            // input输入,自己输入，重置属性值 明天看下视频
            unset($data['attr_values']);
        }
        // 调用自定义的模型方法edit()将数据写入数据库
        $result = $model->editAttribute($data);
        if($result === false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    // 显示属性信息
    public function index(){
        // 两表联查，左外连接 field('a.*,b.type_name')表示查找数据
        $data = db('attribute')->alias('a')->join('shop_type b','a.type_id=b.id','left')->
        field('a.*,b.type_name')->select();
        // dump($data);exit;
        return $this->fetch('index',['data'=>$data]);
    }
    // 添加属性
    public function add(Request $request){
        if ($request->isGet()) {
            // 获取所有的类型信息
            $type=model('Type')->all();
            $this->assign('type', $type);
            return $this->fetch();
        }
        $data = input();
        // 判断属性值的输入方式，1位input输入，2为下拉选框
        if ($data['attr_input_type']==2) {
            // select选择,为下拉选框时，
            if (empty($data['attr_values'])) {
                $this->error('select选择必须有默认');
            }
        } else {
            // input输入,自己输入，重置属性值 明天看下视频
            // input输入的话代表是唯一属性，attr_values为空
            unset($data['attr_values']);
        }
        db('attribute')->insert($data);
        $this->success('ok','index');
    }
}
