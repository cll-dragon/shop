<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/**
 * 商品模型
 */
class Goods extends Model{
    // 删除商品数据
    public function remove($goods_id){
        return Goods::where('id',$goods_id)->delete();
    }
    // 设置商品的删除状态
    public function setDelStatus($goods_id,$status){
        // setField()方式就是修改属性值
        return Db::name('goods')->where('id',$goods_id)->setField('is_del',$status);
    }

    // 商品编辑入库
    public function editGoods($data){
        // 需要针对三个状态都修改，不然其它两个状态使用默认值
        // 没有传递数据进来就为0
        $data['is_hot'] = isset($data['is_hot']) ? 1:0;
        $data['is_new'] = isset($data['is_new']) ? 1:0;
        $data['is_rec'] = isset($data['is_rec']) ? 1:0;
        // 数据写入
        $this->isUpdate(TRUE)->allowField(TRUE)->save($data,['id'=>$data['id']]);
    }

    // 切换商品状态
	public function changeStatus($goods_id,$field)
	{
		// 获取要修改的商品信息
		$goods_info = Db::name('goods')->find($goods_id);
		// 计算最终要修改的内容
        $status = $goods_info[$field] ? 0 : 1;
        // echo $status;exit;
        // setField方法主要用于修改单个字段的内容 第一个参数字段名称 第二个参数修改的值
		Db::name('goods')->where('id',$goods_id)->setField($field,$status);
		return $status;
	}
    // 获取商品列表数据
    public function listData($is_del=0){
        // 设置分类的搜索条件
        $where=['is_del'=>$is_del];
            // input('cate_id')获取商品数据的分类id
        if(input('cate_id')){
            // 使用分类作为条件进行搜索
            $where['cate_id']=input('cate_id');
        }
        // 获取推荐状态
        $intro_type=input('intro_type');
        if($intro_type){
            // 使用推荐状态作为条件
            $where[$intro_type]=1;
        }
        if(input('keyword')){
            // 使用关键字搜索
            $where['goods_name']=['like','%'.input('keyword').'%'];
        }
        // dump($where);exit;
        // 调用分页的方法，传递每页显示数据条数
        // paginate(2,false,['query'=>input()]解决搜索后翻页条件丢失
        $data = db('goods')->where($where)->paginate(2,false,['query'=>input()]);
        // dump($data); exit;
        // echo db('goods')->getLastSql();exit;
        return $data;

        // 使用模型对象获取数据
        // return $this->all();
        // 使用query对象获取数据
        // return db('goods')->select();
    }
    // 商品添加入库
    public function addGoods($data){
        // 追加商品入库时间
        $data['addtime']=time();
        // dump($data);exit;

        // 向多个表里面写入数据时要使用事务功能，防止出错
        // 开启事务
        // dump($data);exit;
        Db::startTrans();
        try {
            // 数据写入,allowField()过滤非数据表字段的数据
            // 向商品表里面写入数据
            $this->isUpdate(false)->allowField(TRUE)->save($data);
            // 实现向商品属性值表写入内容
            // 获取最后写入数据的ID
            $goods_id = $this->getLastInsId();
            // dump($goods_id);
            // 接受属性的ID,接收带参数的数组格式的数据需要在后面加个/a
            // 有多个属性，就有多个属性id
            $attr_ids = input('attr_ids/a');
            // dump($attr_ids);
            // 接受属性值,属性值也有多个
            $attr_values = input('attr_values/a');
            // dump($attr_values);exit;
            // 调用自定义的模型方法完成入库，在GoodsAttr模型里面创建addAll方法
            model('GoodsAttr')->addAll($goods_id,$attr_ids,$attr_values);
            // 调用GoodsImg模型类里面的方法实现文件上传并且入库
            model('GoodsImg')->uploadImages($goods_id);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error='数据写入错误';
            return false;
        }  
    }
}