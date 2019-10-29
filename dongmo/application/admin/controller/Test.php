<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
/**
*功能测试的代码
*/
class Test extends Controller{
    // 定时向test表中发送数据
    public function sendData(){
        $query = Db::name('test');
        $insertData = [
            "name" => "陈子龙",
        ];
        // dump($insertData);exit;
        $query -> insert($insertData);
    }
    public function makeCaptcha(){
        // 指定验证码生成的格式
        $config=[
            'codeSet'=>'0123456789',
            'length'=>4
        ];
        // 传递配置信息到Captcha类里面，覆盖里面相同的配置信息
        $obj=new \think\captcha\Captcha($config);
        // 生成验证码返回s
        return $obj->entry();
    }

    // 验证码的快速使用
    public function yzm(){
        echo md5(123456);exit;
        // request()可以获取request对象
        if(request()->isGet()){
            return $this->fetch();
        }
        $obj=new \think\captcha\Captcha();
        dump($obj->check(input('captcha')));
        // dump(captcha_check(input('captcha')));
    }
    // 验证码的快速使用
    public function captcha(){
        // request()可以获取request对象
        if(request()->isGet()){
            return $this->fetch();
        }
        dump(captcha_check(input('captcha')));
    }

    // 单文件上传,  依赖注入$request对象
    public function uploadOne(Request $request){
        if($request->isGet()){
            return $this->fetch();
        }
        // $req=request(); 使用助手函数获取请求对象
        // 获取File类的对象,file方法的参数为input的name值
        $file = $request->file('image');
        // dump($file);
        // 调用方法上传，move()方法的参数需要指定上传的根目录
        $info=$file->move('uploads');
        dump($info);
        // 获取上传之后的信息
        echo $info->getSaveName().'<br>';
        echo $info->getFileName();

    }
    
    public function index(){
        return 'index';
    }
    public function makeUrl(){
        echo url('index');
    }
}