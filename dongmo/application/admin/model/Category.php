<?php
namespace app\admin\model;
use think\Model;
/**
 * 分类模型
 */
class Category extends Model{
    // 编辑分类的模型方法
    public function editCategory($data){
        // 验证名称是否重复
        // 组装条件，条件排除当前修改的数据查找是否有重名
        // 条件就是  id不相等的相同分类名称
        $map = [
            'cate_name'=>$data['cate_name'],
            //  修改的本来就是自己，所以不能找自己，id不能相等
            'id'=>['neq',$data['id']]
            // neq,不相等
        ];
        // 找到数据，说明分类名称重复了，报错
        if($this->get($map)){
            $this->error="分类名称重复";
            return false;
        }
        // 所设置的上级分类不能是当前修改的分类下的任何一个子分类
        // 获取当前分类下的子分类
        $son = $this->getCateTree($data['id']);
        foreach($son as $value){
            // getAttr直接获取字段值
            if($data['parent_id'] == $value->getAttr('id')){
                $this->error="上级分类设置错误";
                return false;
            }
        }
        return $this->save($data,['id'=>$data['id']]);
    } 

    // 删除分类数据方法
    public function remove($cate_id){
        // dump($cate_id);exit;
        // 判断是否存在子分类,在数据库表里面查找是否有父类id等于这个携带的id
        if($this->get(['parent_id'=>$cate_id])){
            $this->error="该分类存在子分类不能删除";
            return false;
        }
        // 开始删除
        return $this->destroy($cate_id);
    }

    // 创建方法写入分类数据,  $data 是表单提交的数据
    public function insert($data){
        // 判断分类名称是否重复,判断from表单提交的分类名是否有重复的
        // 模型对象查找到了就算有重复的
        if($this->get(['cate_name'=>$data['cate_name']])){
            // 类型名重复，设置错误信息
            // Model基类里面有个error属性，可以设置错误信息，还有个getError()方法返回这个error属性
            // 然后在控制器里面通过模型对象调用getError()方法获取到错误信息，这个比较常用
            // 具体见tp框架day5,1.4分类添加17分钟处
            $this->error="分类名称重复";
            return false;
        }
        // 分类名不重复，完成入库,
        // isUpdate()参数为false表示插入数据，为true表示更新数据
        return $this->isUpdate(false)->save($data);
    }

    // 获取分类下的所有的数据
    // getCateTree方法可以获取自定分类的子分类
    public function getCateTree($id=0){
        // 获取所有的分类数据,  模型对象在控制器里面实例化
        $data = $this->all();
        // 数据进行格式化操作， 
        // 调用common.php里面的公共方法get_tree(),公共方法不需要引用
        return get_tree($data,$id);
    }
}