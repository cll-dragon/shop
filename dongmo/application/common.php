<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 对分类的数据进行格式化操作
 * $data 数组array 原始的分类数据
 * id int 查找的分类的id
 * lev int 分层
 * 分层之后返回 return array
 */
// 无限极分类
function get_tree($data,$id=0,$lev=0){
    // 创建一个静态数组保存最终的分类结果
    static $arr=[];
    foreach($data as $key=>$value){
        if($value['parent_id']==$id){
            // 如果该元素的父类id等于参数id，就是要找的分类，$value['lev']新添加的字段，方便分类分层
            $value['lev']=$lev;
            $arr[]=$value;
            // 根据当前元素的id递归查找子分类
            get_tree($data,$value['id'],$lev+1);
        }
    }
    return $arr;
}
