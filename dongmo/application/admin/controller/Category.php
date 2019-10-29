<?php
namespace app\admin\controller;
use think\Db;
// 引入Request基类，使用请求对象
use think\Request;
/**
 * 分类的控制器  
 */
class  Category extends Common{
    // 编辑分类方法，依赖注入$request请求对象，使用请求对象判断表单提交方式
    public function edit(Request $request){
        // 获取模型对象
        $model = model('Category');
        // dump(date('Ymd'));exit;
        // 没点提交时，from表单默认是Get方法
        if($request->isGet()){
            // 获取当前要修改的数据, input()方法获取url里面传递过来的id值
            $info = $model->get(input('id'));
            // 将数据分配给模板
            // 有新界面所以要分配给模板,再渲染模板显示
            $this->assign('info',$info);
            // 获取所有的分类的数据,该方法在模型类里面自定义 
            $tree = $model->getCateTree();
            $this->assign('tree',$tree);    
            return $this->fetch();
        }
        // $data=input();
        // $map = [
        //     'cate_name'=>$data['cate_name'],
        //     //  修改的本来就是自己，所以不能找自己，id不能相等
        //     'id'=>['neq',$data['id']]
        //     // neq,不相等
        // ];
        // $model -> where($map) -> find();
        // dump($model -> getLastSql());
        // exit;

        // 调用自定义的方法完成数据入库修改
        $result = $model->editCategory(input());
        if($result === false){
            $this->error($model->getError());
        }
        // 删除成功，跳转到index方法里面显示分类
        $this->success("ok",'index');
    }

    // 删除分类数据方法
    public function remove(){
        $model = model('Category');
        // 调用自定义的模型方法删除
        $result = $model->remove(input('id'));
        if($result === false){
            $this->error($model->getError());
        }
        // 删除成功，跳转到index方法里面显示分类
        $this->success("ok",'index');
    }
    // 分类的列表显示功能
    public function index(){
        // 获取所有的分类数据
        $data = model('Category')->getCateTree();
        // 将数据分配给模板
        $this->assign("data",$data);
        // 渲染模板
        return $this->fetch();
    }

    // 显示分类的添加模板
    // 依赖注入参数$request对象，使用request对象来获取请求数据，可以不用使用超全局变量了
    public function add(Request $request){
        // $_SERVER超全局变量里面有个关联下标REQUEST_METHOD
        // 保存的值是表单提交的方式，默认为get
        // echo $_SERVER["REQUEST_METHOD"]; 输出为GET

        // 获取模型对象，不再使用query对象
        $model = model('Category');
        // 连接数据库，实例化数据库对象
        // $query=Db::name('category');
        // if($_SERVER["REQUEST_METHOD"]=='GET'){
        //使用request对象调用Request基类内置的方法判断From表单的提交方式是否为get 
        if($request->isGet()){
            // 这是通过query对象调用方法获取分类数据
            // // 获取所有的分类数据
            // $tree=$query->select();   
            // // 调用自定义函数进行数据格式化，类别整理
            // $tree=get_tree($tree);

            // 下面通过模型对象调用方法获取分类数据
            $tree = $model->getCateTree();
            // 获得的是一维数组，数组里面的元素是对象，
            // 这些元素这是tp里面的模型内置对象，可以使用在模板里面使用点语法调用
            // dump($tree);
            // 将分类数据赋值给模板
            $this->assign('tree',$tree);
            // 默认为GET，渲染模板
            return $this->fetch();
        }
        // 为post请求
        // 1.接收表单提交的$_POST里面的数据
        // $data=$_POST;
        // 直接使用助手函数input获取表单提交的数据
        $data=input();
        // 验证令牌token,可以将token的验证加入到任何一个验证规则中
        $rule=[
            'cate_name'=>'require|token'
        ];
        $res=$this->validate($data,$rule);
        if($res!==TRUE){
            $this->error($res);
        }
        // 表单中的__token__值验证之后需要销毁
        unset($data['__token__']);

        // 调用模型类下的自定义方法实现数据写入
        $result = $model->insert($data);
        // 模型方法的返回值，判断数据是否插入成功
        if($result === false){
            // $this表示当前控制器类下的对象
            // getError()方法是模型基类里的方法，返回error属性，在模型类里的方法设置error属性值，比较常用
            $this->error($model->getError());
        }
        $this->success("数据写入成功");

        // 2.连接数据库，实例化数据库对象，完成入库
        // $query=Db::name('category');
        // 判断要写入的分类名称是否重复
        // fetchSql(true)可以用来输出sql语句，错与对都会输出，都不会执行语句
        // if($model->where('cate_name',$data['cate_name'])->find()){
        //     // 找到相同的分类名称就报错
        //     $this->error('分类名称重复');
        // }
        //分类名称不重复就插入分类数据 
        // 法一：直接写
        // $res = $query->insert($data);
        // $this->success("操作完成",'admin/index/index');

        //法二：抛出错误 
        // 设置一个标识，$flag为true，为了防止在
        // $flag=true;
        // try {
        //     // insert方法，插入一条数据
        //     $res = $query->insert($data);
        // } catch (\Exception $e) {
        //     $flag = false;
        //     return "数据入库错误";
        // }
        // if($flag){
        //     // success()方法实现跳转,回到上一级目录  
        //     $this->success("操作完成",'admin/category/add');
        // }else{
        //     $this->error("操作失败");
        // }
    }
}