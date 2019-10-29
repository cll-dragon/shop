<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
 * 角色模型
 */
class Role extends Model{
    // 角色删除
    public function remove($role_id){
        $this->where('id',$role_id)->delete();
    }
    // 角色添加
    public function add($data){
        // 查询数据库里面是否存在要添加的角色名称
        if($this->get(['role_name'=>$data['role_name']])){
            $this->error('角色名称重复');
            return false;
        }
        // allowField(true)过滤非数据表字段
        $this->isUpdate(false)->allowField(true)->save($data);
    }
}