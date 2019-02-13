<?php

namespace app\user\controller;

use think\Controller;

class Index extends Controller
{
    public function loadTemplate($templateName = 'Login')
    {
        $config = getConfig();
        if (!file_exists(env('APP_PATH') . 'template/User/' . $templateName . '.php') ||
            ($templateName == 'Head' || $templateName == 'Footer' || $templateName == 'Sidebar'))
            return abort('404', '页面未找到');
        $data = [
            'webName' => $config['webName']
        ];
        $uid  = session('uid', '', 'user');
        if (empty($uid) && $templateName != 'Login')
            $this->redirect('/user/Login', [], 302);
        if ($templateName == 'Dashboard') {
            $userInfo                   = db()->table('epay_user')->where('id', $uid)->cache(60)->limit(1)->field('key,balance,rate')->select();
            $data['beforeSettleRecord'] = db()->table('epay_order')->where([
                ['uid', '=', $uid],
                ['isShield', '=', 0],
                ['status', '=', 1],
            ])->cache(300)->whereTime('endTime', 'yesterday')->sum('money');
            $data['balance']            = $userInfo[0]['balance'] / 1000;
            $data['key']                = $userInfo[0]['key'];
            $data['uid']                = $uid;
            $data['rate']               = $userInfo[0]['rate'] / 100;
            $data['totalOrder']         = db()->table('epay_order')->where('uid', $uid)->cache(120)->count('id');
            $data['settleRecord']       = db()->table('epay_settle')->cache(300)->field('createTime,money')->where('uid', $uid)->whereTime('createTime', 'week')->limit(7)->select();

        }
        return $this->fetch('/User/' . $templateName, $data);
    }

    public function getSettleList()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);
        $page = input('get.page/d', 1);
        if ($page <= 0)
            $page = 1;
        $mysql    = db();
        $result   = $mysql->table('epay_settle')->field('id,account,money,status,createTime')->order('id desc')->where('uid', $uid)->page($page, 15)->select();
        $totalRow = $mysql->table('epay_settle')->where('uid', $uid)->count('id');
        return json(['status' => 1, 'data' => $result, 'totalPage' => ceil($totalRow / 15)]);
    }

    public function getInfo()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);
        $mysql  = db();
        $result = $mysql->table('epay_user')->field('id,key,account,balance,email,qq,domain,clearType,username')->where('id', $uid)->select();
        $data   = $result[0];
        return json(['status' => 1, 'data' => $data]);
    }

    public function postInfo()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);
        $type = input('post.type/s');
        if ($type != 'settle' && $type != 'connectInfo')
            return json(['status' => 0, 'msg' => '保存信息类型错误']);
        if ($type == 'settle') {
            $settleType = input('post.settleType/d');
            $account    = input('post.account/s');
            $username   = input('post.username/s');
            if ($settleType != 1 && $settleType != 2 && $settleType != 3)
                return json(['status' => 0, 'msg' => '结算类型有误,请重新选择']);
            if (empty($account))
                return json(['status' => 0, 'msg' => '收款账号不能为空']);
            if (strlen($account) > 32)
                return json(['status' => 0, 'msg' => '收款账号长度不能超过32个字符']);
            if (empty($username))
                return json(['status' => 0, 'msg' => '真实姓名不能为空']);
            if (strlen($username) > 10)
                return json(['status' => 0, 'msg' => '用户名称不能超过10个字符串']);
            $mysql  = db();
            $result = $mysql->table('epay_user')->limit(1)->where('id', $uid)->update([
                'clearType' => $settleType,
                'username'  => $username,
                'account'   => $account
            ]);
            return json(['status' => $result, 'msg' => $result ? '保存信息成功' : '保存信息失败,请重新']);
        } else if ($type == 'connectInfo') {
            $email  = input('post.email/s');
            $qq     = input('post.qq/s');
            $domain = input('post.domain/s');
            if (empty($email) || empty($qq) || empty($domain))
                return json(['status' => 0, 'msg' => '参数不能为空']);
            if (strlen($domain) > 64)
                return json(['status' => 0, 'msg' => '域名长度不能超过64个字符']);
            if (strlen($qq) > 20)
                return json(['status' => 0, 'msg' => 'qq号码长度不能超过20个字符']);
            if (strlen($email) > 32)
                return json(['status' => 0, 'msg' => '邮箱账号长度不能超过32个字符串']);

            $mysql  = db();
            $result = $mysql->table('epay_user')->limit(1)->where('id', $uid)->update([
                'email'  => $email,
                'qq'     => $qq,
                'domain' => $domain
            ]);
            return json(['status' => $result, 'msg' => $result ? '保存信息成功' : '保存信息失败,请重新']);
        }
    }

    public function postSearchOrder()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);

        $searchType    = input('post.type/d', 'all');
        $searchContent = input('post.content/s');
        $page          = input('post.page/d', 1);

        if ($searchType > 5 || $searchType < 0)
            return json(['status' => 0, 'msg' => '搜索类型有误']);
        if ($searchType != 0 && empty($searchContent))
            return json(['status' => 0, 'msg' => '搜索内容不能为空']);
        $searchData = [
            ['uid', '=', $uid]
        ];
        switch ($searchType) {
            case 1:
                $searchData[] = ['tradeNo', '=', $searchContent];
                break;
            case 2:
                $searchData[] = ['tradeNoOut', '=', $searchContent];
                break;
            case 3:
                $searchData[] = ['productName', 'like', '%' . $searchContent . '%'];
                break;
            case 4:
                $searchData[] = ['money', '=', decimalsToInt($searchContent, 2)];
                break;
        }
        $totalRow = db()->table('epay_order')->where($searchData)->count('id');
        $result   = db()->table('epay_order')->where($searchData)->order('id desc')->page($page, 15)->field('tradeNo,tradeNoOut,productName,money,type,createTime,endTime,status')->select();
        foreach ($result as $key => $value) {
                $result[$key]['tradeNo'] = (string)$value['tradeNo'];
        }
        return json(['status' => 1, 'data' => $result, 'totalPage' => ceil($totalRow / 15)]);
    }

    public function postNotified()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);
        $tradeNo = input('post.tradeNo/s');
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '请求参数有误']);
        $result = db()->table('epay_order')->where([
            'tradeNo' => $tradeNo,
            'uid'     => $uid
        ])->field('status')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '订单不存在']);
        if (!$result[0]['status'])
            return json(['status' => 0, 'msg' => '订单尚未支付，无法重新通知']);
        $callbackUrl = buildCallBackUrl($tradeNo, 'notify');
        trace('[手动重新通知] uid=>' . $uid . ' tradeNo=>' . $tradeNo, 'info');
        return json(['status' => 1, 'url' => $callbackUrl]);
    }
}