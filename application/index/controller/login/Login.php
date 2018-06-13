<?php
namespace app\index\controller;

use think\Db;
use think\Session;

class Login extends Xcx
{
    public function _initialize(){
//        $this->checkLogin();
    }

    public function index(){
        $this->checkLogin();
    }

    public function memberlogin(){
        $username = input('post.username/s');
        $password = input('post.password/s');
        $user = Db::name('user')->where(['u_name'=>$username,'u_passwd'=>$password])->find();
        if($user){
            Session::set('user',['username'=>$username,'password'=>$password]);
            $this->success('登录成功');
        }else{
            $this->error('登录失败');
        }
    }
}
