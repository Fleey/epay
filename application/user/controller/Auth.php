<?php

namespace app\user\controller;

use think\Controller;
use think\Db;
use tools\AuthCode;
use tools\Geetest;

class Auth extends Controller
{
    private static $keyList = ['UserLogin'];

    public function getGeetestInfo()
    {
        $config    = getConfig();
        $isGeetest = !empty($config['geetestCaptchaID']) && !empty($config['geetestPrivateKey']);
        if (!$isGeetest)
            return json(['status' => 0, 'msg' => '极验证接口尚未开启']);
        $gtSDK  = new Geetest($config['geetestCaptchaID'], $config['geetestPrivateKey']);
        $data   = [
            'client_type' => $this->request->isMobile() ? 'h5' : 'web',
            'ip_address'  => $this->request->ip()
        ];
        $status = $gtSDK->pre_process($data, 1);
        session('gtServerStatus', $status);
        return json($gtSDK->get_response());
    }


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
        $uid      = input('post.uid/d');
        $password = input('post.password/s');

        $config    = getConfig();
        $isGeetest = !empty($config['geetestCaptchaID']) && !empty($config['geetestPrivateKey']);
        if (!$isGeetest) {
            if (!session('CheckUserLoginAuthCode'))
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
        if (empty($uid))
            return json(['status' => 0, 'msg' => '账号不能为空']);
        if (empty($password))
            return json(['status' => 0, 'msg' => '密码不能为空']);
        if (strlen($password) != 32) {
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        $result = Db::table('epay_user')->where('id', $uid)->field('key,isBan')->limit(1)->select();
        if (empty($result)) {
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        if ($result[0]['key'] != $password) {
            return json(['status' => -1, 'msg' => '账号或密码不正确']);
        }
        if ($result[0]['isBan'])
            return json(['status' => 0, 'msg' => '账号已被封禁，无法登陆']);
        session('uid', $uid, 'user');
        $clientIp = getClientIp();
        addServerLog($uid,1,$clientIp, getIpSite($clientIp));
        //save data
        return json(['status' => 1, 'msg' => '登陆成功']);
    }

    public function getExit()
    {
        session('uid', null, 'user');
        return json(['status' => 1, 'msg' => '注销成功']);
    }
}