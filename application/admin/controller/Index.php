<?php

namespace app\admin\controller;

use app\admin\model\SearchTable;
use think\Controller;
use think\Db;
use ZipArchive;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('/IndexTemplate');
    }

    public function loadTemplate($templateName = 'Login')
    {
        $config = getConfig();
        if (!file_exists(env('APP_PATH') . 'template/Admin/' . $templateName . '.php') ||
            ($templateName == 'Head' || $templateName == 'Footer' || $templateName == 'Sidebar'))
            return abort('404', '页面未找到');
        $data     = [
            'webName' => $config['webName']
        ];
        $username = session('username', '', 'admin');
        if (empty($username) && $templateName != 'Login')
            $this->redirect('/admin/Login', [], 302);
        else
            $data['isGeetest'] = !empty($config['geetestCaptchaID']) && !empty($config['geetestPrivateKey']);
        if ($templateName == 'Dashboard') {
            $data['totalOrder'] = Db::table('epay_order')->cache(60)->count('id');
            $data['totalUser']  = Db::table('epay_user')->cache(60)->count('id');
            $data['totalMoney'] = getServerConfig('totalMoney');
            if (empty($data['totalMoney']))
                $data['totalMoney'] = 0;
            $data['totalMoneyRate'] = getServerConfig('totalMoneyRate');
            if (empty($data['totalMoneyRate']))
                $data['totalMoneyRate'] = 0;
            $data['settleRecord'] = [];
            for ($i = 6; $i >= 1; $i--) {
                $data['settleRecord'][] = ['createTime' => date('Y-m-d', strtotime('-' . $i . ' day'))];
            }
            $data['settleRecord'][] = ['createTime' => date('Y-m-d', strtotime('now'))];
            foreach ($data['settleRecord'] as $key => $value) {
                $data['settleRecord'][$key]['money'] = Db::table('epay_settle')->whereBetweenTime('createTime', $value['createTime'])->sum('money');
            }
            $data['statistics'] = [
                'yesterday' => [
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_order')->where([
                            'type'   => 1,
                            'status' => 1
                        ])->whereTime('endTime', 'yesterday')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_order')->where([
                            'type'   => 2,
                            'status' => 1
                        ])->whereTime('endTime', 'yesterday')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_order')->where([
                            'type'   => 3,
                            'status' => 1
                        ])->whereTime('endTime', 'yesterday')->sum('money')
                    ]
                ],
                'today'     => [
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_order')->where([
                            'type'   => 1,
                            'status' => 1
                        ])->whereTime('endTime', 'today')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_order')->where([
                            'type'   => 2,
                            'status' => 1
                        ])->whereTime('endTime', 'today')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_order')->where([
                            'type'   => 3,
                            'status' => 1
                        ])->whereTime('endTime', 'today')->sum('money')
                    ]
                ]
            ];
        }
        return $this->fetch('/Admin/' . $templateName, $data);
    }

    public function getOrderInfo()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $tradeNo = input('get.tradeNo/s');
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '平台订单ID不能为空']);
        $orderInfo = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->select();
        if (empty($orderInfo))
            return json(['status' => 0, 'msg' => '平台订单ID不存在']);
        $orderInfo[0]['tradeNo'] = (string)$orderInfo[0]['tradeNo'];
        return json(['status' => 1, 'data' => $orderInfo[0]]);
    }

    public function getSettleRecord()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $type = input('get.type/s');
        if ($type == 'auto') {
            $data = [];
            for ($i = 0; $i <= 7; $i++) {
                $time   = date('Y-m-d', strtotime('-' . $i . ' day'));
                $data[] = [
                    'createTime' => $time,
                    'totalMoney' => Db::table('epay_settle')->where([
                        'clearType' => 4
                    ])->whereBetweenTime('createTime', $time)->sum('money')
                ];
            }
        } else {
            $settleTimeList = Db::table('epay_settle')->where('addType', 1)->order('createTime desc')->limit(15)->group('createTime')->field('createTime')->select();
            $data           = [];
            foreach ($settleTimeList as $value) {
                $result = Db::table('epay_settle')->where('addType', 1)->where('createTime', $value['createTime'])->sum('money');
                if (!empty($result))
                    $data[] = [
                        'createTime' => $value['createTime'],
                        'totalMoney' => $result
                    ];
            }
            if (empty($data))
                return json(['status' => 0, 'msg' => '暂无更多记录']);
        }
        return json(['status' => 1, 'data' => $data]);
    }

    public function getSettleOperate()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $type       = input('get.type/s');
        $createTime = input('get.createTime/s');
        if (empty($type))
            return json(['status' => 0, 'msg' => '请求参数不能为空']);

        if ($type == 'confirmSettle') {
            $settleList = Db::table('epay_settle')->where([
                'createTime' => $createTime,
                'addType'    => 1
            ])->field('id,uid')->cursor();
            foreach ($settleList as $value) {
                $this->confirmSettle($value['id'], $value['uid']);
            }
            return json(['status' => 1, 'msg' => '批量更新结算状态成功']);
        } else if ($type == 'downloadSettle') {
            $head   = ['商户ID', '收款方式', '收款账号', '收款人姓名', '付款金额（元）', '付款理由'];
            $body   = [];
            $result = Db::table('epay_settle')->field('uid,clearType,account,username,money,addType')->where([
                ['createTime', '=', $createTime],
                ['addType', '=', 1]
            ])->select();
            foreach ($result as $value) {
                $clearName = '';
                switch ($value['clearType']) {
                    case 1:
                        $clearName = '银行转账（手动）';
                        break;
                    case 2:
                        $clearName = '微信转账（手动）';
                        break;
                    case 3:
                        $clearName = '支付宝转账（手动）';
                        break;
                }
                $desc = '';
                switch ($value['addType']) {
                    case 1:
                        $desc = '系统零时自动结账';
                        break;
                }
                $body[] = [$value['uid'], $clearName, $value['account'], $value['username'], $value['money'] / 100, $desc];
            }
            exportToExcel('pay_' . $createTime . '.csv', $head, $body);
        } else if ($type == 'downloadSettleAuto') {
            $head          = ['商户ID', '收款方式', '收款账号', '收款人姓名', '付款金额（元）', '付款理由'];
            $body          = [];
            $autoClearList = Db::table('epay_user')->where([
                'clearType' => 4
            ])->field('id,clearType,account,username')->cursor();
            foreach ($autoClearList as $key => $value) {
                $settleMoney = Db::table('epay_settle')->where([
                    'uid'       => $value['id'],
                    'clearType' => 4
                ])->whereTime('createTime', $createTime)->sum('money');
                $body[]      = [$value['id'], '支付宝转账（自动）', $value['account'], $value['username'], $settleMoney / 100, '支付宝自动转账'];
            }
            exportToExcel('pay_' . $createTime . '.csv', $head, $body);
        } else if ($type == 'userSettleInfo') {
            $uid = input('get.uid/d');
            if (empty($uid))
                return json(['status' => 0, 'msg' => 'uid is empty']);
            $data = [];
            for ($i = 6; $i >= 1; $i--) {
                $data[] = ['createTime' => date('Y-m-d', strtotime('-' . $i . ' day'))];
            }
            $data[] = ['createTime' => date('Y-m-d', strtotime('now'))];
            foreach ($data as $key => $value) {
                $data[$key]['money'] = Db::table('epay_settle')->where(['uid' => $uid])->whereBetweenTime('createTime', $value['createTime'])->sum('money');
            }
            return json(['status' => 1, 'data' => $data]);
        }
    }

    public function getSettleInfo()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $id = input('get.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '参数不能为空']);
        $result = Db::table('epay_settle')->where('id', $id)->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '结算记录不存在']);
        return json(['status' => 1, 'data' => $result[0]]);
    }

    public function getUserInfo()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $uid = input('get.uid/d');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '用户uid不能为空']);
        $result = Db::table('epay_user')->where('id', $uid)->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '用户不存在,请重试']);
        $data            = $result[0];
        $config          = getConfig();
        $data['deposit'] = getPayUserAttr($uid, 'deposit');
        if ($data['deposit'] == '')
            $data['deposit'] = 0;
        $data['settleMoney'] = getPayUserAttr($uid, 'settleMoney');
        if ($data['settleMoney'] == '')
            $data['settleMoney'] = 0;
        $data['payMoneyMax'] = getPayUserAttr($uid, 'payMoneyMax');
        if ($data['payMoneyMax'] == '')
            $data['payMoneyMax'] = $config['defaultMaxPayMoney'] / 100;
        $data['payDayMoneyMax'] = getPayUserAttr($uid, 'payDayMoneyMax');
        if ($data['payDayMoneyMax'] == '')
            $data['payDayMoneyMax'] = 0;
        $data['isSettleApply'] = getPayUserAttr($uid, 'isSettleApply');
        if ($data['isSettleApply'] == '')
            $data['isSettleApply'] = 0;
        return json(['status' => 1, 'data' => $data]);
    }

    public function getConfig()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $keyName = input('get.keyName/s');
        if (empty($keyName))
            return json(['status' => 0, 'msg' => '键名不存在']);
        $config = getConfig();
        if (empty($config[$keyName]))
            $config[$keyName] = '';
        return json(['status' => 1, 'data' => $config[$keyName]]);
    }

    public function getUpdateProgram()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $result = curl('http://update.moxi666.cn/version.json');
        if ($result === false)
            return json(['status' => 0, 'msg' => '更新服务器异常，请联系管理员处理']);
        $result = json_decode($result, true);
        if (empty($result['version']) || empty($result['downloadPath']))
            return json(['status' => 0, 'msg' => '更新服务器异常，请联系管理员处理']);
        $downloadPath  = $result['downloadPath'];
        $latestVersion = $result['version'];
        if (config('app_version') == $latestVersion)
            return json(['status' => 0, 'msg' => '程序版本已经是最新，无法继续升级']);
        if (getServerConfig('isUpdateLoading') == 'yes')
            return json(['status' => 0, 'msg' => '正在更新程序，无法多次运行']);
        setServerConfig('isUpdateLoading', 'yes');
        set_time_limit(0);
        $result = file_put_contents('../epay-' . $latestVersion . '.zip', file_get_contents($downloadPath));
        if ($result) {
            $zip    = new ZipArchive;
            $result = $zip->open('../epay-' . $latestVersion . '.zip');
            if ($result === true) {
                $zip->extractTo('../');
                $zip->close();
            }
        }
        setServerConfig('isUpdateLoading', 'no');
        return json(['status' => 1, 'msg' => '更新系统成功,稍后将为您刷新页面']);
    }

    public function postOrderStatus()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $tradeNo = input('post.tradeNo/s');
        $status  = input('post.status/d');
        if ($status != 0 && $status != 1)
            return json(['status' => 0, 'msg' => '更改订单状态有误']);
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '订单号码不能为空']);

        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
            'status'  => $status,
            'endTime' => getDateTime()
        ]);
        return json(['status' => $result, 'msg' => '更新订单状态' . ($result ? '成功' : '失败')]);
    }

    public function postConfig()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $keyName = input('post.keyName/s');
        $isArray = input('post.isArray/b', false);
        $value   = input('post.data/' . ($isArray ? 'a' : 's'));
        if (empty($keyName) || empty($value))
            return json(['status' => 0, 'msg' => '请求数据有误']);
        if ($isArray) {
            foreach ($value as $key => $value1) {
                if (strpos($key, 'is') === 0)
                    $value[$key] = $value1 === 'true';
            }
        } else {
            if ($keyName == 'defaultMaxPayMoney' || $keyName == 'defaultMoneyRate')
                $value = decimalsToInt($value, 2);
        }
        $config           = getConfig();
        $config[$keyName] = $value;
        $result           = putConfig($config);
        if (!$result)
            return json(['status' => 0, 'msg' => '保存配置文件异常,请检查文件权限']);
        return json(['status' => 1, 'msg' => '保存配置文件成功']);
    }

    public function postConfirmSettle()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $id = input('post.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '参数不能为空']);
        $result = Db::table('epay_settle')->where('id', $id)->field('status,clearType,addType,money,uid')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '订单不存在']);
        if ($result[0]['status'])
            return json(['status' => 0, 'msg' => '订单已经结算过，无法继续结算']);
        if ($result[0]['clearType'] == 4)
            return json(['status' => 0, 'msg' => '订单结算类型有误']);
        if ($result[0]['money'] <= 0)
            return json(['status' => 0, 'msg' => '订单结算金额有误']);

        $result = $this->confirmSettle($id, $result[0]['uid']);
        return json(['status' => $result ? 1 : 0, 'msg' => $result ? '操作成功' : '操作失败']);
    }

    public function postSetAdmin()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $username = input('post.username/s');
        $password = input('post.password/s');
        if (empty($username) || empty($password))
            return json(['status' => 0, 'msg' => '参数不能为空']);
        $salt     = getRandChar(6);
        $saveData = [
            'salt'     => $salt,
            'username' => $username,
            'password' => hash('sha256', hash('sha256', $password) . $salt)
        ];
        $result   = setServerConfig('adminAccount', serialize($saveData));
        return json(['status' => $result, 'msg' => '请求' . ($result ? '成功' : '失败')]);
    }

    public function postDeleteUser()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $uid = input('post.uid/d');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '删除用户信息失败']);
        $result = Db::table('epay_user')->where('id', $uid)->limit(1)->delete();
        if ($result) {
            Db::table('epay_settle')->where('uid', $uid)->delete();
            Db::table('epay_order')->where('uid', $uid)->delete();
        }
        return json(['status' => $result, 'msg' => '操作' . ($result ? '成功' : '失败')]);
    }

    public function postAddUser()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $balance        = input('post.balance/s', 0);
        $clearType      = input('post.clearType/s', 1);
        $clearMode      = input('post.clearMode/d', 0);
        $deposit        = input('post.deposit/s', 0);
        $settleMoney    = input('post.settleMoney/s', 0);
        $domain         = input('post.domain/s', '');
        $email          = input('post.email/s', '');
        $isBan          = input('post.isBan/d', 0);
        $payDayMoneyMax = input('post.payDayMoneyMax/s', 0);
        $payMoneyMax    = input('post.payMoneyMax/s', 0);
        $qq             = input('post.qq/s', 0);
        $rate           = input('post.rate/s', 0);
        $username       = input('post.username/s', '');
        $account        = input('post.account/s', '');

        $rate = decimalsToInt($rate, 2);
        if ($rate > 10000)
            $rate = 10000;

        $result = Db::table('epay_user')->insertGetId([
            'key'        => getRandChar(32),
            'balance'    => decimalsToInt($balance, 3),
            'clearMode'  => $clearMode,
            'clearType'  => $clearType,
            'domain'     => $domain,
            'email'      => $email,
            'isBan'      => $isBan,
            'qq'         => $qq,
            'rate'       => $rate,
            'username'   => $username,
            'account'    => $account,
            'createTime' => getDateTime()
        ]);
        if (!$result)
            return json(['status' => 0, 'msg' => '新增用户失败,请重试']);
        setPayUserAttr($result, 'deposit', decimalsToInt($deposit, 2));
        setPayUserAttr($result, 'settleMoney', decimalsToInt($settleMoney, 2));
        setPayUserAttr($result, 'payMoneyMax', decimalsToInt($payMoneyMax, 2));
        setPayUserAttr($result, 'payDayMoneyMax', decimalsToInt($payDayMoneyMax, 2));
        return json(['status' => 1, 'msg' => '新增用户成功']);
    }

    public function postUserInfo()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $uid = input('post.uid/d');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '保存用户信息失败']);
        $result = Db::table('epay_user')->where('id', $uid)->field('id')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '用户不存在']);
        $balance        = input('post.balance/s', 0);
        $clearType      = input('post.clearType/s', 1);
        $clearMode      = input('post.clearMode/d', 0);
        $deposit        = input('post.deposit/s', 0);
        $settleMoney    = input('post.settleMoney/s', 0);
        $domain         = input('post.domain/s', '');
        $email          = input('post.email/s', '');
        $isBan          = input('post.isBan/d', 0);
        $payDayMoneyMax = input('post.payDayMoneyMax/s', 0);
        $payMoneyMax    = input('post.payMoneyMax/s', 0);
        $qq             = input('post.qq/s', 0);
        $rate           = input('post.rate/s', 0);
        $username       = input('post.username/s', '');
        $account        = input('post.account/s', '');
        $isSettleApply  = input('post.isSettleApply/s', '');

        $rate = decimalsToInt($rate, 2);
        if ($rate > 10000)
            $rate = 10000;

        Db::table('epay_user')->where('id', $uid)->limit(1)->update([
            'balance'   => decimalsToInt($balance, 3),
            'clearType' => $clearType,
            'clearMode' => $clearMode,
            'domain'    => $domain,
            'email'     => $email,
            'isBan'     => $isBan,
            'qq'        => $qq,
            'rate'      => $rate,
            'username'  => $username,
            'account'   => $account
        ]);
        setPayUserAttr($uid, 'deposit', decimalsToInt($deposit, 2));
        setPayUserAttr($uid, 'settleMoney', decimalsToInt($settleMoney, 2));
        setPayUserAttr($uid, 'payMoneyMax', decimalsToInt($payMoneyMax, 2));
        setPayUserAttr($uid, 'payDayMoneyMax', decimalsToInt($payDayMoneyMax, 2));
        setPayUserAttr($uid, 'isSettleApply', $isSettleApply);
        return json(['status' => 1, 'msg' => '保存用户信息成功']);
    }

    public function postReloadKey()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $uid = input('post.uid/d');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '用户id无效']);
        $key    = getRandChar(32);
        $result = Db::table('epay_user')->where('id', $uid)->limit(1)->update([
            'key' => $key
        ]);
        if (!$result)
            return json(['status' => 0, 'msg' => '刷新密匙失败']);
        return json(['status' => 1, 'msg' => '刷新密匙成功', 'key' => $key]);
    }

    public function postSetShield()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $tradeNo = input('post.tradeNo/s');
        $status  = input('post.status/d', 0);
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '订单号码无效']);
        if ($status != 0 && $status != 1)
            return json(['status' => 0, 'msg' => '请求状态有误']);
        $orderInfo = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->field('money,uid')->select();
        if (empty($orderInfo))
            return json(['status' => 0, 'msg' => '订单不存在无法更改屏蔽状态']);
        $userInfo = Db::table('epay_user')->where('id', $orderInfo[0]['uid'])->field('rate')->limit(1)->select();

        $rate         = $userInfo[0]['rate'] / 100;
        $addMoneyRate = $orderInfo[0]['money'] * ($rate / 100);
        //计算费率

        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
            'isShield' => $status
        ]);
        if ($result) {
            if ($status)
                Db::table('epay_user')->limit(1)->where('id', $orderInfo[0]['uid'])->inc('balance', $addMoneyRate * 10)->update();
            else
                Db::table('epay_user')->limit(1)->where('id', $orderInfo[0]['uid'])->dec('balance', $addMoneyRate * 10)->update();
        }
        //订单状态更新成功才操作这个
        return json(['status' => $result, 'msg' => '更新状态' . ($result ? '成功' : '失败')]);
    }

    public function postDeleteSettleRecord()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $settleID = input('post.id/d');
        if (empty($settleID))
            return json(['status' => 0, 'msg' => '结算号码不能为空']);

        $result = Db::table('epay_settle')->where('id', $settleID)->limit(1)->delete();
        return json(['status' => $result ? 1 : 0, 'msg' => '删除结算申请' . ($result ? '成功' : '失败')]);
    }

    public function postNotified()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $tradeNo = input('post.tradeNo/s');
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '请求参数有误']);
        $result = Db::table('epay_order')->where([
            'tradeNo' => $tradeNo
        ])->field('status')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '订单不存在']);
        if (!$result[0]['status'])
            return json(['status' => 0, 'msg' => '订单尚未支付，无法重新通知']);
        $callbackUrl = buildCallBackUrl($tradeNo, 'notify');
        trace('[手动重新通知] 管理员操作 tradeNo=>' . $tradeNo, 'info');
        return json(['status' => 1, 'url' => $callbackUrl]);
    }


    /**
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function postSearchTable()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $searchTable = input('post.searchTable/s');
        //直接查询表
        $startSite = input('post.start/i');
        //开始搜索行位置
        $getLength = input('post.length/i');
        //获取行数量
        $order = input('post.order/a');

        $searchValue = input('post.search/a');

        $args = input('post.args/a');

        $SearchTable = new SearchTable($searchTable, $startSite, $getLength, $order, $searchValue, $args);
        return json($SearchTable->getData());
    }

    private function confirmSettle($settleID, $uid)
    {
        $result = Db::table('epay_settle')->where('id', $settleID)->field('status,clearType,addType,money,uid')->limit(1)->select();
        if (empty($result))
            return false;
        $userInfo = Db::table('epay_user')->where('id', $uid)->field('balance,clearMode')->limit(1)->select();
        if ($userInfo[0]['clearMode'] == 1) {
            $updateUserResult = Db::table('epay_user')->where('id', $result[0]['uid'])->limit(1)->dec('balance', $result[0]['money'] * 10)->update();
            if (!$updateUserResult)
                return false;
        }
        $result = Db::table('epay_settle')->where('id', $settleID)->update([
            'status'     => 1,
            'updateTime' => getDateTime()
        ]);
        return $result != 0;
    }
}
