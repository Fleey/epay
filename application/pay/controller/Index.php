<?php

namespace app\pay\controller;

use app\pay\model\PayModel;
use think\App;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Controller
{
    private $systemConfig;
    private $getData;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->systemConfig = getConfig();
    }

    /**
     * 聚合支付 提交创建订单部分
     * @return mixed|\think\response\Redirect
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function submit()
    {
        if (empty($_SERVER['HTTP_USER_AGENT']))
            exit();
        if ($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1)')
            exit();
        //判断UA

        $tempDataA = input('get.');
        $tempDataB = input('post.');
        if (!empty($tempDataA['pid'])) {
            $this->getData = $tempDataA;
        } else if (!empty($tempDataB['pid'])) {
            $this->getData = $tempDataB;
        } else {
            return $this->fetch('/SystemMessage', ['msg' => '你还未配置支付接口商户！']);
        }
        //效验数据

        $uid = intval($this->getData['pid']);
        //get uid
        if (empty($uid))
            return $this->fetch('/SystemMessage', ['msg' => 'PID不存在！']);
        if (empty($this->getData['sign']))
            return $this->fetch('/SystemMessage', ['msg' => '签名(sign)不能为空']);
        $userData = Db::table('epay_user')->limit(1)->field('key,isBan')->where('id', $uid)->select();
        if (empty($userData))
            return $this->fetch('/SystemMessage', ['msg' => '签名校验失败，请返回重试！']);
        if (!PayModel::checkSign($this->getData, $userData[0]['key'], $this->getData['sign']))
            return $this->fetch('/SystemMessage', ['msg' => '签名校验失败，请返回重试！']);
        if ($userData[0]['isBan'])
            return $this->fetch('/SystemMessage', ['msg' => '商户已封禁，无法支付！']);

        if (empty($this->getData['money']))
            return $this->fetch('/SystemMessage', ['msg' => '金额(money)不能为空']);
        if (empty($this->getData['type']))
            return $this->fetch('/SystemMessage', ['msg' => '支付类型(type)不能为空']);
        if (empty($this->getData['out_trade_no']))
            return $this->fetch('/SystemMessage', ['msg' => '订单号(out_trade_no)不能为空']);
        if (empty($this->getData['name']))
            return $this->fetch('/SystemMessage', ['msg' => '商品名称(name)不能为空']);
        if (empty($this->getData['notify_url']))
            return $this->fetch('/SystemMessage', ['msg' => '通知地址(notify_url)不能为空']);
        if (empty($this->getData['return_url']))
            return $this->fetch('/SystemMessage', ['msg' => '回调地址(return_url)不能为空']);

        if (!is_IntOrDecimal($this->getData['money']))
            return $this->fetch('/SystemMessage', ['msg' => '金额(money) 格式不正确']);
        //判断金额格式 禁止那些E
        $type        = $this->getData['type'];
        $tradeNoOut  = $this->getData['out_trade_no'];
        $notifyUrl   = strip_tags($this->getData['notify_url']);
        $returnUrl   = strip_tags($this->getData['return_url']);
        $productName = strip_tags($this->getData['name']);
        $money       = decimalsToInt($this->getData['money'], 2);
        $siteName    = empty($this->getData['sitename']) ? '聚合支付平台' : urlencode(base64_encode($this->getData['sitename']));
        //build param
        if (empty($notifyUrl))
            return $this->fetch('/SystemMessage', ['msg' => '通知地址(notify_url)不能为空']);
        if (empty($returnUrl))
            return $this->fetch('/SystemMessage', ['msg' => '回调地址(return_url)不能为空']);
        if (empty($productName))
            return $this->fetch('/SystemMessage', ['msg' => '商品名称(name)不能为空']);
        if (mb_strlen($productName) > 64)
            return $this->fetch('/SystemMessage', ['msg' => '商品名称(name)长度不能超过64个字符']);

        try {
            PayModel::checkBadWord($this->systemConfig, $productName, $uid);
        } catch (Exception $exception) {
            return $this->fetch('/SystemMessage', ['msg' => $exception->getMessage()]);
        }
        //检测违禁词

        if ($money <= 0)
            return $this->fetch('/SystemMessage', ['msg' => '金额(money)格式有误']);

        try {
            PayModel::checkUserMaxPayMoney($money, $uid, $this->systemConfig);
        } catch (Exception $exception) {
            return $this->fetch('/SystemMessage', ['msg' => $exception->getMessage()]);
        }
        //check user max pay money

        if (!preg_match('/^[a-zA-Z0-9.\_\-|]{1,64}+$/', $tradeNoOut))
            return $this->fetch('/SystemMessage', ['msg' => '订单号(out_trade_no)格式不正确 最小订单号位一位 最大为64位']);

        $converPayType = PayModel::converPayName($type);

        if (empty($converPayType))
            return $this->fetch('/SystemMessage', ['msg' => '支付类型(type)暂不支持该方式']);

//        if($money < 100 && $converPayType == 1)
//            return $this->fetch('/SystemMessage',['msg'=>'微信最低支付金额 1 RMB']);

        if ($converPayType == 3 && !$this->systemConfig['alipay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['alipay']['tips']]);
        } else if ($converPayType == 2 && !$this->systemConfig['qqpay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['qqpay']['tips']]);
        } else if ($converPayType == 1 && !$this->systemConfig['wxpay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['wxpay']['tips']]);
        } else if ($converPayType == 4 && !$this->systemConfig['bankpay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['bankpay']['tips']]);
        }
        //check is open pay 总开关有效

        $userPayConfig = unserialize(getPayUserAttr($uid, 'payConfig'));
        if (!empty($userPayConfig)) {
            if ($converPayType == 3 && !$userPayConfig['alipay']['isOpen']) {
                return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['alipay']['tips']]);
            } else if ($converPayType == 2 && !$userPayConfig['qqpay']['isOpen']) {
                return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['qqpay']['tips']]);
            } else if ($converPayType == 1 && !$userPayConfig['wxpay']['isOpen']) {
                return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['wxpay']['tips']]);
            } else if ($converPayType == 4 && !$userPayConfig['bankpay']['isOpen']) {
                return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['bankpay']['tips']]);
            }
            //检测用户是否有相应支付接口权限
        }

        $clientIpv4 = getClientIp();

        $tradeNo = date('YmdHis') . rand(11111, 99999);

        $tradeNoData = Db::table('epay_order')->where('tradeNo=:tradeNo', ['tradeNo' => $tradeNo])->limit(1)->field('id')->select();
        if (!empty($tradeNoData))
            $tradeNo = date('YmdHis') . rand(11111, 99999);
        //防止单号重复
        $tradeNoOutData = Db::table('epay_order')->where([
            'tradeNoOut' => $tradeNoOut,
            'uid'        => $uid
        ])->limit(1)->field('tradeNo,type')->select();

        if (empty($tradeNoOutData)) {
            $notifyUrl = str_replace('%20', '', $notifyUrl);
            $returnUrl = str_replace('%20', '', $returnUrl);
            //remove empty str
            $orderDiscountMoney = PayModel::getOrderDiscountMoney($uid, $money);
            //减免订单金额
            $discountMoneyAfter = $money - $orderDiscountMoney;
            if ($discountMoneyAfter >= 0)
                $money = $discountMoneyAfter;
            //如果减免后金额小于或等于0则不进行减免操作

            $orderCreateTime = getDateTime();
            $result          = Db::table('epay_order')->insert([
                'uid'         => $uid,
                'tradeNo'     => $tradeNo,
                'tradeNoOut'  => $tradeNoOut,
                'notify_url'  => $notifyUrl,
                'return_url'  => $returnUrl,
                'money'       => $money,
                'type'        => $converPayType,
                'productName' => $productName,
                'ipv4'        => $clientIpv4,
                'status'      => 0,
                'createTime'  => $orderCreateTime
            ]);
            if (!$result)
                return $this->fetch('/SystemMessage', ['msg' => '创建订单失败,请重试']);

            if ($orderDiscountMoney != 0)
                Db::table('epay_order_attr')->insert([
                    'tradeNo'    => $tradeNo,
                    'attrKey'    => 'discountMoney',
                    'attrValue'  => $orderDiscountMoney,
                    'createTime' => $orderCreateTime
                ]);
            //创建订单减免记录 如果减免金额为 0 则不创建
        } else {
            $tradeNo = $tradeNoOutData[0]['tradeNo'];
            if ($tradeNoOutData[0]['type'] != $converPayType)
                return $this->fetch('/SystemMessage', ['msg' => '支付方式改变,请重新下单！']);
//                Db::table('epay_order')->where('tradeNo=:tradeNo', ['tradeNo' => $tradeNo])->limit(1)->update([
//                    'type' => $converPayType
//                ]);
            //改变支付类型，注意这里可能存在问题，如果这个改变订单支付类型并且金额更新大于原先输入的金额数量
        }
        //解决用户交易号重复问题

        if (!empty($userPayConfig)) {
            if ($type == 'tenpay')
                $type = 'qqpay';
            if ($userPayConfig[$type]['apiType'] == 1)
                return redirect(url('/Pay/CenterPay/Submit?tradeNo=' . $tradeNo, '', false, true));
        } else {
            if (isset($this->systemConfig[$type]['apiType']))
                if ($this->systemConfig[$type]['apiType'] == 1)
                    return redirect(url('/Pay/CenterPay/Submit?tradeNo=' . $tradeNo, '', false, true));
        }

        //中央支付
        if ($converPayType == 3) {
            return redirect(url('/Pay/Alipay/Submit?tradeNo=' . $tradeNo, '', false, true));
            //转跳到支付宝支付
        } else if ($converPayType == 1) {
            if (!empty($this->systemConfig['notifyDomain']))
                return redirect($this->systemConfig['notifyDomain'] . '/Pay/WxPay/Submit?tradeNo=' . $tradeNo . '&siteName=' . $siteName);
            return redirect(url('/Pay/WxPay/Submit?tradeNo=' . $tradeNo . '&siteName=' . $siteName, '', false, true));
            //转跳到微信支付
        } else if ($converPayType == 2) {
            return redirect(url('/Pay/QQPay/Submit?tradeNo=' . $tradeNo . '&siteName=' . $siteName, '', false, true));
            //转跳到财付通支付
        } else if ($converPayType == 4) {
            return redirect(url('/Pay/BankPay/Submit?tradeNo=' . $tradeNo . '&siteName=' . $siteName, '', false, true));
            //银联支付
        }
        return $this->fetch('/SystemMessage', ['msg' => '支付类型有误请重试！']);
    }

    /**
     * 查询订单状态
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function OrderStatus()
    {
        $tradeNo = input('get.tradeNo/s');
        //获取订单ID
        $type = input('get.type/d');
        //获取订单类型
        if (empty($tradeNo))
            return json(['status' => 0, 'msg' => '未付款']);
        if (empty($type))
            return json(['status' => 0, 'msg' => '未付款']);

        $isMobile = $this->request->isMobile();

        $result = Db::table('epay_order')->field('status,uid')->limit(1)->where('tradeNo=:tradeNo', ['tradeNo' => $tradeNo])->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '未付款']);

        if ($isMobile && $type == 1) {
            $isOpenCancelReturn = getPayUserAttr($result[0]['uid'], 'isCancelReturn');
            $isOpenCancelReturn = $isOpenCancelReturn == 'true';
            if ($isOpenCancelReturn) {
                $returnData = [
                    'status' => 1,
                    'msg'    => $result[0]['status'] ? '已付款' : '未付款',
                    'url'    => buildCallBackUrl($tradeNo, 'return')
                ];
                return json($returnData);
            }
        }

        $returnData = ['status' => $result[0]['status'], 'msg' => $result[0]['status'] ? '已付款' : '未付款'];
        if ($result[0]['status'])
            $returnData['url'] = buildCallBackUrl($tradeNo, 'return');
        return json($returnData);
    }
}