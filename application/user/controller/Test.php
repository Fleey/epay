<?php

namespace app\user\controller;

use think\Controller;

class Test extends Controller
{
    private $partner = '1000';
    //商户ID
    private $key = 'QOwfoFwMg8dMDM5CDqmkwFBHHcW3hF3C';
    //商户Key

    private $signType = 'md5';

    //api url

    public function loadTemplate()
    {
        $config = getConfig();
        return $this->fetch('/TestTemplate', ['webName' => $config['webName']]);
    }

    public function pay()
    {
        $getData = input('post.');

        $notify_url = url('/test/notify', '', false, true);
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        $return_url = url('/test/return', '', false, true);
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/  页面跳转同步通知页面路径
        $out_trade_no = $getData['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填
        $type = $getData['type'];
        //支付方式
        $name = $getData['WIDsubject'];
        //商品名称
        $money = $getData['WIDtotal_fee'];
        //付款金额
        $sitename = '小老弟支付测试站点';
        //站点名称

        $param = [
            'pid'          => $this->partner,
            'type'         => $type,
            'notify_url'   => $notify_url,
            'return_url'   => $return_url,
            'out_trade_no' => $out_trade_no,
            'name'         => $name,
            'money'        => $money,
            'sitename'     => $sitename
        ];
        return $this->buildRequestForm($param);
    }

    public function getNotify()
    {
        $getData = input('get.');
        $pid     = $getData['pid'];
        //商户号
        $tradeNo = $getData['trade_no'];
        //小老弟订单号
        $tradeNoOut = $getData['out_trade_no'];
        //	商户系统内部的订单号
        $payType = $getData['type'];
        //alipay:支付宝,tenpay:财付通,
        //qqpay:QQ钱包,wxpay:微信支付,
        //alipaycode:支付宝扫码,jdpay:京东支付
        $productName = $getData['name'];
        //商品名称
        $money    = $getData['money'];
        $status   = $getData['trade_status'];
        $sign     = $getData['sign'];
        $signType = $getData['sign_type'];

        if ($signType != 'MD5')
            return $this->fetch('/SystemMessage', ['msg' => '验证签名算法不支持！']);
        if ($this->buildSign($getData) != $getData['sign'])
            return $this->fetch('/SystemMessage', ['msg' => '签名效验不正确！']);

        //下面做你想做的事情

        return '<h1 style="text-align: center;">您已经成功支付</h1>';
    }

    public function getReturn()
    {
        $getData = input('get.');
        $pid     = $getData['pid'];
        //商户号
        $tradeNo = $getData['trade_no'];
        //小老弟订单号
        $tradeNoOut = $getData['out_trade_no'];
        //	商户系统内部的订单号
        $payType = $getData['type'];
        //alipay:支付宝,tenpay:财付通,
        //qqpay:QQ钱包,wxpay:微信支付,
        //alipaycode:支付宝扫码,jdpay:京东支付
        $productName = $getData['name'];
        //商品名称
        $money    = $getData['money'];
        $status   = $getData['trade_status'];
        $sign     = $getData['sign'];
        $signType = $getData['sign_type'];

        if ($signType != 'MD5')
            return $this->fetch('/SystemMessage', ['msg' => '验证签名算法不支持！']);
        if ($this->buildSign($getData) != $getData['sign'])
            return $this->fetch('/SystemMessage', ['msg' => '签名效验不正确！']);

        //下面做你想做的事情

        return '<h1 style="text-align: center;">您已经成功支付</h1>';
    }

    private function buildSign(array $param)
    {
        $param = paraFilter($param);
        //param filter
        $param = argSort($param);
        //param sort
        $sign = signMD5(createLinkstring($param), $this->key);

        return $sign;
    }


    private function buildRequestForm(array $param)
    {
        $sign = $this->buildSign($param);

        $param['sign']      = $sign;
        $param['sign_type'] = $this->signType;
        $html               = '<form id="alipaysubmit" name="alipaysubmit" action="' . url('/submit.php?', '', false, true) . '_input_charset=utf-8" method="post">';
        foreach ($param as $key => $value) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
        }
        $html .= '<input type="submit" value=""/></form>';
        $html .= '<script>document.forms["alipaysubmit"].submit();</script>';

        return $html;
    }

}
