<?php
// 每次指定application命名空间比较麻烦，tp框架改写了application目录对应的命名空间为app
namespace app\admin\controller;
/**
 * 后台首页控制器
 */
// 在同一级不需要引入
class Index extends Common{
    // 后台首页控制器的方法
    public function index(){
        return $this->fetch();
    }
    // 渲染首页顶部的模板
    public function top(){
        return $this->fetch();
    }
    // 渲染首页导航的模板
    public function menu(){
        $this->assign('menus',$this->_user['menus']);  
        return $this->fetch();
    }
    // 渲染首页主体的模板
    public function main(){
        return $this->fetch();
    }
}