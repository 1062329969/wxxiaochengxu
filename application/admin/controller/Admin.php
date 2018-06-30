<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台基类继承顶级基类 所有的后台控制器继承该类
 */
namespace app\admin\controller;

class Admin extends GlobalCheck
{
    protected function _initialize()
    {
         parent::_initialize();
    }
}
