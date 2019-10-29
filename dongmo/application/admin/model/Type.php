<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/**
 * 类型模型
 */
class Type extends Model{
    // 从数据库里面移除类型数据
    public function remove($type_id){
        return $this->where('id',$type_id)->delete();
    }
}