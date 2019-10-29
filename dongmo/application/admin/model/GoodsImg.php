<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class GoodsImg extends Model{
    // 商品添加相册上传
    public function uploadImages($goods_id){
        $list=[];
        // 获取上传的图片文件,pics是file型input文本框的name属性
        $files=request()->file('pics');
        // dump($files);exit;
        foreach($files as $value){
            // 检查图片格式
            $info=$value->validate(['ext'=>'jpg,png'])->move('uploads/up');
            if(!$info){
                // 上传的图片不满足要求，跳过当前的图片
                continue;
            }
            // 计算图片的存储目录,$info->getSaveName()获取文件上传之后的存储目录,目录名 年月日 再加/文件名
            // str_replace()替换字符，文件地址里面\不兼容linux系统
            $goods_img='uploads/up/'.str_replace('\\','/',$info->getSaveName());
            // 生成缩略图
            $img=\think\Image::open($goods_img);
            // 计算缩略图的保存地址,$info->getFileName()获取上传之后的文件名称
            $goods_thumb='uploads/up/'.date('Ymd').'/thumb_'.$info->getFileName();
            // 生成缩略图
            $img->thumb(150,150)->save($goods_thumb);
            $list[]=[
                'goods_id'=>$goods_id,
                'goods_img'=>$goods_img,
                'goods_thumb'=>$goods_thumb
            ];
        }
        // dump($list);exit;
        if($list){
            Db::name('goods_img')->insertAll($list);
        }
    }
}