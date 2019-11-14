<?php

namespace app\admin\controller;

use app\admin\model\FileModel;
use app\admin\model\SearchTable;
use app\pay\controller\WxPay;
use app\pay\model\CenterPayModel;
use app\pay\model\PayModel;
use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
use think\Controller;
use think\Db;
use ZipArchive;

class Index extends Controller
{
    public function index()
    {
        $config = getConfig();
        return $this->fetch('/IndexTemplate', [
            'webName' => $config['webName'],
            'webQQ'   => $config['webQQ']
        ]);
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
            $this->redirect('/cy2018/Login', [], 302);
        else
            $data['isGeetest'] = !empty($config['geetestCaptchaID']) && !empty($config['geetestPrivateKey']);
        if ($templateName == 'Dashboard') {
            $data = $this->getDashboardData();
        }
        return $this->fetch('/Admin/' . $templateName, $data);
    }

    public function getCloseAll()
    {
        $config = getConfig();
        foreach ($config as $key => $value) {
            if ($key == 'wxpay')
                $config[$key]['isOpen'] = false;
        }
        putConfig($config);
        return json(['status' => 0, 'msg' => 'close all success']);
    }

    public function getDeleteRecord()
    {
        $time = input('get.time/d', 0);
        if ($time <= 1) {
            echo '不能够删除小于或等于一日的数据';
            exit();
        }
        $deleteTime = '- ' . $time . ' day';
        //delete 15 day before data
        Db::table('epay_order')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_order_attr')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_settle')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_log')->whereTime('createTime', '<=', $deleteTime)->delete();

        echo '删除记录成功';
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

        $discountMoney                 = PayModel::getOrderAttr($tradeNo, 'discountMoney');
        $orderInfo[0]['discountMoney'] = empty($discountMoney) ? 0 : $discountMoney;
        $orderInfo[0]['discountMoney'] /= 100;

