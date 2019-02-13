<?php

namespace app\admin\controller;

use app\admin\model\SearchTable;
use think\Controller;
use think\Db;

class Index extends Controller
{
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
        $mysql = db();
        if ($templateName == 'Dashboard') {
            $data['totalOrder'] = $mysql->table('epay_order')->cache(60)->count('id');
            $data['totalUser']  = $mysql->table('epay_user')->cache(60)->count('id');
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
                $data['settleRecord'][$key]['money'] = db()->table('epay_settle')->whereBetweenTime('createTime', $value['createTime'])->cache(300)->sum('money');
            }
            $data['statistics'] = [
                'yesterday' => [
                    [
                        'type'       => 1,
                        'totalMoney' => db()->table('epay_order')->where([
                            'type'   => 1,
                            'status' => 1
                        ])->cache(600)->whereTime('endTime', 'yesterday')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => db()->table('epay_order')->where([
                            'type'   => 2,
                            'status' => 1
                        ])->cache(600)->whereTime('endTime', 'yesterday')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => db()->table('epay_order')->where([
                            'type'   => 3,
                            'status' => 1
                        ])->cache(600)->whereTime('endTime', 'yesterday')->sum('money')
                    ]
                ],
                'today'     => [
                    [
                        'type'       => 1,
                        'totalMoney' => db()->table('epay_order')->where([
                            'type'   => 1,
                            'status' => 1
                        ])->cache(300)->whereTime('endTime', 'today')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => db()->table('epay_order')->where([
                            'type'   => 2,
                            'status' => 1
                        ])->cache(300)->whereTime('endTime', 'today')->sum('money')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => db()->table('epay_order')->where([
                            'type'   => 3,
                            'status' => 1
                        ])->cache(300)->whereTime('endTime', 'today')->sum('money')
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
        $mysql     = db();
        $orderInfo = $mysql->table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->select();
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
                    'totalMoney' => \think\Db::table('epay_settle')->where([
                        'clearType' => 4
                    ])->whereBetweenTime('createTime', $time)->sum('money')
                ];
            }
        } else {
            $settleTimeList = \think\Db::table('epay_settle')->where('clearType', '<>', 4)->order('createTime desc')->limit(15)->group('createTime')->field('createTime')->select();
            $data           = [];
            foreach ($settleTimeList as $value) {
                $result = \think\Db::table('epay_settle')->where('clearType', '<>', 4)->where('createTime', $value['createTime'])->sum('money');
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
        if (empty($type) || empty($createTime))
            return json(['status' => 0, 'msg' => '请求参数不能为空']);

        if ($type == 'confirmSettle') {
            Db::table('epay_settle')->where([
                ['createTime', '=', $createTime],
                ['clearType', '<>', 4]
            ])->update([
                'status'     => 1,
                'updateTime' => getDateTime()
            ]);
            return json(['status' => 1, 'msg' => '批量更新结算状态成功']);
        } else if ($type == 'downloadSettle') {
            $head   = ['商户流水号', '收款方式', '收款账号', '收款人姓名', '付款金额（元）', '付款理由'];
            $body   = [];
            $result = Db::table('epay_settle')->field('clearType,account,username,money,addType')->where([
                ['createTime', '=', $createTime],
                ['clearType', '<>', 4]
            ])->select();
            foreach ($result as $key => $value) {
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
                $body[] = [$key, $clearName, $value['account'], $value['username'], $value['money'] / 100, $desc];
            }
            exportToExcel('pay_' . $createTime . '.csv', $head, $body);
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
        $result = db()->table('epay_settle')->where('id', $id)->limit(1)->select();
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
        $mysql  = db();
        $result = $mysql->table('epay_user')->where('id', $uid)->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '用户不存在,请重试']);
        $data            = $result[0];
        $config          = getConfig();
        $data['deposit'] = getPayUserAttr($uid, 'deposit');
        if ($data['deposit'] == '')
            $data['deposit'] = 0;
        $data['payMoneyMax'] = getPayUserAttr($uid, 'payMoneyMax');
        if ($data['payMoneyMax'] == '')
            $data['payMoneyMax'] = $config['defaultMaxPayMoney'] / 100;
        $data['payDayMoneyMax'] = getPayUserAttr($uid, 'payDayMoneyMax');
        if ($data['payDayMoneyMax'] == '')
            $data['payDayMoneyMax'] = 0;
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

        $result = \think\Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
            'status' => $status
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
        $result = db()->table('epay_settle')->where('id', $id)->field('status,clearType,money,uid')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '订单不存在']);
        if ($result[0]['status'])
            return json(['status' => 0, 'msg' => '订单已经结算过，无法继续结算']);
        if ($result[0]['clearType'] == 4)
            return json(['status' => 0, 'msg' => '订单结算类型有误']);
        if ($result[0]['money'] <= 0)
            return json(['status' => 0, 'msg' => '订单结算金额有误']);
        $result = db()->table('epay_settle')->where('id', $id)->update([
            'status'     => 1,
            'updateTime' => getDateTime()
        ]);
        return json(['status' => $result, 'msg' => $result ? '操作成功' : '操作失败']);
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
        $result = db()->table('epay_user')->where('id', $uid)->limit(1)->delete();
        return json(['status' => $result, 'msg' => '操作' . ($result ? '成功' : '失败')]);
    }

    public function postAddUser()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $balance        = input('post.balance/s', 0);
        $clearType      = input('post.clearType/s', 1);
        $deposit        = input('post.deposit/s', 0);
        $domain         = input('post.domain/s', '');
        $email          = input('post.email/s', '');
        $isBan          = input('post.isBan/d', 0);
        $isClear        = input('post.isClear/d', 0);
        $payDayMoneyMax = input('post.payDayMoneyMax/s', 0);
        $payMoneyMax    = input('post.payMoneyMax/s', 0);
        $qq             = input('post.qq/s', 0);
        $rate           = input('post.rate/s', 0);
        $username       = input('post.username/s', '');
        $account        = input('post.account/s', '');

        $rate = decimalsToInt($rate, 2);
        if ($rate > 10000)
            $rate = 10000;

        $result = db()->table('epay_user')->insertGetId([
            'key'        => getRandChar(32),
            'balance'    => decimalsToInt($balance, 3),
            'clearType'  => $clearType,
            'domain'     => $domain,
            'email'      => $email,
            'isBan'      => $isBan,
            'qq'         => $qq,
            'rate'       => $rate,
            'username'   => $username,
            'account'    => $account,
            'isClear'    => $isClear,
            'createTime' => getDateTime()
        ]);
        if (!$result)
            return json(['status' => 0, 'msg' => '新增用户失败,请重试']);
        setPayUserAttr($result, 'deposit', decimalsToInt($deposit, 2));
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
        $result = db()->table('epay_user')->where('id', $uid)->field('id')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '用户不存在']);
        $balance        = input('post.balance/s', 0);
        $clearType      = input('post.clearType/s', 1);
        $deposit        = input('post.deposit/s', 0);
        $domain         = input('post.domain/s', '');
        $email          = input('post.email/s', '');
        $isBan          = input('post.isBan/d', 0);
        $isClear        = input('post.isClear/d', 0);
        $payDayMoneyMax = input('post.payDayMoneyMax/s', 0);
        $payMoneyMax    = input('post.payMoneyMax/s', 0);
        $qq             = input('post.qq/s', 0);
        $rate           = input('post.rate/s', 0);
        $username       = input('post.username/s', '');
        $account        = input('post.account/s', '');

        $rate = decimalsToInt($rate, 2);
        if ($rate > 10000)
            $rate = 10000;

        db()->table('epay_user')->where('id', $uid)->limit(1)->update([
            'balance'   => decimalsToInt($balance, 3),
            'clearType' => $clearType,
            'domain'    => $domain,
            'email'     => $email,
            'isBan'     => $isBan,
            'qq'        => $qq,
            'rate'      => $rate,
            'username'  => $username,
            'account'   => $account,
            'isClear'   => $isClear
        ]);
        setPayUserAttr($uid, 'deposit', decimalsToInt($deposit, 2));
        setPayUserAttr($uid, 'payMoneyMax', decimalsToInt($payMoneyMax, 2));
        setPayUserAttr($uid, 'payDayMoneyMax', decimalsToInt($payDayMoneyMax, 2));

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
        $mysql  = db();
        $key    = getRandChar(32);
        $result = $mysql->table('epay_user')->where('id', $uid)->limit(1)->update([
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
        $mysql  = db();
        $result = $mysql->table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
            'isShield' => $status
        ]);
        return json(['status' => $result, 'msg' => '更新状态' . ($result ? '成功' : '失败')]);
    }

    public function postNotified()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $tradeNo = input('post.tradeNo/s');
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '请求参数有误']);
        $result = db()->table('epay_order')->where([
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

        $SearchTable = new SearchTable(db(), $searchTable, $startSite, $getLength, $order, $searchValue, $args);
        return json($SearchTable->getData());
    }
}
