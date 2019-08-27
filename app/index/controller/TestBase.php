<?php

namespace app\index\controller;

use think\Cache;
use think\Controller;
use think\Request;
use think\Session;

class TestBase extends Controller
{
    private $noLogin = [
        'Test/login',
        'Test/register',
    ];

    public function __construct()
    {
        header("Access-Control-Allow-Origin: 'http://test.sujianxunc.com'");
        header('Access-Control-Allow-Headers:*');
        parent::__construct();
        $token = request()->header('Authorization');

        if (!defined('IS_AJAX')) $this->request->isAjax() ? define('IS_AJAX', true) : define('IS_AJAX', false);
        if (!defined('IS_GET')) ($this->request->method() == 'GET') ? define('IS_GET', true) : define('IS_GET', false);
        if (!defined('IS_POST')) ($this->request->method() == 'POST') ? define('IS_POST', true) : define('IS_POST', false);

        if (!defined('MODULE_NAME')) define('MODULE_NAME', $this->request->module());         //当前模块名称
        if (!defined('CONTROLLER_NAME')) define('CONTROLLER_NAME', $this->request->controller()); //当前控制器名称
        if (!defined('ACTION_NAME')) define('ACTION_NAME', $this->request->action());         //当前操作名称
        if (!in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $this->noLogin)) {
            $this->checkToken($token);
        }
    }

    private function checkToken($token)
    {
        $isTrue = Cache::get($token);
        if (!$isTrue) {
            echo sendError('token is expired', 401);
            exit;
        }
        $expire = config('expire');
        Cache::set($token, 1, $expire);
    }

    /**
     * 成功响应
     * @param array $data
     * @return array
     */
    public function sendSuccess($data = [])
    {
        $result = [
            'error'   => 0,
            'message' => 'success',
            'data'    => $data,
        ];
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 失败响应
     * @param array $data
     * @param int $code
     * @return \think\response\Json
     */
    public function sendError($data = [], $code = 400)
    {
        header("HTTP/1.1 400 Bad Request");
        $result = [
            'error'   => $code,
            'message' => $data
        ];
        return json($result, $code);
    }
}