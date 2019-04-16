<?php

namespace app\pay\controller;

use app\pay\model\PayModel;
use app\pay\model\QQPayModel;
use think\App;
use think\Controller;
use think\Db;

class QQPay extends Controller
{
    private $systemConfig;
    private $qqPayConfig;
    private $notifyUrl;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->systemConfig = getConfig();
        $this->qqPayConfig  = $this->systemConfig['qqpay'];
        if (empty($this->systemConfig['notifyDomain'])) {
            $this->notifyUrl = url('/Pay/QQPay/Notify', '', false, true);
        } else {
            $this->notifyUrl = $this->systemConfig['notifyDomain'] . '/Pay/QQPay/Notify';
        }
    }

    /**
     * @return mixed|\think\response\Redirect
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubmit()
    {
        $tradeNo  = input('get.tradeNo');
        $siteName = htmlentities(base64_decode(input('get.siteName')));
        if (empty($siteName))
            $siteName = '易支付';
        if (empty($tradeNo))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID有误！']);
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('uid,money,productName,status,type,createTime')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID无效！']);
        if ($result[0]['type'] != 2)
            return $this->fetch('/SystemMessage', ['msg' => '支付方式有误！']);
        if ($result[0]['status'])
            return $this->fetch('/SystemMessage', ['msg' => '交易已经完成无法再次支付！']);

        $apiType       = 0;
        $userPayConfig = unserialize(getPayUserAttr($result[0]['uid'], 'payConfig'));
        if (!empty($userPayConfig)) {
            $apiType = $userPayConfig['qqpay']['apiType'];
        } else {
            if (isset($this->qqPayConfig['apiType']))
                $apiType = $this->qqPayConfig['apiType'];
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

        $param         = [
            'out_trade_no'     => $tradeNo,
            'body'             => $productName,
            'fee_type'         => 'CNY',
            'notify_url'       => $this->notifyUrl,
            'spbill_create_ip' => getClientIp(),
            'total_fee'        => $result[0]['money'],
            'trade_type'       => 'NATIVE'
        ];
        $QQPayModel    = new QQPayModel($this->qqPayConfig);
        $requestResult = $QQPayModel->sendPayRequest($param);
        $codeUrl       = '';
        if ($requestResult['return_code'] == 'SUCCESS' && $requestResult['result_code'] == 'SUCCESS')
            $codeUrl = $requestResult['code_url'];
        else
            if (!empty($requestResult['err_code']))
                return $this->fetch('/SystemMessage', ['msg' => 'QQ钱包支付下单失败！<br>[' . $requestResult['err_code'] . ']' . $requestResult['err_code_des']]);
            else
                return $this->fetch('/SystemMessage', ['msg' => 'QQ钱包支付下单失败！<br>[' . $requestResult['return_code'] . ']' . $requestResult['return_msg']]);


        if ($this->request->isMobile())
            $codeUrl = 'https://myun.tenpay.com/mqq/pay/qrcode.html?_wv=1027&_bid=2183&t=' . $requestResult['prepay_id'];

        if (strpos($this->request->header('user-agent'), 'QQ/') !== false)
            return redirect($codeUrl, [], 302);
        //判断是否手机QQ
        return $this->fetch('/QQPay' . ($this->request->isMobile() ? 'Mobile' : 'Pc') . 'Template', [
            'siteName'    => $siteName,
            'productName' => $result[0]['productName'],
            'money'       => $result[0]['money'] / 100,
            'tradeNo'     => $tradeNo,
            'addTime'     => $result[0]['createTime'],
            'codeUrl'     => $codeUrl
        ]);
    }

    /**
     * @return \think\response\Xml
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function postNotify()
    {
        $requestData = file_get_contents('php://input');
        //get post xml
        $requestData = xmlToArray($requestData);
        //数据转换
        $sign       = $requestData['sign'];
        $QQPayModel = new QQPayModel($this->qqPayConfig);
        if ($sign != $QQPayModel->signParam($requestData))
            return xml(['return_code' => 'FAIL', 'return_msg' => '签名失败']);
        //sign error

        $tradeNoOut = $requestData['out_trade_no'];
        //商户订单号
        $transactionID = $requestData['transaction_id'];
        //QQ钱包订单号
        $totalMoney = $requestData['total_fee'];
        //金额,以分为单位
        $moneyType = $requestData['fee_type'];
        //币种

        $result = Db::table('epay_order')->where('tradeNo', $tradeNoOut)->field('status')->limit(1)->select();
        if (empty($result))
            return xml(['return_code' => 'FAIL', 'return_msg' => '订单无效']);
        if ($result[0]['status'])
            return xml(['return_code' => 'SUCCESS']);
        //订单已经付款成功

        if ($requestData['trade_state'] == 'SUCCESS') {
            Db::table('epay_order')->where('tradeNo', $tradeNoOut)->limit(1)->update([
                'status'  => 1,
                'endTime' => getDateTime()
            ]);
            //更新订单状态
            processOrder($tradeNoOut);
            //统一处理订单
        }
        return xml(['return_code' => 'SUCCESS']);
    }
}
