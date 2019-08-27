<?php

namespace app\index\controller;

use think\Cache;

class Test extends TestBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if (IS_POST) {
            $md5    = md5(123456);
            $expire = config('expire');
            Cache::set($md5, 1, $expire);
            //print_r($this->requestData['password']);
            //exit;
            return $this->sendSuccess($md5);
        }
    }

    public function getToken()
    {
        print_r(Cache::get('user.id' . 123));
    }

    public function user()
    {
        return $this->sendSuccess('用户中心');
    }

}
