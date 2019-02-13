<?php

namespace app\user\controller;

use think\Controller;
use tools\AuthCode;

class Auth extends Controller
{
    private static $keyList = ['UserLogin'];

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
        $uid      = input('post.uid/d');
        $password = input('post.password/s');
        if (!session('CheckUserLoginAuthCode'))
            return json(['status' => 0, 'msg' => '还没有通过人机验证']);
        if (empty($uid))
            return json(['status' => 0, 'msg' => '账号不能为空']);
        if (empty($password))
            return json(['status' => 0, 'msg' => '密码不能为空']);
        if (strlen($password) != 32) {
            session('CheckUserLoginAuthCode', null);
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        $mysql  = db();
        $result = $mysql->table('epay_user')->where('id', $uid)->field('key,isBan')->limit(1)->select();
        if (empty($result)) {
            session('CheckUserLoginAuthCode', null);
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        if ($result[0]['key'] != $password) {
            session('CheckUserLoginAuthCode', null);
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        if($result[0]['isBan'])
            return json(['status'=>0,'msg'=>'账号已被封禁，无法登陆']);
        session('uid', $uid, 'user');
        //save data
        return json(['status' => 1, 'msg' => '登陆成功']);
    }

    public function getExit()
    {
        session('uid', null, 'user');
        return json(['status' => 1, 'msg' => '注销成功']);
    }
}