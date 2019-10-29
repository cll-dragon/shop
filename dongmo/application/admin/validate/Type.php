<?php
namespace app\admin\validate;
use think\Validate;
/**
 * 类型验证器
 */
class Type extends Validate{
    protected $rule = [
        // require表示不能为空，length:2,10；表示长度在2到10之间
        // unique指定该数据必须唯一， :后面指定在type表中字段唯一
        'type_name'=>'require|length:2,10|unique:type'
    ];
}