        $tradePayConfig             = PayModel::getOrderAttr($tradeNo, 'payConfig');
        $tradePayConfig             = json_decode($tradePayConfig, true);
        $orderInfo[0]['sub_mch_id'] = $tradePayConfig['subMchID'];
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
            $data = [];
            for ($i = 0; $i <= 7; $i++) {
                $time   = date('Y-m-d', strtotime('-' . $i . ' day'));
                $data[] = [
                    'createTime' => $time,
                    'totalMoney' => Db::table('epay_settle')->whereIn('addType', [1, 3])->whereBetweenTime('createTime', $time)->sum('money')
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
            ])->field('id')->cursor();
            foreach ($settleList as $value) {
                $this->confirmSettle($value['id']);
            }
            return json(['status' => 1, 'msg' => '批量更新结算状态成功']);
        } else if ($type == 'downloadSettle') {
            $head   = ['商户ID', '收款方式', '收款账号', '收款人姓名', '付款金额（元）', '付款理由'];
            $body   = [];
            $result = Db::table('epay_settle')->field('uid,clearType,account,username,money,addType')->whereIn('addType', [1, 3])->whereBetweenTime('createTime', $createTime)->cursor();
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
                        $desc = '系统自动结账';
                        break;
                    case 2:
                        $desc = '支付宝自动结账';
                        break;
                    case 3:
                        $desc = '用户手动提交结账';
                        break;
                }
                $body[] = [$value['uid'], $clearName, $value['account'], $value['username'], $value['money'] / 100, $desc];
            }
            exportToExcel('pay_' . $createTime . '.csv', $head, $body);
            return;
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
            return;
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
                $data[$key]['fee']   = Db::table('epay_settle')->where(['uid' => $uid])->whereBetweenTime('createTime', $value['createTime'])->sum('fee');
            }
            return json(['status' => 1, 'data' => $data]);
        }
        return json(['status' => 0, 'data' => '你搞啥呢小老弟']);
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
        if ($result[0]['clearType'] == 5 || $result[0]['clearType'] == 6)
            $result[0]['settleQrFileID'] = getPayUserAttr($result[0]['uid'], 'qrFileID');

        $result[0]['balanceData'] = [];
        for ($i = 1; $i <= 7; $i++) {
            $time  = date('Y-m-d', strtotime('-' . $i . ' day'));
            $money = Db::table('epay_user_data_model')->where([
                'uid'      => $result[0]['uid'],
                'attrName' => 'moneyRecord'
            ])->whereBetweenTime('createTime', $time)->limit(1)->field('data')->select();
            if (empty($money))
                $result[0]['balanceData'][] = ['money' => 0, 'time' => $time];
            else
                $result[0]['balanceData'][] = ['money' => $money[0]['data'] / 1000, 'time' => $time];
        }
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
        $data['productNameShowMode'] = intval(getPayUserAttr($uid, 'productNameShowMode'));
        if ($data['productNameShowMode'] == 1)
            $data['productName'] = getPayUserAttr($uid, 'productName');
        else
            $data['productName'] = '';

        $payRate = getPayUserAttr($uid, 'payRate');
        if (empty($payRate)) {
            $data['rateWx']     = $data['rate'] / 100;
            $data['rateQQ']     = $data['rate'] / 100;
            $data['rateAlipay'] = $data['rate'] / 100;
        } else {
            $payRate            = unserialize($payRate);
            $data['rateWx']     = $payRate['rateWx'] / 100;
            $data['rateQQ']     = $payRate['rateQQ'] / 100;
            $data['rateAlipay'] = $payRate['rateAlipay'] / 100;
        }

        {
            $settleConfig = getPayUserAttr($uid, 'settleConfig');
            if ($settleConfig != '')
                $settleConfig = unserialize($settleConfig);
            else
                $settleConfig = [];

            if (isset($settleConfig['settleFee']))
                $data['settleFee'] = $settleConfig['settleFee'] / 1000;
            else
                $data['settleFee'] = 0;

            if (isset($settleConfig['settleHour']))
                $data['settleHour'] = $settleConfig['settleHour'];
            else
                $data['settleHour'] = 0;
        }
        //结算配置加载

        $data['balance'] = intval($data['balance']);

        $data['orderDiscounts'] = getPayUserAttr($uid, 'orderDiscounts');
        if ($data['orderDiscounts'] != '')
            $data['orderDiscounts'] = unserialize($data['orderDiscounts']);
        $data['payConfig'] = getPayUserAttr($uid, 'payConfig');
        if ($data['payConfig'] != '')
            $data['payConfig'] = unserialize($data['payConfig']);
        $data['isCancelReturn'] = getPayUserAttr($uid, 'isCancelReturn');
        if ($data['isCancelReturn'] == '')
            $data['isCancelReturn'] = 'false';
        $data['frozenBalance'] = getPayUserAttr($uid, 'frozenBalance');
        if ($data['frozenBalance'] == '')
            $data['frozenBalance'] = '0';
        if ($data['clearType'] == 5 || $data['clearType'] == 6)
            $data['qrFileID'] = getPayUserAttr($uid, 'qrFileID');
        //获取二维码
        $data['aliSellerEmail'] = getPayUserAttr($uid, 'aliSellerEmail');
        //获取支付即使转账账户
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

        $config = $config[$keyName];
        if (isset($config['certPublic'])) {
            $config['certPublic'] = @file_get_contents(FileModel::getFilePath($config['certPublic'], false));
            if ($config['certPublic'] === false)
                $config['certPublic'] = '读取证书文件失败，请重新上传';
        }
        if (isset($config['certPrivate'])) {
            $config['certPrivate'] = @file_get_contents(FileModel::getFilePath($config['certPrivate'], false));
            if ($config['certPrivate'] === false)
                $config['certPrivate'] = '读取证书文件失败，请重新上传';
        }
        return json(['status' => 1, 'data' => $config]);
    }

    public function getUpdateProgram()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);
        $result = curl('http://update.zmz999.com/version.json');
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
                unlink('../epay-' . $latestVersion . '.zip');
                //delete update file
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

        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('status')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '订单不存在']);
        if ($result[0]['status'] == 2)
            return json(['status' => 0, 'msg' => '订单已经被冻结,请取消冻结后修改状态']);

        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
            'status'  => $status,
            'endTime' => getDateTime()
        ]);
        if ($result) {
            $orderData = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->field('uid,money')->select();
            if (empty($orderData)) {
                trace('更新订单状态时，修改用户余额异常', 'INFO');
                return json(['status' => 0, 'msg' => '程序异常,请联系管理员处理']);
            }
            $orderData = $orderData[0];

            $userInfo = Db::table('epay_user')->where('id', $orderData['uid'])->field('rate')->limit(1)->select();

            $rate         = $userInfo[0]['rate'] / 100;
            $addMoneyRate = $orderData['money'] * ($rate / 100);
            $addMoneyRate = $addMoneyRate * 10;
            $addMoneyRate = number_format($addMoneyRate, 2, '.', '');
            //计算费率

            if ($status)
                Db::table('epay_user')->where('id', $orderData['uid'])->limit(1)->inc('balance', $addMoneyRate)->update();
            else
                Db::table('epay_user')->where('id', $orderData['uid'])->limit(1)->dec('balance', $addMoneyRate)->update();
        }
        //订单状态确实更新成功
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
        if (empty($keyName))
            return json(['status' => 0, 'msg' => '请求数据有误']);
        if ($isArray) {
            foreach ($value as $key => $value1) {
                if (strpos($key, 'is') === 0) {
                    $value[$key] = $value1 === 'true';
                    //布尔值参数模式
                } else if (strpos($key, 'cert') === 0) {
                    $value[$key] = FileModel::saveString($value1, '.pem');
                    //证书文件模式
                } else {
                    $value[$key] = trim($value1);
                    //普通参数模式
                }
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
        $id     = input('post.id/d');
        $remark = input('post.remark/s', '暂无转账备注');
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
        $userInfo = Db::table('epay_user')->where('id', $result[0]['uid'])->field('balance')->limit(1)->select();
        if (empty($userInfo))
            return json(['status' => 0, 'msg' => '用户不存在，无法结算金额']);
        if ($userInfo[0]['balance'] / 10 - $result[0]['money'] <= 0)
            return json(['status' => 0, 'msg' => '用户余额不足，无法进行结算']);
        $result = $this->confirmSettle($id, $remark);
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

    public function postBatchSetFee()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $fee = input('post.fee/s');
        if (empty($fee))
            return json(['status' => 0, 'msg' => '请求参数不能为空']);

        $isAdd = substr($fee, 0, 1);
        if ($isAdd != '+' && $isAdd != '-')
            return json(['status' => 0, 'msg' => '余额操作符仅支持 + 或 -']);
        $fee = substr($fee, 1, strlen($fee) - 1);
        if (!is_IntOrDecimal($fee))
            return json(['status' => 0, 'msg' => '用户金额格式有误']);

        $isAdd = $isAdd == '+';

        $result = Db::table('epay_user');

        $fee = decimalsToInt($fee, 2);
        if ($isAdd)
            $result = $result->inc('rate', $fee);
        else
            $result = $result->dec('rate', $fee);

        $result->where('rate', '>', 0)->update();
        return json(['status' => 1, 'msg' => '批量更新用户费率成功']);
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

        $setUserBalance      = input('post.setUserBalance/s', 0);
        $clearType           = input('post.clearType/s', 1);
        $clearMode           = input('post.clearMode/d', 0);
        $deposit             = input('post.deposit/s', 0);
        $settleMoney         = input('post.settleMoney/s', 0);
        $domain              = input('post.domain/s', '');
        $email               = input('post.email/s', '');
        $isBan               = input('post.isBan/d', 0);
        $payDayMoneyMax      = input('post.payDayMoneyMax/s', 0);
        $payMoneyMax         = input('post.payMoneyMax/s', 0);
        $qq                  = input('post.qq/s', 0);
        $rate                = input('post.rate/s', 0);
        $username            = input('post.username/s', '');
        $account             = input('post.account/s', '');
        $productNameShowMode = input('post.productNameShowMode/d', 0);
        $orderDiscounts      = input('post.orderDiscounts/s', '');
        $payConfig           = input('post.payConfig/s', '');
        $settleHour          = input('post.settleHour/d', 0);
        $settleFee           = input('post.settleFee/s', 0);

        $rate = decimalsToInt($rate, 2);
        if ($rate > 10000)
            $rate = 10000;
        if (empty($rate)) {
            $systemConfig = getConfig();
            $rate         = $systemConfig['defaultMoneyRate'];
        }
        //add rate default

        $isAdd = false;

        if (!empty($setUserBalance)) {
            $setUserBalanceLength = strlen($setUserBalance);
            if ($setUserBalanceLength == 1)
                return json(['status' => 0, 'msg' => '更新用户金额格式不正确']);
            $isAdd = substr($setUserBalance, 0, 1);
            if ($isAdd != '+' && $isAdd != '-')
                return json(['status' => 0, 'msg' => '余额操作符仅支持 + 或 -']);
            $isAdd = $isAdd == '+';
            //判断是否增加金额
            $setUserBalance = substr($setUserBalance, 1, $setUserBalanceLength - 1);
            if (!is_IntOrDecimal($setUserBalance))
                return json(['status' => 0, 'msg' => '用户金额格式有误']);
            //判断金额格式 禁止那些E
        }
        //设置增加余额不为空

        if (!empty($settleFee)) {
            if (!is_IntOrDecimal($settleFee)) {
                return json(['status' => 0, 'msg' => '结算手续费格式有误']);
            }
        } else {
            $settleFee = 0;
        }
        //判断手续费格式
        if ($clearMode == 3) {
            if (empty($settleHour))
                return json(['status' => 0, 'msg' => '自定义结算时间格式有误']);
        }
        //校验自定义结算时间数据

        //上面上开校验参数了。。。

        $result = Db::table('epay_user')->insertGetId([
            'key'        => getRandChar(32),
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

        if (!empty($setUserBalance))
            if ($isAdd)
                Db::table('epay_user')->where('id', $result)->limit(1)->inc('balance', decimalsToInt($setUserBalance, 3))->update();
            else
                Db::table('epay_user')->where('id', $result)->limit(1)->dec('balance', decimalsToInt($setUserBalance, 3))->update();
        //更新用户余额
        {
            $settleConfig = getPayUserAttr($result, 'settleConfig');
            if (empty($settleConfig))
                $settleConfig = [];
            else
                $settleConfig = unserialize($settleConfig);
            //判断数据是否为空 不为空则序列化

            $settleConfig['settleFee']  = decimalsToInt($settleFee, 3);
            $settleConfig['settleHour'] = $settleHour;

            setPayUserAttr($result, 'settleConfig', serialize($settleConfig));
        }
        //保存结算数据

        if ($clearType == 5 || $clearType == 6) {
            $qrFileID = input('post.qrFileID/d');
            if (empty($qrFileID))
                return json(['status' => 0, 'msg' => '支付二维码不能为空']);
            setPayUserAttr($result, 'qrFileID', $qrFileID);
        }
        if ($productNameShowMode == 1) {
            $productName = input('post.productName/s');
            if (empty($productName))
                $productName = '这是默认商品名，请联系管理员处理';
            setPayUserAttr($result, 'productName', $productName);
        }
        setPayUserAttr($result, 'productNameShowMode', $productNameShowMode);
        setPayUserAttr($result, 'deposit', decimalsToInt($deposit, 2));
        setPayUserAttr($result, 'settleMoney', decimalsToInt($settleMoney, 2));
        setPayUserAttr($result, 'payMoneyMax', decimalsToInt($payMoneyMax, 2));
        setPayUserAttr($result, 'payDayMoneyMax', decimalsToInt($payDayMoneyMax, 2));

        if (!empty($orderDiscounts)) {
            $data = json_decode($orderDiscounts, true);
            if (empty($data))
                return json(['status' => 1, 'msg' => '新增用户成功,但是订单减免功能异常']);
            //try
            setPayUserAttr($result, 'orderDiscounts', serialize($data));
        }

        if (!empty($payConfig)) {
            $data = json_decode($payConfig, true);
            if (empty($data))
                return json(['status' => 1, 'msg' => '新增用户成功,但是支付配置功能异常']);
            setPayUserAttr($result, 'isCancelReturn', $data['isCancelReturn'] ? 'true' : 'false');
            unset($data['isCancelReturn']);
            setPayUserAttr($result, 'payConfig', serialize($data));
        }

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
        $setUserBalance       = input('post.setUserBalance/s', 0);
        $clearType            = input('post.clearType/s', 1);
        $clearMode            = input('post.clearMode/d', 0);
        $deposit              = input('post.deposit/s', 0);
        $settleMoney          = input('post.settleMoney/s', 0);
        $domain               = input('post.domain/s', '');
        $email                = input('post.email/s', '');
        $isBan                = input('post.isBan/d', 0);
        $payDayMoneyMax       = input('post.payDayMoneyMax/s', 0);
        $payMoneyMax          = input('post.payMoneyMax/s', 0);
        $qq                   = input('post.qq/s', 0);
        $username             = input('post.username/s', '');
        $account              = input('post.account/s', '');
        $productNameShowMode  = input('post.productNameShowMode/d', 0);
        $orderDiscounts       = input('post.orderDiscounts/s', '');
        $payConfig            = input('post.payConfig/s', '');
        $settleHour           = input('post.settleHour/d', 0);
        $settleFee            = input('post.settleFee/s', 0);
        $setUserFrozenBalance = input('post.setUserFrozenBalance/s', 0);
        $aliSellerEmail       = input('post.aliSellerEmail/s');

        $rate       = input('post.rate/s', 0);
        $rateQQ     = input('post.rateQQ/s', 0);
        $rateWx     = input('post.rateWx/s', 0);
        $rateAlipay = input('post.rateAlipay/s', 0);

        $rate = decimalsToInt($rate, 2);
        if ($rate > 10000)
            $rate = 10000;

        $rateQQ = decimalsToInt($rateQQ, 2);
        if ($rateQQ > 10000)
            $rateQQ = 10000;

        $rateWx = decimalsToInt($rateWx, 2);
        if ($rateWx > 10000)
            $rateWx = 10000;

        $rateAlipay = decimalsToInt($rateAlipay, 2);
        if ($rateAlipay > 10000)
            $rateAlipay = 10000;

        $isAddUserBalance       = false;
        $isAddUserFrozenBalance = false;

        /**
         * @param $setMoney
         * @return array
         */
        $checkSetBalance = function ($setMoney) {
            $strLen = strlen($setMoney);
            if ($strLen == 1)
                return [-1, ''];
            $isAdd = substr($setMoney, 0, 1);
            if ($isAdd != '+' && $isAdd != '-')
                return [-1, ''];
            $isAdd = $isAdd == '+';
            //判断是否增加金额
            $setUserBalance = substr($setMoney, 1, $strLen - 1);
            if (!is_IntOrDecimal($setUserBalance))
                return [-1, ''];
            //判断金额格式 禁止那些E
            $setMoney = substr($setMoney, 1, $strLen - 1);
            return [$isAdd ? 1 : 2, $setMoney];
        };

        if (!empty($setUserBalance)) {
            $isAddUserBalance = $checkSetBalance($setUserBalance);
            if ($isAddUserBalance[0] == -1)
                return json(['status' => 0, 'msg' => '更新用户金额格式不正确']);
            $setUserBalance   = $isAddUserBalance[1];
            $isAddUserBalance = $isAddUserBalance[0] == 1;
        }
        //设置增加余额不为空
        if (!empty($setUserFrozenBalance)) {
            $isAddUserFrozenBalance = $checkSetBalance($setUserFrozenBalance);
            if ($isAddUserFrozenBalance[0] == -1)
                return json(['status' => 0, 'msg' => '更新用户金额格式不正确']);
            $setUserFrozenBalance   = $isAddUserFrozenBalance[1];
            $isAddUserFrozenBalance = $isAddUserFrozenBalance[0] == 1;
        }
        //设置增加余额不为空

        if (!empty($settleFee)) {
            if (!is_IntOrDecimal($settleFee)) {
                return json(['status' => 0, 'msg' => '结算手续费格式有误']);
            }
        } else {
            $settleFee = 0;
        }
        //判断手续费格式
        if ($clearMode == 3) {
            if (empty($settleHour))
                return json(['status' => 0, 'msg' => '自定义结算时间格式有误']);
        }
        //校验自定义结算时间数据

        //上面上开校验参数了。。。

        if ($clearType == 5 || $clearType == 6) {
            $qrFileID = input('post.qrFileID/d');
            if (empty($qrFileID))
                return json(['status' => 0, 'msg' => '支付二维码不能为空']);
            setPayUserAttr($uid, 'qrFileID', $qrFileID);
        }
        if ($productNameShowMode == 1) {
            $productName = input('post.productName/s');
            if (empty($productName))
                $productName = '这是默认商品名，请联系管理员处理';
            setPayUserAttr($uid, 'productName', $productName);
        }

        setPayUserAttr($uid, 'aliSellerEmail', $aliSellerEmail);
        //保存支付宝转账账户


        setPayUserAttr($uid, 'payRate', serialize([
            'rateWx'     => $rateWx,
            'rateQQ'     => $rateQQ,
            'rateAlipay' => $rateAlipay
        ]));
        //配置支付费率

        Db::table('epay_user')->where('id', $uid)->limit(1)->update([
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
        //更新用户数据
        if (!empty($setUserBalance)) {
            if ($isAddUserBalance)
                $updateResult = Db::table('epay_user')->where('id', $uid)->limit(1)->inc('balance', decimalsToInt($setUserBalance, 3))->update();
            else
                $updateResult = Db::table('epay_user')->where('id', $uid)->limit(1)->dec('balance', decimalsToInt($setUserBalance, 3))->update();
            if ($updateResult)
                Db::table('epay_user_money_log')->insert([
                    'uid'        => $uid,
                    'money'      => ($isAddUserBalance ? '+' : '-') . decimalsToInt($setUserBalance, 3),
                    'desc'       => '',
                    'createTime' => getDateTime()
                ]);
        }
        //更新用户余额
        {
            if (!empty($setUserFrozenBalance)) {
                if (!$this->frozenMoney($uid, $setUserFrozenBalance, $isAddUserFrozenBalance))
                    return json(['status' => 0, 'msg' => '冻结用户金额异常 请重试']);
            }
        }
        //更新冻结金额
        {
            $settleConfig = getPayUserAttr($uid, 'settleConfig');
            if (empty($settleConfig))
                $settleConfig = [];
            else
                $settleConfig = unserialize($settleConfig);
            //判断数据是否为空 不为空则序列化

            $settleConfig['settleFee']  = decimalsToInt($settleFee, 3);
            $settleConfig['settleHour'] = $settleHour;

            setPayUserAttr($uid, 'settleConfig', serialize($settleConfig));
        }
        //保存结算数据

        setPayUserAttr($uid, 'productNameShowMode', $productNameShowMode);
        setPayUserAttr($uid, 'deposit', decimalsToInt($deposit, 2));
        setPayUserAttr($uid, 'settleMoney', decimalsToInt($settleMoney, 2));
        setPayUserAttr($uid, 'payMoneyMax', decimalsToInt($payMoneyMax, 2));
        setPayUserAttr($uid, 'payDayMoneyMax', decimalsToInt($payDayMoneyMax, 2));
        if (!empty($orderDiscounts)) {
            $data = json_decode($orderDiscounts, true);
            if (!empty($data))
                setPayUserAttr($uid, 'orderDiscounts', serialize($data));
        }
        if (!empty($payConfig)) {
            $data = json_decode($payConfig, true);
            if (empty($data))
                return json(['status' => 1, 'msg' => '新增用户成功,但是支付配置功能异常']);

            foreach ($data as $key => $value) {
                if ($key != 'isCancelReturn')
                    if ($value['apiType'] === null || $value['payAisle'] === null || $value['isOpen'] === null)
                        return json(['status' => 1, 'msg' => '更新用户信息成功,但是支付配置功能未能正常保存。']);
            }

            setPayUserAttr($uid, 'isCancelReturn', $data['isCancelReturn'] ? 'true' : 'false');
            unset($data['isCancelReturn']);
            setPayUserAttr($uid, 'payConfig', serialize($data));
        }
        return json(['status' => 1, 'msg' => '保存用户信息成功']);
    }

    public function getUserTradeTotal()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $uid = input('get.uid/d');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '用户id无效']);


        $buildOrderStatistics = function (string $date, int $uid) {
            $userData = Db::table('epay_user')->field('rate')->where('id', $uid)->limit(1)->select();
            if (empty($userData))
                return [
                    'totalOrder'     => 0,
                    'successOrder'   => 0,
                    'updateMoney'    => 0,
                    'tradeMoney'     => 0,
                    'tradeMoneyRate' => 0
                ];
            //获取费率
            $rate = $userData[0]['rate'] / 100;
            //用户费率
            $totalOrder = Db::table('epay_order')->where('uid', $uid)->whereBetweenTime('createTime', $date)->cache(60)->count();
            //总共订单
            $successOrder = Db::table('epay_order')->where('uid', $uid)->whereBetweenTime('endTime', $date)->where('status', 1)->cache(60)->count();
            //成功订单
            $updateMoney = Db::table('epay_user_money_log')->where('uid', $uid)->where([
                ['createTime', '>=', $date . ' 00:00:00'],
                ['createTime', '<=', $date . ' 23:59:59']
            ])->cache(60)->sum('money');
            $updateMoney /= 1000;
            //增减金额总共
            $tradeMoney = Db::table('epay_order')->where([
                'uid'      => $uid,
                'status'   => 1,
                'isShield' => 0
            ])->whereBetweenTime('endTime', $date)->cache(60)->sum('money');
            //没计算费率的交易金额
            $tradeMoneyRate = $tradeMoney * ($rate / 100);

            $alipayRateMoney = Db::table('epay_user_data_model')->where([
                'uid'      => $uid,
                'attrName' => 'alipayRateMoney'
            ])->where([
                ['createTime', '>=', $date . ' 00:00:00'],
                ['createTime', '<=', $date . ' 23:59:59']
            ])->cache(60)->sum('data');
            //支付宝扣除费率金额

            $alipayTotalMoney = Db::table('epay_order')->where([
                'uid'    => $uid,
                'type'   => 3,
                'status' => 1
            ])->where([
                ['endTime', '>=', $date . ' 00:00:00'],
                ['endTime', '<=', $date . ' 23:59:59']
            ])->cache(60)->sum('money');
            //支付宝当日总金额
            return [
                'alipayTotalMoney'  => $alipayTotalMoney,
                'alipayRateMoney'   => $alipayRateMoney,
                'totalOrder'        => $totalOrder,
                'successOrder'      => $successOrder,
                'updateMoney'       => $updateMoney,
                'tradeMoney'        => number_format($tradeMoney / 100, 2, '.', ''),
                'tradeMoneyProfits' => number_format(($tradeMoney - $tradeMoneyRate) / 100, 2, '.', ''),
            ];
        };

        $data           = [];
        $createTimeList = [
            date('Y-m-d', strtotime('- 2 day')),
            date('Y-m-d', strtotime('- 1 day')),
            date('Y-m-d', strtotime('now'))
        ];
        foreach ($createTimeList as $time) {
            $data[$time] = $buildOrderStatistics($time, $uid);
        }
        return json(['status' => 1, 'data' => $data]);
    }

    /**
     * @return \think\response\Json
     */
    public function getCenterPayApiList()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $payType = input('get.payType/s');

        $systemPayConfig = getConfig();
        if (!empty($systemPayConfig[$payType]))
            $systemPayConfig = $systemPayConfig[$payType];
        else
            $systemPayConfig = [];

        if (!isset($systemPayConfig['epayCenterUid']) || !isset($systemPayConfig['epayCenterKey']))
            return json(['status' => 1, 'data' => []]);

        $systemPayConfig['gateway'] = 'http://center.zmz999.com/';
        $centerPayModel             = new CenterPayModel($systemPayConfig);

        return json(['status' => 1, 'data' => $centerPayModel->getPayApiList(PayModel::converPayName($payType))]);
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

    public function postSetFrozen()
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
        $orderInfo = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->field('money,uid,status')->select();
        if (empty($orderInfo))
            return json(['status' => 0, 'msg' => '订单不存在无法冻结订单']);
        if ($orderInfo[0]['status'] != 2 && $orderInfo[0]['status'] != 1)
            return json(['status' => 0, 'msg' => '订单尚未支付,无法进行冻结订单']);
        $frozenMoney = ($orderInfo[0]['money'] / 100) * 20;
        $isSuccess   = $this->frozenMoney($orderInfo[0]['uid'], $frozenMoney, $status == 1);
        if (!$isSuccess)
            return json(['status' => 0, '冻结订单失败,请重试']);
        Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update(['status' => $status == 1 ? 2 : 1]);
        return json(['status' => 1, 'msg' => ($status ? '' : '取消') . '冻结订单成功']);
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
        $orderInfo = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->field('money,uid,status')->select();
        if (empty($orderInfo))
            return json(['status' => 0, 'msg' => '订单不存在无法更改屏蔽状态']);
        if (!$orderInfo[0]['status'])
            return json(['status' => 0, 'msg' => '订单尚未支付,无法进行更改屏蔽状态']);
        $userInfo = Db::table('epay_user')->where('id', $orderInfo[0]['uid'])->field('rate')->limit(1)->select();

        $rate         = $userInfo[0]['rate'] / 100;
        $addMoneyRate = $orderInfo[0]['money'] * ($rate / 100);
        $addMoneyRate = $addMoneyRate * 10;
        $addMoneyRate = number_format($addMoneyRate, 2, '.', '');
        //计算费率

        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
            'isShield' => $status
        ]);
        if ($result) {
            if (!$status)
                Db::table('epay_user')->limit(1)->where('id', $orderInfo[0]['uid'])->inc('balance', $addMoneyRate)->update();
            else
                Db::table('epay_user')->limit(1)->where('id', $orderInfo[0]['uid'])->dec('balance', $addMoneyRate)->update();
            addServerLog(1, 5, getClientIp(), ($status ? '屏蔽' : '恢复') . '订单 tradeNo=> ' . $tradeNo . ' uid => ' . $orderInfo[0]['uid'] . ' money => ' . (($addMoneyRate * 10) / 1000));
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

        addServerLog(1, 4, 'System', '管理员操作 重新手动回调 tradeNo=> ' . $tradeNo);
        return json(['status' => 1, 'url' => $callbackUrl]);
    }


    public function postBatchCallback()
    {
        $username = session('username', '', 'admin');
        if (empty($username))
            return json(['status' => 0, 'msg' => '您需要登录后才能操作']);

        $uid       = input('post.uid/d');
        $payType   = input('post.payType/s');
        $startTime = input('post.startTime/s');
        $endTime   = input('post.endTime/s');
        if (empty($uid))
            return json(['status' => 0, 'msg' => '商户ID不能为空']);
        if (empty($startTime) || empty($endTime))
            return json(['status' => 0, 'msg' => '开始或结束时间不能为空']);
        if ($payType != 'all' && $payType != '1' && $payType != '2' && $payType != '3')
            return json(['status' => 0, 'msg' => '支付类型有误']);

        $notifyCount = 0;
        $filterData  = [
            ['uid', '=', $uid],
            ['endTime', '>=', $startTime],
            ['endTime', '<=', $endTime],
            ['status', '=', 1]
        ];
        if ($payType != 'all')
            $filterData['type'] = $payType;
        $data = Db::table('epay_order')->where($filterData)->field('tradeNo')->cursor();
        foreach ($data as $value) {
            $notifyCount++;
            addCallBackLog($uid, buildCallBackUrl($value['tradeNo'], 'notify'));
        }
        if (!$notifyCount)
            return json(['status' => 0, 'msg' => '暂无查询到更多的订单']);
        return json(['status' => 1, 'msg' => '获取到共计 ' . $notifyCount . ' 个订单，将在15秒全部回调完毕']);
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

        $args        = input('post.args/a');
        $SearchTable = new SearchTable($searchTable, $startSite, $getLength, $order, $searchValue, $args);
        return json($SearchTable->getData());
    }

    public function postOrderRefund()
    {
        $tradeNo = input('post.tradeNo/s');
        $type    = input('post.type/s');
        if (empty($tradeNo) || empty($type))
            return json(['status' => 0, 'msg' => '参数异常']);

        if ($type != '微信' && $type != '财付通')
            return json(['status' => 0, 'msg' => '暂不不支持此订单类型退款']);

        $orderData    = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('money,uid')->limit(1)->select();
        $systemConfig = getConfig();
        if ($type == '微信') {
            try {
                $payConfig = json_decode(PayModel::getOrderAttr($tradeNo, 'payConfig'), true);
                $wxPay     = new WxPayModel(WxPay::getWxxPayConfig($tradeNo, $systemConfig));
                $result    = $wxPay->orderRefund($tradeNo, $orderData[0]['money'], $orderData[0]['money'], Wxx::getWxxCertFilePath($payConfig['accountID']), url('/Pay/WxPay/RefundNotify', '', false, true));
                if (!$result[0])
                    return json(['status' => 0, 'msg' => $result[1]]);
                if ($payConfig['configType'] == 1)
                    Db::table('epay_user')->where('id', $orderData[0]['uid'])->limit(1)->dec('balance', $orderData[0]['money'] * 10)->update();
                Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
                    'status' => 3
                ]);
                return json(['status' => 1, 'msg' => '成功提交退款']);
            } catch (\Exception $e) {
                trace('[订单退款异常]' . $e->getMessage(), 'error');
                return json(['status' => 0, 'msg' => '异常了，请联系相关人员处理']);
            }
        } else if ($type == '财付通') {
            $QQPayModel = new QQPayModel($systemConfig['qqpay']);
            try {
                $result = $QQPayModel->orderRefund($tradeNo, $orderData[0]['money']);
                if (!$result[0])
                    return json(['status' => 0, 'msg' => $result[1]]);
                Db::table('epay_user')->where('id', $orderData[0]['uid'])->limit(1)->dec('balance', $orderData[0]['money'] * 10)->update();
                Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
                    'status' => 4
                ]);
                return json(['status' => 1, 'msg' => '成功提交退款']);
            } catch (\Exception $e) {
                trace('[订单退款异常]' . $e->getMessage(), 'error');
                return json(['status' => 0, 'msg' => '异常了，请联系相关人员处理']);
            }
        }

        return json(['status' => 0, 'msg' => '系统遇到了异常']);

    }

    /**
     * 冻结用户金额
     * @param int $uid
     * @param string $setUserFrozenBalance
     * @param bool $isAddUserFrozenBalance
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function frozenMoney(int $uid, string $setUserFrozenBalance, bool $isAddUserFrozenBalance)
    {
        if ($isAddUserFrozenBalance) {
            $updateResult = Db::table('epay_user')->where('id', $uid)->limit(1)->dec('balance', decimalsToInt($setUserFrozenBalance, 3))->update();
        } else {
            $updateResult = Db::table('epay_user')->where('id', $uid)->limit(1)->inc('balance', decimalsToInt($setUserFrozenBalance, 3))->update();
        }
        if (!$updateResult) {
            trace('冻结用户金额异常 uid => ' . $uid, 'error');
            return false;
        }
        $userFrozenBalance = getPayUserAttr($uid, 'frozenBalance');
        if ($userFrozenBalance == '')
            $userFrozenBalance = 0;
        else
            $userFrozenBalance = floatval($userFrozenBalance);
        $userFrozenBalance += floatval((!$isAddUserFrozenBalance ? '+' : '-') . $setUserFrozenBalance);
        $userFrozenBalance = number_format($userFrozenBalance, 2, '.', '');
        $updateResult      = setPayUserAttr($uid, 'frozenBalance', $userFrozenBalance);
        if (!$updateResult) {
            trace('更新冻结金额异常 uid => ' . $uid . ' frozenBalance=> ' . $userFrozenBalance, 'error');
            return false;
        }
        return true;
    }

    private function confirmSettle($settleID, $remark = '暂无转账备注')
    {
        $result = Db::table('epay_settle')->where('id', $settleID)->field('status,clearType,addType,money,uid')->limit(1)->select();
        if (empty($result))
            return false;
        if ($result[0]['status'])
            return false;
        //if settle is ok
        $updateUserResult = Db::table('epay_user')->where('id', $result[0]['uid'])->limit(1)->dec('balance', $result[0]['money'] * 10)->update();
        if (!$updateUserResult)
            return false;
        $result = Db::table('epay_settle')->where('id', $settleID)->update([
            'remark'     => $remark,
            'status'     => 1,
            'updateTime' => getDateTime()
        ]);
        return $result != 0;
    }

    private function getDashboardData()
    {
        $cacheDashboardData = cache('DashboardData');
        if (empty($cacheDashboardData)) {
            $data['totalOrder'] = Db::table('epay_order')->count('id');
            $data['totalUser']  = Db::table('epay_user')->count('id');
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
            {
                $buildOrderStatistics = function (string $date) {
                    $totalOrder   = Db::table('epay_data_model')->where('attrName', 'in', [
                        'order_total_count_3',
                        'order_total_count_2',
                        'order_total_count_1'
                    ])->where([
                        ['createTime', '>=', $date . ' 00:00:00'],
                        ['createTime', '<=', $date . ' 23:59:59']
                    ])->sum('data');
                    $successOrder = Db::table('epay_data_model')->where('attrName', 'in', [
                        'order_total_count_success_3',
                        'order_total_count_success_2',
                        'order_total_count_success_1'
                    ])->where([
                        ['createTime', '>=', $date . ' 00:00:00'],
                        ['createTime', '<=', $date . ' 23:59:59']
                    ])->sum('data');
                    if ($successOrder == 0 || $totalOrder == 0)
                        $ratio = '0';
                    else
                        $ratio = number_format($successOrder / $totalOrder * 100, 2);


                    {
                        $alipayA  = 0;
                        $userList = Db::query('SELECT epay_user.id,epay_user_attr.`value` FROM epay_user INNER JOIN epay_user_attr ON epay_user.id = epay_user_attr.uid WHERE epay_user_attr.`key` = "aliSellerEmail" AND epay_user_attr.`value` <> ""');

                        if (!empty($userList)) {
                            foreach ($userList as $content) {
                                if (empty($content['value']))
                                    continue;
                                $alipayA += Db::table('epay_user_data_model')->where([
                                    'uid'      => $content['id'],
                                    'attrName' => 'alipayRateMoney'
                                ])->where([
                                    ['createTime', '>=', $date . ' 00:00:00'],
                                    ['createTime', '<=', $date . ' 23:59:59']
                                ])->sum('data');
                            }
                        }
                    }
                    //上面这段是负责统计支付宝独立号数据的

                    return [
                        'totalOrder'   => $totalOrder,
                        'successOrder' => $successOrder,
                        'ratio'        => $ratio,
                        'alipayA'      => $alipayA
                    ];
                };

                $data['orderDataStatistics'] = [];
                $createTimeList              = [
                    date('Y-m-d', strtotime('- 1 day')),
                    date('Y-m-d', strtotime('now'))
                ];
                foreach ($createTimeList as $time) {
                    $data['orderDataStatistics'][$time] = $buildOrderStatistics($time);
                }
            }
            $createTimeList     = [
                date('Y-m-d', strtotime('- 1 day')),
                date('Y-m-d', strtotime('now'))
            ];
            $data['statistics'] = [
                'yesterday' => [
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_1'],
                            ['createTime', '>=', $createTimeList[0] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[0] . ' 23:59:59']
                        ])->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_2'],
                            ['createTime', '>=', $createTimeList[0] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[0] . ' 23:59:59']
                        ])->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_3'],
                            ['createTime', '>=', $createTimeList[0] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[0] . ' 23:59:59']
                        ])->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_4'],
                            ['createTime', '>=', $createTimeList[0] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[0] . ' 23:59:59']
                        ])->sum('data')
                    ]
                ],
                'today'     => [
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_1'],
                            ['createTime', '>=', $createTimeList[1] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[1] . ' 23:59:59']
                        ])->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_2'],
                            ['createTime', '>=', $createTimeList[1] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[1] . ' 23:59:59']
                        ])->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_3'],
                            ['createTime', '>=', $createTimeList[1] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[1] . ' 23:59:59']
                        ])->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where([
                            ['attrName', '=', 'money_total_4'],
                            ['createTime', '>=', $createTimeList[1] . ' 00:00:00'],
                            ['createTime', '<=', $createTimeList[1] . ' 23:59:59']
                        ])->sum('data')
                    ]
                ]
            ];
            {
                $cacheOrderDataComparison = cache('orderDataComparison');
                if (empty($cacheOrderDataComparison)) {
                    $yesterday           = date('Y-m-d', strtotime('-1 day'));
                    $today               = date('Y-m-d', time());
                    $orderDataComparison = [
                        $yesterday => [],
                        $today     => []
                    ];
                    for ($i = 0; $i < 24; $i++) {
                        $o                                               = $i + 1;
                        $hoursStartStr                                   = ($i >= 10 ? $i . '' : '0' . $i) . ':00:00';
                        $hoursEndStr                                     = ($o >= 10 ? $o . '' : '0' . $o) . ':00:00';
                        $orderDataComparison[$yesterday][$hoursStartStr] = Db::table('epay_data_model')->where('attrName', 'in', [
                            'order_total_count_3',
                            'order_total_count_2',
                            'order_total_count_1'
                        ])
                            ->whereTime('createTime', '>=', $yesterday . ' ' . $hoursStartStr)
                            ->whereTime('createTime', '<=', $yesterday . ' ' . $hoursEndStr)->sum('data');
                        $orderDataComparison[$today][$hoursStartStr]     = Db::table('epay_data_model')->where('attrName', 'in', [
                            'order_total_count_3',
                            'order_total_count_2',
                            'order_total_count_1'
                        ])
                            ->whereTime('createTime', '>=', $today . ' ' . $hoursStartStr)
                            ->whereTime('createTime', '<=', $today . ' ' . $hoursEndStr)->sum('data');
                    }
                    $data['orderDataComparison'] = $orderDataComparison;
                    cache('orderDataComparison', json_encode($orderDataComparison), 120);
                } else {
                    $data['orderDataComparison'] = json_decode($cacheOrderDataComparison, true);
                }
            }
            //获取分时订单
            cache('DashboardData', json_encode($data), 120);
        } else {
            $data = json_decode($cacheDashboardData, true);
        }
        return $data;
    }
}
