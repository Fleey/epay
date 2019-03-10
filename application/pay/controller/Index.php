<?php

namespace app\pay\controller;

use think\App;
use think\Controller;
use think\Db;

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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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

        $param = createLinkString(argSort(paraFilter($this->getData)));
        //排序并创建联系参数
        $uid = intval($this->getData['pid']);
        //get uid
        if (empty($uid))
            return $this->fetch('/SystemMessage', ['msg' => 'PID不存在！']);

        $userData = Db::table('epay_user')->limit(1)->field('key,isBan')->where('id', $uid)->select();
        if (empty($userData))
            return $this->fetch('/SystemMessage', ['msg' => '签名校验失败，请返回重试！']);
        if (!verifyMD5($param, $userData[0]['key'], $this->getData['sign']))
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

        {
            $badWordList = str_replace('，', ',', $this->systemConfig['goodsFilter']['keyWord']);
            $badWordList = str_replace('、', ',', $badWordList);
            $badWordList = explode(',', $badWordList);
            if (!empty($badWordList)) {
                $blackReg = '/' . implode('|', $badWordList) . '/i';
                if (preg_match($blackReg, $productName, $matches))
                    return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['goodsFilter']['tips']]);
            }
        }
        //检测违禁词

        if ($money <= 0)
            return $this->fetch('/SystemMessage', ['msg' => '金额(money)格式有误']);

        $maxPayMoney = getPayUserAttr($uid, 'maxPayMoney');
        if (!empty($maxPayMoney)) {
            $maxPayMoney = decimalsToInt($maxPayMoney, 2);
            if (!empty($maxPayMoney)) {
                if ($money > $maxPayMoney)
                    return $this->fetch('/SystemMessage', ['msg' => '[10001]超出商户单个订单最大支付金额']);
            }
        } else {
            if ($money > $this->systemConfig['defaultMaxPayMoney'])
                return $this->fetch('/SystemMessage', ['msg' => '[10001]超出商户单个订单最大支付金额']);
        }
        //check user max pay money

        if (!preg_match('/^[a-zA-Z0-9.\_\-|]{1,64}+$/', $tradeNoOut))
            return $this->fetch('/SystemMessage', ['msg' => '订单号(out_trade_no)格式不正确 最小订单号位一位 最大为64位']);

        $goodsFilterKeyList = explode(',', $this->systemConfig['goodsFilter']['keyWord']);
        foreach ($goodsFilterKeyList as $value) {
            if (!(strpos($productName, $value) === FALSE))
                return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['goodsFilter']['tips']]);
        }
        //check goods filter key

        $converPayType = $this->converPayName($type);

        if ($converPayType == 3 && !$this->systemConfig['alipay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['alipay']['tips']]);
        } else if ($converPayType == 2 && !$this->systemConfig['qqpay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['qqpay']['tips']]);
        } else if ($converPayType == 1 && !$this->systemConfig['wxpay']['isOpen']) {
            return $this->fetch('/SystemMessage', ['msg' => $this->systemConfig['wxpay']['tips']]);
        }
        //check is open pay

        $clientIpv4 = getClientIp();

        $tradeNo = date('YmdHis') . rand(11111, 99999);

        $tradeNoData = Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->field('id')->select();
        if (!empty($tradeNoData))
            $tradeNo = date('YmdHis') . rand(11111, 99999);
        //防止单号重复
        $tradeNoOutData = Db::table('epay_order')->where([
            'tradeNoOut' => $tradeNoOut,
            'uid'        => $uid
        ])->limit(1)->field('tradeNo')->select();
        if (empty($tradeNoOutData)) {
            $notifyUrl = str_replace('%20', '', $notifyUrl);
            $returnUrl = str_replace('%20', '', $returnUrl);
            //remove empty str
            $result = Db::table('epay_order')->insert([
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
                'createTime'  => getDateTime()
            ]);
            if (!$result)
                return $this->fetch('/SystemMessage', ['msg' => '创建订单失败,请重试']);
        } else {
            $tradeNo = $tradeNoData[0]['tradeNo'];
        }
        //解决用户交易号重复问题

        if ($converPayType == 3) {
            return redirect(url('/Pay/Alipay/Submit?tradeNo=' . $tradeNo, '', false, true));
            //转跳到支付宝支付
        } else if ($converPayType == 1) {
            return redirect(url('/Pay/WxPay/Submit?tradeNo=' . $tradeNo . '&siteName=' . $siteName, '', false, true));
            //转跳到微信支付
        } else if ($converPayType == 2) {
            return redirect(url('/Pay/QQPay/Submit?tradeNo=' . $tradeNo . '&siteName=' . $siteName, '', false, true));
            //转跳到财付通支付
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
        $result = Db::table('epay_order')->field('status')->limit(1)->where('tradeNo', $tradeNo)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '未付款']);
        $returnData = ['status' => $result[0]['status'], 'msg' => $result[0]['status'] ? '已付款' : '未付款'];
        if ($result[0]['status'])
            $returnData['url'] = buildCallBackUrl($tradeNo, 'return');
        return json($returnData);
    }

    /**
     * 转换支付名称 主要为了兼容老接口 和 优化数据库
     * @param $payName
     * @return int
     */
    private function converPayName($payName)
    {
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
        return $payName;
    }
}