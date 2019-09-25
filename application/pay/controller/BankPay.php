<?php

namespace app\pay\controller;

use think\App;
use think\Controller;
use think\Db;

class BankPay extends Controller
{
    private $systemConfig;
    private $bankConfig;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->systemConfig = getConfig();
        $this->bankConfig   = $this->systemConfig['bankpay'];
    }

    public function getSubmit()
    {
        $tradeNo = input('get.tradeNo');
        $sign     = input('get.sign/s');
        if(md5($tradeNo.'huaji')!=$sign)
            return $this->fetch('/SystemMessage', ['msg' => '签名有误！']);
        if (empty($tradeNo))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID有误！']);
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('uid,money,productName,status,type')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID无效！']);
        if ($result[0]['type'] != 3)
            return $this->fetch('/SystemMessage', ['msg' => '支付方式有误！']);
        if ($result[0]['status'])
            return $this->fetch('/SystemMessage', ['msg' => '交易已经完成无法再次支付！']);

        $userPayConfig = unserialize(getPayUserAttr($result[0]['uid'], 'payConfig'));
        if (!empty($userPayConfig)) {
            $apiType = $userPayConfig['bankpay']['apiType'];
        } else {
            $apiType = $this->bankConfig['apiType'];
        }

        if ($apiType == 1)
            return $this->fetch('/SystemMessage', ['msg' => '该订单尚不支持原生支付！']);

        return $this->fetch('/SystemMessage', ['msg' => '很抱歉。。。原生支付还没写！']);
    }
}