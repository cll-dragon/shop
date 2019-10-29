<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
/**
 * 登录控制器
 */
// 继承Common会死循环，一直未登录,所以继承Controller控制器父类
class Login extends Controller{
    // 退出登录，删除cookie
    public function logout(){
        // 删除cookie，实现退出
        cookie('user_info',null);
        $this->success('ok','index');
    }
    // 显示登录页面
    public function index(Request $request){
        if($request->isGet()){
            return $this->fetch();
        }
        // 比对验证码是否匹配
        // $obj = new \think\captcha\Captcha();
        // if(!$obj->check(input('captcha'))){
        //     $this->error('验证码错误');
        // }
        $model=model('Admin');
        // dump(input('remember'));exit;
        // 调用自定义的模型类Admin里的login方法判断是否登录成功
        // input('remember'),是否记住登录信息
        $result=$model->login(input('username'),input('password'),input('remember'));
        if($result===false){
            $this->error($model->getError());
        }
        // 跳转到后台主页，跳转到别的控制器，要带上控制器名
        $this->success('ok','index/index');
    }
    // 生成验证码
    public function captcha(){
        $config=[
            'length'=>4,
            'codeSet'=>'1234567890',
            // 是否添加杂点
            'useNoise'=>false,
            // 是否画混淆曲线
            'useCurve'=>false
        ];
        // 修改Captcha类里面的配置文件
        $obj=new \think\captcha\Captcha($config);
        // 生成验证码返回
        return $obj->entry();
    }
}