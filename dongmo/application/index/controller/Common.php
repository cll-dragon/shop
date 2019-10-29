<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
/**
 * 前端的公共控制器
 */
class Common extends Controller{
    public function __construct()
    {
        parent::__construct();
        // 获取分类的数据
        $category = Db::name('category')->select();
        $this->assign('category',$category);   
    }
}