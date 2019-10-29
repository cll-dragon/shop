<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
/**
 * 
 */
// 后期所有的后台控制器全部继承Common控制器
class Common extends Controller{
    // 是否检查权限
    public $is_check_rule = true;
    // 保存用户的完整信息
    public $_user = [];
    public function __construct(){
        // 先运行父类的构造方法
        parent::__construct();
        // 查看cookie里面是否有用户信息
        $user_info=cookie('user_info');
        if(!$user_info){
            $this->error('未登录','login/index');
        }
        // 将用户信息保存到类的属性中
        $this->_user = $user_info;
        // dump($this->_user);exit;
        if($this->_user['role_id']==1){
            // 超级管理员角色
            $this->is_check_rule = false;
            // 获取所有的权限信息，最终显示导航菜单
            $rules = Db::name('rule')->select();
            // dump($rules);exit;
        }else{
            // 普通角色下的用户，取出角色信息
            $role_info=Db::name('role')->where('id',$this->_user['role_id'])->find();
            // 根据角色具备的权限id获取到拥有的权限信息
            $rules = Db::name('rule')->where('id','in',$role_info['rule_ids'])->select();
        }
        // dump($rules);exit;
        // 循环对数据进行格式化操作
        foreach($rules as $key=>$value){
            // 使用user属性保存权限信息属性对应的为一维数组
            // 保存权限信息的数组元素的内容按照"控制器/方法"格式组装
            $this->_user['rules'][]=strtolower($value['controller_name'].'/'.$value['action_name']);
            // 将需要显示的权限信息记录到user属性中
            if($value['is_show']==1){
                $this->_user['menus'][]=$value;
            }
        }
        // dump($this->_user);exit;
        // 判断是否有权限访问
        if($this->is_check_rule){
            // 设置后台首页所有用户都具备权限
            $this->_user['rules'][]='index/index';
            $this->_user['rules'][]='index/top';
            $this->_user['rules'][]='index/menu';
            $this->_user['rules'][]='index/main';
            // 获取当前用户要访问的地址
            $action = strtolower(request()->controller().'/'.request()->action());
            if(!in_array($action,$this->_user['rules'])){
                $this->error('无访问权限');
            }
        }
    }
} 