<?php
namespace app\index\controller\index;

use app\index\controller\Xcx;
use think\Db;

class Index extends Xcx
{
    public function _initialize(){
//        $this->checkLogin();
    }

    public function getlist(){
        $list = Db::name('article')->where(['a_state'=>1])->order('a_id desc')->paginate(4);
        foreach ($list as $k=>$v){
//            var_dump($list[$k]);
            $v['a_img'] = json_decode($v['a_img'],true)[0];
            $list[$k] = $v;
        }
        return json($list);
    }

}
