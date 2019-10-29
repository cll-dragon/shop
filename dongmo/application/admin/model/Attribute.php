<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 属性模型
 */
class Attribute extends Model{
    // 获取所有属性，根据type_id
    public function getAttrsByTypeId($type_id){
        // 查询属性表里指定类型的所有属性,同一个类型有好几个属性
        $data = Db::name('attribute')->where('type_id',$type_id)->select();
        // 将属性值里面的字符串变为数组格式，在渲染模板的时候能够正确显示
        // 录入方式为select选择时,$data是数组，需要一个个循环里面的数据
        foreach ($data as $key=>$value){
            if($value['attr_input_type']==2){
                // 将循环中的元素下的attr_values重新赋值，字符串变为数组
                $data[$key]['attr_values']=explode(',',$value['attr_values']);
            }
        }
        // dump($data);exit;
        return $data;
    }
    // 编辑属性写入
    public function editAttribute($data){
        // 数据编辑写入
        return $this->isUpdate(true)->allowField(true)->save($data,['id'=>$data['id']]);
    }
    // 删除数据
    public function remove($attr_id){
        return $this->where('id',$attr_id)->delete();
    }
}