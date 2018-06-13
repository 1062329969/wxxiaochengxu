<?php
namespace app\index\controller;

use think\Controller;
use think\Cookie;
use think\Session;

class Xcx extends Controller{

    public function checkLogin(){
        if(!Session::has('loginId')){
            $this->error('请登录');
        }
    }

}
