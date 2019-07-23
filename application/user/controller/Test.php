<?php

namespace app\user\controller;

use think\App;
use think\Controller;
use think\Db;

class Test extends Controller
{
    private $partner;
    //商户ID
    private $key;
    //商户Key

    private $signType = 'md5';

    //api url

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $result = Db::table('epay_user')->order('id')->field('id,key')->cache(60)->limit(1)->select();
        if (empty($result))
            exit('尚未查到商户号，需要联系管理员新增测试账号。');
        $this->key     = $result[0]['key'];
        $this->partner = $result[0]['id'];
    }

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

        if (empty($getData['WIDout_trade_no']) || empty($getData['WIDsubject']) || empty($getData['WIDtotal_fee']))
            return '<h1 style="text-align: center;padding-top: 6rem;">页面数据异常，请返回原页面再发起。</h1>';

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

        $subMchID = input('post.subMchID/d', 0);

        $param = [
            'pid'          => $this->partner,
            'type'         => $type,
            'notify_url'   => $notify_url,
            'return_url'   => $return_url,
            'out_trade_no' => $out_trade_no,
            'name'         => $name,
            'money'        => $money,
            'sitename'     => $sitename,
            'subMchID'     => $subMchID
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
//        $pid     = $getData['pid'];
//        //商户号
//        $tradeNo = $getData['trade_no'];
//        //小老弟订单号
//        $tradeNoOut = $getData['out_trade_no'];
//        //	商户系统内部的订单号
//        $payType = $getData['type'];
//        //alipay:支付宝,tenpay:财付通,
//        //qqpay:QQ钱包,wxpay:微信支付,
//        //alipaycode:支付宝扫码,jdpay:京东支付
//        $productName = $getData['name'];
//        //商品名称
//        $money    = $getData['money'];
//        $status   = $getData['trade_status'];
//        $sign     = $getData['sign'];

        if (empty($getData['sign']) || empty($getData['sign_type'])) {
            trace('这个小屁孩在搞事', 'INFO');
            return $this->fetch('/SystemMessage', ['msg' => '告诉你个秘密，我留个cy2018在header哪里，自己去挖掘吧！']);
        }
        $signType = $getData['sign_type'];

        if ($signType != 'MD5')
            return $this->fetch('/SystemMessage', ['msg' => '验证签名算法不支持！']);
        if ($this->buildSign($getData) != $getData['sign'])
            return $this->fetch('/SystemMessage', ['msg' => '签名效验不正确！']);

        //下面做你想做的事情
        if ($getData['trade_status'] != 'TRADE_SUCCESS')
            return '<h1 style="text-align: center;margin-top: 6rem;">您已经取消支付，这个是取消支付页面</h1>';

        return '<h1 style="text-align: center;margin-top: 6rem;">您已经成功支付</h1>';
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
