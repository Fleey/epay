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
    private $apiUrl = 'http://vip.115x.cn/submit.php?';

    //api url

    public function loadTemplate()
    {
        $config = getConfig();
        return $this->fetch('/TestTemplate', ['webName' => $config['webName']]);
    }

    public function pay()
    {
        $getData = input('post.');

        $notify_url = 'http://vip.115x.cn/usk/notify_url.php';
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        $return_url = 'http://vip.115x.cn/epay_return.php';
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
        $html               = '<form id="alipaysubmit" name="alipaysubmit" action="' . $this->apiUrl . '_input_charset=utf-8" method="post">';
        foreach ($param as $key => $value) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
        }
        $html .= '<input type="submit" value=""/></form>';
        $html .= '<script>document.forms["alipaysubmit"].submit();</script>';

        return $html;
    }

}
