<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台主页
 */

namespace app\admin\controller;

class Main extends Admin
{
	//判断session是否已经过期 过期自动登录
	protected function _initialize()
	{
		 parent::_initialize();
	}

	public function index()
    {
        return $this->fetch();
    }

    public function index1(){
        echo 1;die;
    }
}
