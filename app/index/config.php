<?php
//配置文件
return [
    'expire'  => 10,
    'session' => [
        'expire' => 86400,
    ],

    'prefix'           => 'index',
    'view_replace_str' => [
        '__PUBLIC__'          => '',
        '__ROOT__'            => '',
        '__PUBLIC_CSS__'      => '/static/index/css',
        '__PUBLIC_IMG__'      => '/static/index/images',
        '__PUBLIC_JS__'       => '/static/index/js',
        '__BACK_VERSION__'    => time(),
    ],

];