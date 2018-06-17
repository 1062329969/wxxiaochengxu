<?php
namespace app\index\controller\member;

use app\index\controller\Xcx;
use think\Config;
use think\Db;
use think\Session;
use ApiOauth\ApiOauth;

class Member extends Xcx
{
    public function _initialize(){
//        $this->checkLogin();
    }

    public function index(){
        $this->checkLogin();
    }

    public function getuser(){
        echo session_id();
        var_dump(Session::get());die;
    }
}
