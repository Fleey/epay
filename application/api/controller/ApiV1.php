<?php

namespace app\api\controller;

use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
use think\Controller;
use think\Db;

class ApiV1 extends Controller
{
    private $systemConfig;

    /**
     * @return mixed
     */
    public function loadTemplate()
    {
        $this->systemConfig = getConfig();
        return $this->fetch('/ApiDocTemplateV1', [
            'webName' => $this->systemConfig['webName'],
            'webQQ' => $this->systemConfig['webQQ']
        ]);
    }

    /**
     * 接口操作
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function apiCtrl()
    {
        $act = input('get.act/s');
        $uid = input('get.pid/d');
        $userKey = input('get.key/s');
        if (empty($uid))
            return json(['code' => 0, 'msg' => '商户ID不能为空！']);
        if (empty($userKey))
            return json(['code' => 0, 'msg' => '商户密钥不能为空！']);

        $userInfo = Db::table('epay_user')->where('id', $uid)->field('key,isBan,balance,account,username,rate')->limit(1)->select();
        if (empty($userInfo))
            return json(['code' => 0, 'msg' => '密匙效验失败']);
        if ($userInfo[0]['key'] != $userKey)
            return json(['code' => 0, 'msg' => '密匙效验失败']);
        //check user key
        if ($act == 'add') {
            return json(['code' => 0, 'msg' => '当前接口仅作为备用接口使用']);
        } else if ($act == 'query') {
            return json([
                'code' => 1,
                'pid' => $uid,
                'key' => $userKey,
                'type' => 1,
                'active' => !$userInfo[0]['isBan'],
                'money' => $userInfo[0]['balance'] / 100,
                'account' => $userInfo[0]['account'],
                'username' => $userInfo[0]['username'],
                'settle_money' => 0,
                'settle_fee' => 0,
                'money_rate' => $userInfo[0]['rate'] / 100
            ]);
        } else if ($act == 'change') {
            $account = input('get.account/s');
            $username = input('get.username/s');
            if (empty($account) || empty($username))
                return json(['code' => 0, 'msg' => '用户名或结算账户 为空']);
            if (strlen($account) > 32)
                return json(['code' => 0, 'msg' => '结算账户长度不能超过32个字符']);
            if (strlen($username) > 10)
                return json(['code' => 0, 'msg' => '结算用户名长度不能超过10个字符']);
            $updateResult = Db::table('epay_user')->where('id', $uid)->limit(1)->update([
                'account' => $account,
                'username' => $username
            ]);
            if ($updateResult)
                return json(['code' => 1, 'msg' => '修改收款账号成功！']);
            return json(['code' => 0, 'msg' => '修改收款账号失败，请重试']);
        } else if ($act == 'settle') {
            $settleResult = Db::table('epay_settle')->where('uid', $uid)
                ->field('clearType,account,username,money,status,createTime,updateTime')->order('id', 'desc')->limit(10)->select();
            if (empty($settleResult))
                return json(['code' => 0, 'msg' => '暂无查询到结算记录']);
            return json(['code' => 1, 'msg' => '查询结算记录成功！', 'data' => $settleResult]);
        } else if ($act == 'order') {
            $tradeNo = input('get.trade_no/s');
            $tradeNoOut = input('get.out_trade_no/s');
            if (empty($tradeNo) && empty($tradeNoOut))
                return json(['code' => 0, 'msg' => '平台单号 或 商家单号不能为空']);
            $selectData = [
                'uid' => $uid
            ];
            if (!empty($tradeNo))
                $selectData['tradeNo'] = $tradeNo;
            if (!empty($tradeNoOut))
                $selectData['tradeNoOut'] = $tradeNoOut;
            $selectResult = Db::table('epay_order')->limit(1)->where($selectData)->select();
            if (empty($selectResult))
                return json(['code' => 0, 'msg' => '订单不存在']);
            return json([
                'code' => 1,
                'msg' => '查询订单号成功！',
                'pid' => $uid,
                'trade_no' => $selectResult[0]['tradeNo'],
                'out_trade_no' => $selectResult[0]['tradeNoOut'],
                'name' => $selectResult[0]['productName'],
                'addtime' => $selectResult[0]['createTime'],
                'endtime' => $selectResult[0]['endTime'],
                'status' => $selectResult[0]['status'],
                'money' => $selectResult[0]['money'] / 100,
                'type' => $this->converPayName($selectResult[0]['type'], true)
            ]);
        } else if ($act == 'orders') {
            $limit = input('get.limit/d', 20);
            $page = input('get.page/d', 1);
            if (empty($page) || empty($limit))
                return json(['msg' => '参数有误', 'code' => 0]);
            if ($limit > 50)
                $limit = 50;
            $selectResult = Db::table('epay_order')
                ->field('type,tradeNo as trade_no,tradeNoOut as out_trade_no,productName as name,createTime as addtime,endTime as endtime,status,money')->where('uid', $uid)->page($page, $limit)->select();
            if (empty($selectResult))
                return json(['code' => 0, 'msg' => '暂无查询到更多的订单']);
            return json([
                'code' => 1,
                'msg' => '查询结算记录成功！',
                'data' => $selectResult
            ]);
        }
        return json(['code' => 0, 'msg' => '操作类型有误！']);
    }

    /**
     * 获取二维码支付接口
     */
    public function apiQrCode()
    {
        $uid = input('get.pid/d');
        $type = input('get.type/s');
        $tradeNoOut = input('get.out_trade_no/s');
        $notifyUrl = input('get.notify_url/s');
        $productName = input('get.name/s');
        $money = input('get.money/s');
        $sign = input('get.sign/s');
        $signType = input('get.sign_type/s');
        //get param
        if (is_null($uid) || is_null($type) || is_null($tradeNoOut) || is_null($notifyUrl) || is_null($productName) ||
            is_null($money) || is_null($sign) || is_null($signType))
            return json(['code' => 0, 'msg' => '参数不能为空']);

        $userInfo = Db::table('epay_user')->where('id', $uid)->field('key,isBan')->limit(1)->select();
        if (empty($userInfo))
            return json(['code' => 0, 'msg' => '签名校验失败，请返回重试！']);
        if (!$this->checkSign(input('get.'), $userInfo[0]['key'], $sign))
            return json(['code' => 0, 'msg' => '签名校验失败，请返回重试！']);
        //check sign
        if ($userInfo[0]['isBan'])
            return json(['code' => 0, 'msg' => '商户已封禁，无法支付！']);
        //check sign and is ban user
        $type = strtolower($type);
        if ($type != 'qqpay' && $type != 'wxpay')
            return json(['code' => 0, 'msg' => '支付方式有误']);
        //check pay type
        $money = decimalsToInt($money, 2);
        if ($money > 1000 || $money <= 0)
            return json(['code' => 0, 'msg' => '支付金额有误']);

        $maxPayMoney = getPayUserAttr($uid, 'maxPayMoney');
        if (!empty($maxPayMoney)) {
            $maxPayMoney = decimalsToInt($maxPayMoney, 2);
            if (!empty($maxPayMoney)) {
                if ($money > $maxPayMoney)
                    return json(['code' => 0, 'msg' => '超出商户单个订单最大支付金额']);
            }
        } else {
            if ($money > $this->systemConfig['defaultMaxPayMoney'])
                return json(['code' => 0, 'msg' => '超出商户单个订单最大支付金额']);
        }
        //check user max pay money
        if (!preg_match('/^[a-zA-Z0-9.\_\-|]{1,64}+$/', $tradeNoOut))
            return json(['code' => 0, 'msg' => '订单号(out_trade_no)格式不正确 最小订单号位一位 最大为64位']);
        //check trade out

        $goodsFilterKeyList = explode(',', $this->systemConfig['goodsFilter']['keyWord']);
        foreach ($goodsFilterKeyList as $value) {
            if (!(strpos($productName, $value) === FALSE))
                return json(['code' => 0, 'msg' => $this->systemConfig['goodsFilter']['tips']]);
        }
        //check goods filter key
        $tradeNo = date('YmdHis') . rand(11111, 99999);

        $createTime = getDateTime();
        $result = Db::table('epay_order')->insert([
            'uid' => $uid,
            'tradeNo' => $tradeNo,
            'tradeNoOut' => $tradeNoOut,
            'notify_url' => $notifyUrl,
            'return_url' => '',
            'money' => $money,
            'type' => $this->converPayName($type),
            'productName' => $productName,
            'ipv4' => $this->request->ip(),
            'status' => 0,
            'createTime' => $createTime
        ]);
        if (!$result)
            return json(['code' => 0, 'msg' => '创建订单失败,请重试']);

        if ($type == 'wxpay') {
            $wxPayModel = new WxPayModel($this->systemConfig['wxpay']);
            $requestResult = $wxPayModel->sendPayRequest([
                'money' => $money,
                'tradeNo' => $tradeNo
            ], 'NATIVE');
            if ($requestResult['return_code'] != 'SUCCESS' && $requestResult['result_code'] != 'SUCCESS')
                return json(['code' => 0, 'msg' => '微信支付下单失败！[' . $requestResult['err_code'] . ']' . $requestResult['err_code_desc']]);
        } else {
            $qqPayModel = new QQPayModel($this->systemConfig['qqpay']);
            $param = [
                'out_trade_no' => $tradeNo,
                'body' => $result[0]['productName'],
                'fee_type' => 'CNY',
                'notify_url' => url('/Pay/QQPay/Notify', '', false, true),
                'spbill_create_ip' => request()->ip(),
                'total_fee' => $result[0]['money'],
                'trade_type' => 'NATIVE'
            ];
            $requestResult = $qqPayModel->sendPayRequest($param);
            if ($requestResult['return_code'] != 'SUCCESS' && $requestResult['result_code'] != 'SUCCESS')
                return json(['code' => 0, 'msg' => 'QQ钱包支付下单失败！[' . $requestResult['err_code'] . ']' . $requestResult['err_code_desc']]);
        }
        return json([
            'code' => 1,
            'msg' => '下单成功！',
            'trade_no' => $tradeNo,
            'out_trade_no' => $tradeNoOut,
            'code_url' => $requestResult['code_url']
        ]);
    }

    /**
     * 转换支付名称 主要为了兼容老接口 和 优化数据库
     * @param $payName
     * @param bool $isReversal
     * @return int|String
     */
    private function converPayName($payName, $isReversal = false)
    {
        if ($isReversal) {
            switch ($payName) {
                case 1:
                    $payName = 'wxpay';
                    break;
                case 3:
                    $payName = 'alipay';
                    break;
                case 2:
                    $payName = 'tenpay';
                    break;
                default:
                    $payName = 'null';
                    break;
            }
        } else {
            switch ($payName) {
                case 'wxpay':
                    $payName = 1;
                    break;
                case 'alipay':
                    $payName = 3;
                    break;
                case 'qqpay':
                    $payName = 2;
                    break;
                case 'tenpay':
                    $payName = 2;
                    break;
                default:
                    $payName = 0;
                    break;
            }
        }
        return $payName;
    }

    /**
     * 效验签名
     * @param array $data
     * @param $key
     * @param $sign
     * @return bool
     */
    private function checkSign(array $data, string $key, string $sign)
    {
        return verifyMD5(createLinkString(argSort(paraFilter($data))), $key, $sign);
    }
}