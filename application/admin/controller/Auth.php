<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use tools\AuthCode;
use tools\Geetest;

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
        AuthCode::$imageL = 360;

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
        $username  = input('post.username/s');
        $password  = input('post.password/s');
        $config    = getConfig();
        $isGeetest = !empty($config['geetestCaptchaID']) && !empty($config['geetestPrivateKey']);
        if (!$isGeetest) {
            if (!session('CheckAdminLoginAuthCode'))
                return json(['status' => 0, 'msg' => '还没有通过人机验证']);
        } else {
            $data  = [
                'client_type' => $this->request->isMobile() ? 'h5' : 'web',
                'ip_address'  => $this->request->ip()
            ];
            $gtSDK = new Geetest($config['geetestCaptchaID'], $config['geetestPrivateKey']);
            if (session('gtServerStatus')) {
                $result = $gtSDK->success_validate(input('post.geetest_challenge'), input('post.geetest_validate'), input('post.geetest_seccode'), $data);
            } else {
                $result = $gtSDK->fail_validate(input('post.geetest_challenge'), input('post.geetest_validate'), input('post.geetest_seccode'));
            }
            if (!$result)
                return json(['status' => 0, 'msg' => '还没有通过人机验证']);
        }
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
        Db::table('epay_log')->insert([
            'uid'        => 1,
            'type'       => 1,
            'ipv4'       => getClientIp(),
            'createTime' => getDateTime(),
            'data'       => 'login admin system'
        ]);
        session('username', $username, 'admin');
        $clientIp = getClientIp();
        Db::table('epay_log')->insert([
            'uid'        => 1,
            'type'       => 1,
            'ipv4'       => $clientIp,
            'createTime' => getDateTime(),
            'data'       => getIpSite($clientIp)
        ]);
        //save data
        return json(['status' => 1, 'msg' => '登陆成功']);
    }

    public function getExit()
    {
        session('username', null, 'admin');
        return json(['status' => 1, 'msg' => '注销成功']);
    }
}