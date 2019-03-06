<?php

namespace app\pay\controller;

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
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('money,productName,status,type,createTime')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID无效！']);
        if ($result[0]['type'] != 2)
            return $this->fetch('/SystemMessage', ['msg' => '支付方式有误！']);
        if ($result[0]['status'])
            return $this->fetch('/SystemMessage', ['msg' => '交易已经完成无法再次支付！']);

        $param         = [
            'out_trade_no'     => $tradeNo,
            'body'             => $result[0]['productName'],
            'fee_type'         => 'CNY',
            'notify_url'       => url('/Pay/QQPay/Notify', '', false, true),
            'spbill_create_ip' => request()->ip(),
            'total_fee'        => $result[0]['money'],
            'trade_type'       => 'NATIVE'
        ];
        $QQPayModel    = new QQPayModel($this->qqPayConfig);
        $requestResult = $QQPayModel->sendPayRequest($param);
        $codeUrl       = '';
        if ($requestResult['return_code'] == 'SUCCESS' && $requestResult['result_code'] == 'SUCCESS')
            $codeUrl = $requestResult['code_url'];
        else
            return $this->fetch('/SystemMessage', ['msg' => 'QQ钱包支付下单失败！[' . $requestResult['err_code'] . ']' . $requestResult['err_code_des']]);
        if (strpos($this->request->header('HTTP_USER_AGENT', ''), 'QQ/') !== false)
            return redirect($codeUrl, [], 302);
        //判断是否手机QQ
        return $this->fetch('/QQPayTemplate', [
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
