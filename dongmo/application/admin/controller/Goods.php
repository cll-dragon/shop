<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
/**
 * 商品的控制器
 */
class Goods extends Common{
    // 实现类型切换显示属性方法,获取类型，根据类型获取属性，根据属性获取属性值
    public function showAttr(){
        $type_id = input('type_id');
        // 在attribute表中查询类型下的属性信息
        $data = model('Attribute')->getAttrsByTypeId($type_id);
        // dump($data);exit;
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 彻底删除数据方法
    public function delete(){
        $goods_id=input('id');
        // 调用自定义的模型类方法删除数据库商品数据
        model('Goods')->remove($goods_id);
        $this->success('ok');
    }

    // 商品还原
    public function rollback(){
        $goods_id = input('id');
        // 调用模型方法将伪删除状态修改还原
        model('Goods')->setDelStatus($goods_id,0);
        $this->success('ok','recycle');
    }

    // 商品回收站
    public function recycle(){
        $model = model('Goods');
        // 使用模型对象调用自定义的方法
        $data = $model -> listData(1);
        // dump($data);
        $this->assign('data',$data);
        // 获取所有的分类数据赋值给模板的搜索条件
        $tree = model('Category')->getCateTree();
        $this->assign('tree',$tree);
        return $this->fetch();
    }

    //实现商品伪删除 
    public function remove(){
        $goods_id=input('id');
        // 调用自定义的模型类方法实现商品伪删除
        // setDelStatus方法的第二个参数设置为1就是伪删除，0就是还原
        model('Goods')->setDelStatus($goods_id,1);
        $this->success('ok','recycle');
    }

    // 修改编辑商品信息,依赖注入request对象
    public function edit(Request $request){
        $model = model('Goods');
        // 判断是否是get请求
        if($request->isGet()){
            // 获取商品id
            $goods_id = input('id');
            // 查询该商品信息
            $info = $model->get($goods_id);
            // 将商品信息赋值给模板
            $this->assign('info',$info);
            // 获取所有的类型数据
            $type=model('Type')->all();
            $this->assign('type',$type);
            // 获取商品对应的属性信息,获取属性信息，经过格式化变为了以属性id为下标的地址
            $attrs=model('GoodsAttr')->getAttrByGoodsId($goods_id);  
            $this->assign('attrs',$attrs);
            // 获取所有分类信息
            // $model是商品信息的模型对象，这里要使用分类的模型对象
            $tree=model('Category')->getCateTree();
            $this->assign('tree',$tree);
            return $this->fetch();
        }
        $data=input();
        // 使用自定义验证器验证数据
        $result = $this->validate($data,'Goods');
        if($result !== TRUE){
            $this->error($result);
        }
        // 检查货号是否正常
        if($this->checkGoodsSn($data,true) === FALSE){
            $this->error('货号错误');
        }
        // 处理文件上传，修改商品可以上传图片，也可以不上传图片
        $this->uploadGoodsImg($data,false);
        // 调用自定义的模型类里面的方法修改商品数据入库
        $result = $model->editGoods($data);
        if($result === FALSE){
            $this->error($model->getError());
        }
        // 修改成功，跳转到index页面
        $this->success('ok','index');

    }
    // 处理商品的状态切换
	public function changeStatus()
	{
		$model = model('Goods');
		// 调用自定义的模型方法处理更换状态 返回的结果为最终的0或者1
		$status = $model->changeStatus(input('goods_id'),input('field'));
		if($status === false){
			// 表示修改状态故障
			return json(['code'=>0,'msg'=>$model->getError()]);
        }
        // json_encode()，php里面的内置函数，将数组转换为json格式的字符串
        // json()方法是TP内的助手函数
		// 等价于原生的echo  json_encode()
		return json(['code'=>0,'msg'=>'ok','status'=>$status]);
	}
    // 商品列表显示
    public function index(){
        // 实例化模型对象
        $model = model("Goods");
        // 调用自定义的模型类方法获取商品数据
        $data=$model->listData();
        // dump($data);
        // 将数据赋值给模板
        $this->assign('data',$data);
        // 获取所有的分类数据赋值给模板的搜索条件
        $tree=model('Category')->getCateTree();
        $this->assign('tree',$tree);
        // 渲染模板
        return $this->fetch();
    }

    // 商品添加
    public function add(Request $request){
        // define("NAME","商品新添加");
        // echo NAME;exit;
        // dump($request->method());
        // 判断请求方式
        if($request->isGet()){
            // 获取所有的分类数据
            $type=model('Type')->all();
            $this->assign('type',$type);
            // 创建分类的模型对象
            $model1 = model('Category');
            // 获取所有的分类数据
            $tree = $model1->getCateTree();
            // 将分类数据赋值给模板
            $this->assign('tree',$tree);
            // 渲染模板
            return $this->fetch();
        }
        // 接收数据,$data得到的是数组
        $data = input();
        // dump($data);exit;
        // echo goods_name;
        // 创建商品的模型对象
        $model = model('Goods');
        // dump($data);exit;
        // 检查名称是否存在
        // if (!$data['goods_name']) {
        //     $this->error('商品名称必须填写');
        // }
        // // 检查价格
        // if($data['shop_price'] <= 0 || $data['shop_price']>$data['market_price']){
        //     $this->error('价格错误');
        // }
        // // 检查分类id
        // if($data['cate_id']<=0){
        //     $this->error('分类必须选择');
        // }
        // 调用控制器检查数据
        $result=$this->validate($data,'Goods');
        if($result !== TRUE){
            $this->error($result);
        }

        // 检查商品货号是否正常
        if($this->checkGoodsSn($data) === false){
            $this->error('货号错误');
        }
        // 调用自定义的方法完成商品图片文件上传
        $this->uploadGoodsImg($data);
        // dump($data);exit;
        // 调用自定义模型方法完成商品数据入库
        $result = $model->addGoods($data);
        
        if($result===false){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    // 检查货号是否正确方法,使用传址方式，使用同一个$data变量
    protected function checkGoodsSn(&$data,$is_filter=false){
        if($data['goods_sn']){
            // 创建$where数组保存检查条件
            $where=[
                'goods_sn'=>$data['goods_sn']
            ];
            // 传进来的$is_filter为true，表示不需要验证数据本身,在添加数据时就不需要这个条件
            if($is_filter){
                // 修改操作，需要排除当前数据
                $where['id']=['neq',$data['id']];
            }
            // 用户提交了货号，检查是否重复,查找数据库里面是否有该货号的商品
            if(model('Goods')->get($where)){
                return false;
            }
        }else{
            // 没有提交货号，生成一个不重复的货号
            $data['goods_sn']=uniqid();
        }
    }  
    // 商品图片文件上传方法,传址方式
    protected function uploadGoodsImg(&$data,$is_must=true){
        // 使用rquest对象调用file()方法获取File类的对象，
        // goods_img是模板里面定义的name属性值
        $file = request()->file('goods_img');
        if(!$file){
            if($is_must){
                // 没有文件上传
                $this->error('文件必须上传');
            }
            // 当编辑商品信息时图片非必须，如果没有上传，代码后续执行出错
            return true;
        }
        // 调用方法上传文件
        $info=$file->validate(['ext'=>'jpg,png'])->move('uploads');
        // 判断返回的结果，如果为false表示文件上传异常，为对象表示文件上传正常
        if(!$info){
            $this->error('文件上传错误');//$file->getError()
        }
        // 计算出上传之后的文件地址
        $file_path='uploads/'.$info->getSaveName();
        // echo $file_path;
        // 计算的格式：uploads/20190530\dd59d68e4a408741f3c3dc1af4ed836a.jpg
        // 修改地址中的\为/,因为在linux系统中\不能被解析
        $file_path=str_replace('\\','/',$file_path);
        // 打开图片
        $img=\think\Image::open($file_path);
        // 计算生成缩略图的地址，格式 uploads/20190530/thumb_364378d45e610fef5d7dd3eba5cccf15.jpg
        // 由于文件上传先执行，当前的日期目录一定存在
        $goods_thumb='dongmo/public/uploads/'.date('Ymd').'/thumb_'.$info->getFilename();
        // dump($goods_thumb);exit;
        // 生成缩略图
        $img->thumb(150,150)->save($goods_thumb);
        // 修改$data变量下的数据，最终当调用addGoods模型方法时图片信息自动入库，
        // 不同方法使用同一数据要使用传址方式作为参数
        $data['goods_img']=$file_path;
        $data['goods_thumb']=$goods_thumb;
    }
}