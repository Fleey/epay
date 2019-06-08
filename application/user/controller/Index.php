<?php

namespace app\user\controller;

use think\Controller;
use think\Db;

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
        //是否允许用户申请结算
        if (empty($uid) && $templateName != 'Login')
            $this->redirect('/user/Login', [], 302);
        if ($templateName != 'Login') {
            $userInfo              = Db::table('epay_user')->where('id', $uid)->cache(60)->limit(1)->field('clearMode,key,balance,rate')->select();
            $data['isSettleApply'] = $userInfo[0]['clearMode'] == 1 ? true : false;
        } else {
            $data['isGeetest'] = !empty($config['geetestCaptchaID']) && !empty($config['geetestPrivateKey']);
        }
        if ($templateName == 'Dashboard') {
            $data['balance']         = $userInfo[0]['balance'] / 1000;
            $data['key']             = $userInfo[0]['key'];
            $data['uid']             = $uid;
            $data['rate']            = $userInfo[0]['rate'] / 100;
            $data['todayTotalOrder'] = Db::table('epay_order')->where('uid', $uid)->whereTime('createTime', 'today')->cache(120)->count('id');

            $data['todaySuccessOrder'] = Db::table('epay_order')->where([
                'uid'      => $uid,
                'status'   => 1,
                'isShield' => 0
            ])->whereTime('createTime', 'today')->cache(120)->count('id');

            $data['todayOrderTotalMoney'] = Db::table('epay_order')->whereTime('endTime', 'today')->where([
                'uid'      => $uid,
                'status'   => 1,
                'isShield' => 0
            ])->cache(120)->sum('money');

            $data['todayOrderTypeCount'] = [
                'wx'   => Db::table('epay_order')->whereTime('endTime', 'today')->cache(600)->where([
                    'uid'      => $uid,
                    'status'   => 1,
                    'type'     => 1,
                    'isShield' => 0
                ])->sum('money'),
                'qq'   => Db::table('epay_order')->whereTime('endTime', 'today')->cache(600)->where([
                    'uid'      => $uid,
                    'status'   => 1,
                    'type'     => 2,
                    'isShield' => 0
                ])->sum('money'),
                'ali'  => Db::table('epay_order')->whereTime('endTime', 'today')->cache(600)->where([
                    'uid'      => $uid,
                    'status'   => 1,
                    'type'     => 3,
                    'isShield' => 0
                ])->sum('money'),
                'bank' => Db::table('epay_order')->whereTime('endTime', 'today')->cache(600)->where([
                    'uid'      => $uid,
                    'status'   => 1,
                    'type'     => 4,
                    'isShield' => 0
                ])->sum('money')
            ];

            $data['settleRecord'] = [];
            for ($i = 6; $i >= 1; $i--) {
                $data['settleRecord'][] = ['createTime' => date('Y-m-d', strtotime('-' . $i . ' day'))];
            }
            $data['settleRecord'][] = ['createTime' => date('Y-m-d', strtotime('now'))];
            foreach ($data['settleRecord'] as $key => $value) {
                $data['settleRecord'][$key]['money'] = Db::table('epay_settle')->where('uid', $uid)->whereBetweenTime('createTime', $value['createTime'])->cache(300)->sum('money');
            }

        } else if ($templateName == 'SettleApply') {
            if (!$data['isSettleApply'])
                return '<h3 class="text-center" style="margin-top: 12rem;">管理员暂未允许您手动提交结算申请，请联系管理员处理</h3>';
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
        $result   = Db::table('epay_settle')->field('id,account,money,fee,status,createTime,remark')->order('id desc')->where('uid', $uid)->page($page, 15)->select();
        $totalRow = Db::table('epay_settle')->where('uid', $uid)->count('id');
        return json(['status' => 1, 'data' => $result, 'totalPage' => ceil($totalRow / 15)]);
    }

    public function getInfo()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);
        $result = Db::table('epay_user')->field('id,key,account,balance,email,qq,domain,clearType,clearMode,username')->where('id', $uid)->select();
        $data   = $result[0];

        $userSettleConfig = getPayUserAttr($uid, 'settleConfig');
        if (empty($userSettleConfig))
            $userSettleConfig = [];
        else
            $userSettleConfig = unserialize($userSettleConfig);
        $settleFee = 0;
        if (!empty($userSettleConfig['settleFee']))
            $settleFee = $userSettleConfig['settleFee'] / 10;

        $data['settleFee'] = $settleFee;

        $data['frozenBalance'] = getPayUserAttr($uid, 'frozenBalance');
        if ($data['frozenBalance'] == '')
            $data['frozenBalance'] = 0;

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
            return json(['status' => 0, 'msg' => '修改结算信息,请联系管理员修改']);
            $settleType = input('post.settleType/d');
            $account    = input('post.account/s');
            $username   = input('post.username/s');
            $userInfo   = Db::table('epay_user')->limit(1)->field('clearMode')->where('id', $uid)->select();
            if (empty($userInfo))
                return json(['status' => 0, 'msg' => '系统发生异常,请联系管理员处理']);
            if ($userInfo[0]['clearMode'] == 2) {
                if ($settleType != 4)
                    return json(['status' => 0, 'msg' => '非法参数']);
                //限制自动结算类型
            } else if ($userInfo[0]['clearMode'] == 1 || $userInfo[0]['clearMode'] == 0) {
                if ($settleType != 1 && $settleType != 2 && $settleType != 3)
                    return json(['status' => 0, 'msg' => '非法参数']);
                //限制手动结算类型
            }
            if (empty($account))
                return json(['status' => 0, 'msg' => '收款账号不能为空']);
            if (strlen($account) > 100)
                return json(['status' => 0, 'msg' => '收款账号长度不能超过100个字符']);
            if (empty($username))
                return json(['status' => 0, 'msg' => '真实姓名不能为空']);
            if (strlen($username) > 100)
                return json(['status' => 0, 'msg' => '用户名称不能超过100个字符串']);
            $result = Db::table('epay_user')->limit(1)->where('id', $uid)->update([
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

            $result = Db::table('epay_user')->limit(1)->where('id', $uid)->update([
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
            ['a.uid', '=', $uid]
        ];
        switch ($searchType) {
            case 1:
                $searchData[] = ['a.tradeNo', '=', $searchContent];
                break;
            case 2:
                $searchData[] = ['a.tradeNoOut', '=', $searchContent];
                break;
            case 3:
                $searchData[] = ['a.productName', 'like', '%' . $searchContent . '%'];
                break;
            case 4:
                if (strpos($searchContent, '->') !== false) {
                    $searchContent = explode('->', $searchContent);
                    if (count($searchContent) != 2)
                        break;
                    $searchData[] = ['a.money', '>=', decimalsToInt($searchContent[0], 2)];
                    $searchData[] = ['a.money', '<=', decimalsToInt($searchContent[1], 2)];
                } else {
                    $searchData[] = ['a.money', '=', decimalsToInt($searchContent, 2)];
                    break;
                }
                break;
            case 5:
                if (strpos($searchContent, '->') !== false) {
                    $searchContent = explode('->', $searchContent);
                    if (count($searchContent) != 2)
                        break;
                    $searchData[] = ['a.createTime', '>=', $searchContent[0]];
                    $searchData[] = ['a.createTime', '<=', $searchContent[1]];
                }
                break;
        }
        $totalRow = Db::table('epay_order')->alias('a')->where($searchData)->count('id');
        $result   = Db::table('epay_order')->alias('a')->leftJoin('epay_order_attr b', 'b.attrKey = "discountMoney" and b.tradeNo = a.tradeNo')
            ->field('a.tradeNo,a.tradeNoOut,a.productName,a.money,a.type,a.createTime,a.endTime,a.status,b.attrValue as `discountMoney`')
            ->where($searchData)->order('a.id desc')->page($page, 15)->select();
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
        $result = Db::table('epay_order')->where([
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

    public function postSettleApply()
    {
        $uid = session('uid', '', 'user');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '需要登陆后才能继续操作']);
        $money = input('post.money/s');
        if (empty($money))
            return json(['status' => 0, 'msg' => '申请结算金额不能为零']);
        $settleRecord = Db::table('epay_settle')->where([
            'uid'    => $uid,
            'status' => 0
        ])->field('id')->limit(1)->select();
        if (!empty($settleRecord))
            return json(['status' => 0, 'msg' => '上一次结算管理员尚未处理，请联系管理员处理']);
        $money    = decimalsToInt($money, 2);
        $userInfo = Db::table('epay_user')->where('id', $uid)->field('balance,clearType,clearMode,username,account')->limit(1)->select();
        if (empty($userInfo))
            return json(['status' => 0, 'msg' => '数据异常,请联系管理员处理']);

        $userSettleConfig = getPayUserAttr($uid, 'settleConfig');
        if (empty($userSettleConfig))
            $userSettleConfig = [];
        else
            $userSettleConfig = unserialize($userSettleConfig);
        $settleFee = 0;
        if (!empty($userSettleConfig['settleFee']))
            $settleFee = $userSettleConfig['settleFee'] / 10;

        if ($userInfo[0]['balance'] < ($money * 10 + $settleFee))
            return json(['status' => 0, 'msg' => '您的余额不足,不能够结算这么多']);
        if ($userInfo[0]['clearMode'] != 1)
            return json(['status' => 0, 'msg' => '您当前账号结算方式，不支持手动提交结算申请']);

        $result = Db::table('epay_settle')->insertGetId([
            'uid'        => $uid,
            'clearType'  => $userInfo[0]['clearType'],
            'addType'    => 3,
            'account'    => $userInfo[0]['account'],
            'username'   => $userInfo[0]['username'],
            'money'      => $money + $settleFee,
            'fee'        => $settleFee,
            'status'     => 0,
            'createTime' => getDateTime()
        ]);
        return json(['status' => $result ? 1 : 0, 'msg' => '提交结算申请' . ($result ? '成功' : '失败')]);
    }
}