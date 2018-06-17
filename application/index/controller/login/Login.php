<?php
namespace app\index\controller\login;

use app\index\controller\Xcx;
use think\Config;
use think\Db;
use think\Session;
use ApiOauth\ApiOauth;

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
            $this->success('登录成功');
        }else{
            $this->error('登录失败');
        }
    }

    public function getopenid(){
        $code = input('post.code/s');
        $info= ['appid'=>Config::get('app.appid'),'appsecret'=>Config::get('app.appsecret')];
        $ApiOauth=new ApiOauth();
        return $ApiOauth->getopenid($info,$code);
    }

    public function adduser(){
        $code = input('post.code/s');
        $userinfo = input('post.userinfo/a');
        $info= ['appid'=>Config::get('app.appid'),'appsecret'=>Config::get('app.appsecret')];
        $ApiOauth=new ApiOauth();
        $openidarr = $ApiOauth->getopenid($info,$code);
        $openid = $openidarr['info']['openid'];
        $user = Db::name('user')->where(['u_openid'=>$openid])->find();

        $datas = [
            'u_wxname'=>$userinfo['nickName'],
            'u_logintime'=>time(),
            'u_wxpic'=>$userinfo['avatarUrl'],
            'u_wxsex'=>$userinfo['gender'],
            'u_openid'=>$userinfo['openid']
        ];
        if($user){
            $res = Db::name('user')->where(['u_id'=>$user['u_id']])->update($datas);
            Session::set('openid',$user['u_openid']);
            Session::set('uid',$user['u_id']);
        }else{
            $datas['u_name'] = $userinfo['nickName'];
            $datas['u_addtime'] = time();
            $res = Db::name('user')->update($datas);
            Session::set('openid',$userinfo['u_openid']);
            Session::set('uid',Db::getLastInsID());
        }
        if($res) {
            $this->success('登录成功');
        }else{
            $this->error('登录失败');
        }
    }
}
