<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 权限模型
 */
class Rule extends Model{
    // 获取所有格式化之后的数据
    public function getRules($id=0){
        // 获取所有的分类数据
        $data = $this->all();
        // 调用common.php里面的公共方法get_tree(),公共方法不需要引用
        return get_tree($data,$id);
    }
}