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
        echo getsessionid();
        var_dump(Session::get());die;
    }

    public function myewm(){
        $id = Session::get('uid');
        $erw = Db::name('user')->field('u_ewm')->find($id);
        if($erw){
            $erwpath = $erw['u_ewm'];
        }else{
            $a = file_get_contents('http://qr.liantu.com/api.php?text=http://ihair.yongxianghui.net/member/reg/rd_id/'.$id.'.html');
            $filename = time().'.png';
            $path = ROOT_PATH . 'public' . DS . 'static/uploads/ewm/'.$filename;
            file_put_contents($path,$a);
            Db::name('user')->where(['u_id'=>$id])->update(['u_ewm'=>'/static/uploads/ewm/'.$filename]);
            $erwpath = '/static/uploads/ewm/'.$filename;
        }
        return json(['path'=>$erwpath,'id'=>$id]);
    }
}
