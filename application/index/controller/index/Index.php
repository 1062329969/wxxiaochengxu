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
        $list = Db::name('article')->where(['a_state'=>1])->paginate(4)->toArray();
        return json($list);
    }

}
