<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[member]'     => [
        '/login'   => ['@index/login/Login/index'],
        '/dologin'   => ['@index/login/Login/memberlogin'],
        '/mylove' => ['@index/member/Member/getuser'],
        '/myewm' => ['@index/member/Member/myewm'],
        '/myinfo' => ['@index/member/Member/myinfo'],
        '/scancode' => ['@index/member/Member/scancode']
    ],
    '[index]'     => [
        '/list'   => ['@index/index/Index/getlist'],
    ],
    'adduser'=>'@index/login/Login/adduser',
    'getopenid'=>'@index/login/Login/getopenid',

];
