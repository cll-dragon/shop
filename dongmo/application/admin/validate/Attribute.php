<?php
namespace app\admin\validate;
use think\Validate;
/**
 * 属性验证器
 */
class Attribute extends Validate{
    protected $rule = [
        'attr_name'=>'require|unique:attribute'
    ];
}