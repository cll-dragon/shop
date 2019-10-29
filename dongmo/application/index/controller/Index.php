<?php
namespace app\index\controller;
/**
 * 前台首页控制器
 */
class Index extends Common{
    public function index(){
        $goods_model = model('Goods');
        // 保存推荐状态的商品
        $data=[];
        // 获取热卖的数据
        $data[] = $goods_model->getRecGoods('is_hot');
        // 获取推荐状态的数据
        $data[] = $goods_model->getRecGoods('is_rec');
        // 获取新品状态的数据
        $data[] = $goods_model->getRecGoods('is_new');
        // dump($data);exit;
        // 分配首页的标识控制分类菜单的显示问题
        // dump($data);die;
        $this->assign('is_show',1);
        $this->assign('data',$data);
        return $this->fetch();
    }
}
