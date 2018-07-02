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
//        $id = Session::get('uid');
        $openid = input('param.openid');
        $user = Db::name('user')->where(['u_openid'=>$openid])->field('u_id')->find();
        $id = $user['u_id'];
        $erw = Db::name('user')->field('u_ewm')->find();
        if($erw['u_ewm']){
            $erwpath = $erw['u_ewm'];
        }else{
            $a = file_get_contents('http://qr.liantu.com/api.php?text='.$openid);
            $filename = time().'.png';
            $path = ROOT_PATH . 'public' . DS . 'static/uploads/ewm/'.$filename;
            file_put_contents($path,$a);
            Db::name('user')->where(['u_id'=>$id])->update(['u_ewm'=>'/static/uploads/ewm/'.$filename]);
            $erwpath = '/static/uploads/ewm/'.$filename;
        }
        return json(['path'=>$erwpath,'id'=>$id]);
    }

    public function myinfo(){
        $openid = input('post.openid');
        $user = Db::name('user')->where(['u_openid'=>$openid])->find();
        return json($user);
    }

    public function scancode(){
        $result = input('param.result');
        $openid = input('param.openid');
        $u_id = Db::name('user')->where(['u_openid'=>$openid])->field('u_id')->find();
        $ouser = Db::name('user')->where(['u_openid'=>$result])->field('u_id')->find();
        $data = [];
        $data['as_uid'] = $u_id['u_id'];
        $data['as_ouid'] = $ouser['as_ouid'];
        $data['as_openid'] = $result;
        $data['as_addtime'] = time();
        $data['as_updatetime'] = time();
        $data['as_state'] = 0;
        $val = Db::name('association')->insert($data);
        if($val){
            $this->success('关联成功，请等待对方回应');
        }else{
            $this->error('关联失败，请重试');
        }
    }
}
