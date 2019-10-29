<?php
namespace app\index\controller;
use think\Db;
use think\Model;
class Goods extends Common{
    public function index(){
        // 接收参数
        $goods_id = input('goods_id');
        // dump($goods_id);die;
        $goods = model('Goods')->getGoodsInfo($goods_id);
        // dump($goods);die;
        $this->assign('goods',$goods);
        return $this->fetch();
    }
}