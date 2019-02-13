<?php

namespace app\admin\controller;

use think\Controller;
use tools\AuthCode;

class Auth extends Controller
{
    private static $keyList = ['AdminLogin'];

    /**
     * @return \think\Response
     */
    public function getCodeImg()
    {
        $key = input('get.page');
        if (!empty($_SERVER['HTTP_VIA']) || !in_array($key, self::$keyList)) {
            return response('Access Denied', 403);
        }
        header('HTTP/1.1 200 OK');
        AuthCode::$imageH = 80;
        AuthCode::$length = 4;
        AuthCode::$imageL = 390;

        AuthCode::$useNoise = true;  //是否启用噪点
        AuthCode::$useCurve = true;   //是否启用干扰曲线
        AuthCode::entry($key, true);
        exit;
    }

    /**
     * @return \think\Response|\think\response\Json
     */
    public function postCheckCodeImg()
    {
        $key = input('post.page');
        if (!empty($_SERVER['HTTP_VIA']) || !in_array($key, self::$keyList)) {
            return response('Access Denied', 403);
        }
        $Ret = AuthCode::CheckAuthCode($key, input('post.click_list/a'));
        if ($Ret) {
            session('Check' . $key . 'AuthCode', true);
            return json(['state' => 1, 'msg' => '成功通过人机验证']);
        } else {
            return json(['state' => 0, 'msg' => '验证失败,请重试']);
        }
    }

    /**
     * @return \think\response\Json
     */
    public function postLogin()
    {
        $username = input('post.username/s');
        $password = input('post.password/s');
        if (!session('CheckAdminLoginAuthCode'))
            return json(['status' => 0, 'msg' => '还没有通过人机验证']);
        if (empty($username))
            return json(['status' => 0, 'msg' => '账号不能为空']);
        if (empty($password))
            return json(['status' => 0, 'msg' => '密码不能为空']);
        $result = unserialize(getServerConfig('adminAccount'));
        if (empty($result)) {
            session('CheckAdminLoginAuthCode', null);
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        if ($result['username'] != $username) {
            session('CheckAdminLoginAuthCode', null);
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        if (hash('sha256', hash('sha256', $password) . $result['salt']) != $result['password']) {
            session('CheckAdminLoginAuthCode', null);
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        session('username', $username, 'admin');
        //save data
        return json(['status' => 1, 'msg' => '登陆成功']);
    }

    public function getExit()
    {
        session('username', null, 'admin');
        return json(['status' => 1, 'msg' => '注销成功']);
    }
}