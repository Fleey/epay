<?php

namespace app\pay\controller;

use app\pay\model\AliPayModel;
use think\App;
use think\Controller;
use think\Db;

class Alipay extends Controller
{
    private $alipayConfig;
    private $systemConfig;
    private $notifyUrl;
    private $returnUrl;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->systemConfig = getConfig();
        $this->alipayConfig = $this->systemConfig['alipay'];
        $this->returnUrl    = url('/Pay/Alipay/Return', '', false, true);
        if (empty($this->systemConfig['notifyDomain'])) {
            $this->notifyUrl = url('/Pay/Alipay/Notify', '', false, true);
        } else {
            $this->notifyUrl = $this->systemConfig['notifyDomain'] . '/Pay/Alipay/Notify';
        }
    }

    /*
     * 发起请求地址
     */
    public function getSubmit()
    {
        $tradeNo = input('get.tradeNo');
        if (empty($tradeNo))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID有误！']);
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('uid,money,productName,status,type')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID无效！']);
        if ($result[0]['type'] != 3)
            return $this->fetch('/SystemMessage', ['msg' => '支付方式有误！']);
        if ($result[0]['status'])
            return $this->fetch('/SystemMessage', ['msg' => '交易已经完成无法再次支付！']);

        $apiType       = 0;
        $userPayConfig = unserialize(getPayUserAttr($result[0]['uid'], 'payConfig'));
        if (!empty($userPayConfig)) {
            $apiType = $userPayConfig['alipay']['apiType'];
        } else {
            if (isset($this->alipayConfig['apiType']))
                $apiType = $this->alipayConfig['apiType'];
        }

        if ($apiType == 1)
            return $this->fetch('/SystemMessage', ['msg' => '该订单尚不支持原生支付！']);

        $productNameShowMode = intval(getPayUserAttr($result[0]['uid'], 'productNameShowMode'));
        $productName         = empty($this->systemConfig['defaultProductName']) ? '这个是默认商品名称' : $this->systemConfig['defaultProductName'];
        if ($productNameShowMode == 1) {
            $tempData    = getPayUserAttr($result[0]['uid'], 'productName');
            $productName = empty($tempData) ? '商户尚未设置默认商品名称' : $tempData;
        } else if ($productNameShowMode == 2) {
            $productName = $result[0]['productName'];
        }

        $isMobile = $this->request->isMobile();
        $param    = [
            'service'        => $isMobile ? 'alipay.wap.create.direct.pay.by.user' : 'create_direct_pay_by_user',
            'partner'        => $this->alipayConfig['partner'],
            'seller_id'      => $this->alipayConfig['partner'],
            'payment_type'   => 1,
            'notify_url'     => $this->notifyUrl,
            'return_url'     => $this->returnUrl,
            'subject'        => $productName,
            'out_trade_no'   => $tradeNo,
            'total_fee'      => $result[0]['money'] / 100,
            '_input_charset' => 'UTF-8'
        ];
        if ($isMobile)
            $param['app_pay'] = 'Y';
        $aliPayModel = new AliPayModel($this->alipayConfig);
        return $aliPayModel->buildRequestForm($param, 'get', '正在跳转');
    }

    /**
     * 同步回调地址
     */
    public function getReturn()
    {
        $aliPayModel = new AliPayModel($this->alipayConfig);

        $getData = input('get.');
        if (empty($getData['out_trade_no']))
            return $this->fetch('/SystemMessage', ['msg' => '支付同步回调异常，请联系网站管理员处理！']);
        if (empty($getData['sign']))
            return $this->fetch('/SystemMessage', ['msg' => '回调签名异常,请联系管理员处理！']);

        $verifyResult = $aliPayModel->verifyData('get');
        if (!$verifyResult)
            return $this->fetch('/SystemMessage', ['msg' => '支付宝返回验证失败！']);

        $tradeNoOut  = $getData['out_trade_no'];
        $tradeStatus = $getData['trade_status'];
        $result      = Db::table('epay_order')->where('tradeNo', $tradeNoOut)->field('status,return_url')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '订单不存在，请联系管理员处理！']);
        //判断订单是否存在
        if ($result[0]['status'])
            return redirect(buildCallBackUrl($tradeNoOut, 'return'));
        //订单状态已经被更新

        if ($tradeStatus == 'TRADE_SUCCESS') {
            Db::table('epay_order')->where('tradeNo', $tradeNoOut)->limit(1)->update([
                'status'  => 1,
                'endTime' => getDateTime()
            ]);
            processOrder($tradeNoOut, true);
        }
        return redirect(buildCallBackUrl($tradeNoOut, 'return'));
    }

    /**
     * 异步回调地址
     */
    public function postNotify()
    {
        $aliPayModel  = new AliPayModel($this->alipayConfig);
        $verifyResult = $aliPayModel->verifyData('post');
        if (!$verifyResult)
            return json(['status' => 0, 'msg' => 'fail']);

        $getData    = input('post.');
        $tradeNoOut = $getData['out_trade_no'];
        //商户订单号
        $tradeNo = $getData['trade_no'];
        //支付宝交易号
        //$type        = $getData['type'];
        $tradeStatus = $getData['trade_status'];
        //交易状态
        $buyerEmail = input('get.buyer_email');
        //买家支付宝

        $result = Db::table('epay_order')->where('tradeNo', $tradeNoOut)->field('status')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => 'fail']);
        //判断订单是否存在
        if ($result[0]['status'])
            return json(['status' => 0, 'msg' => 'fail']);
        //订单状态已经被更新
        if ($tradeStatus == 'TRADE_SUCCESS') {
            Db::table('epay_order')->where('tradeNo', $tradeNoOut)->limit(1)->update([
                'status'  => 1,
                'endTime' => getDateTime()
            ]);
            processOrder($tradeNoOut, true);
        }

        return json(['status' => 1, 'msg' => 'success']);
    }
}