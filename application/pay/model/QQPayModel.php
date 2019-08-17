<?php

namespace app\pay\model;

use app\admin\model\FileModel;

class QQPayModel
{
    private $qqPayConfig;

    public function __construct(array $qqPayConfig)
    {
        $this->qqPayConfig = $qqPayConfig;
    }

    /**
     * 构建签名
     * @param array $param
     * @return string
     */
    public function signParam(array $param)
    {
        ksort($param);
        $stringA = '';
        foreach ($param as $key => $value) {
            if ($value != '' && $key != 'sign')
                $stringA .= $key . '=' . $value . '&';
        }
        //排序并组合字符串
        $stringA .= 'key=' . $this->qqPayConfig['mchkey'];
        $stringA = strtoupper(md5($stringA));
        return $stringA;
    }

    /**
     * 查询支付支付记录
     * @param $tradeNo
     * @return array|mixed|object
     */
    public function selectPayRecord(string $tradeNo)
    {
        $param         = [
            'mch_id'       => $this->qqPayConfig['mchid'],
            'nonce_str'    => getRandChar(16),
            'out_trade_no' => $tradeNo
        ];
        $param['sign'] = $this->signParam($param);
        $xml           = arrayToXml($param);
        //build xml
        $result = curl('https://qpay.qq.com/cgi-bin/pay/qpay_order_query.cgi', [], 'post', $xml, 'xml');
        return xmlToArray($result);
    }

    /**
     * 获取付款二维码
     * @param array $param
     * @return array|mixed|object
     */
    public function sendPayRequest(array $param)
    {
        $param['mch_id']    = $this->qqPayConfig['mchid'];
        $param['nonce_str'] = getRandChar(16);
        $param['sign']      = $this->signParam($param);
        $xml                = arrayToXml($param);
        //build xml
        $result = curl('https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi', [], 'post', $xml, 'xml');
        return xmlToArray($result);
    }

    /**
     * 申请退款 （这玩意不支持异步通知吊疼）
     * @param string $tradeNo //订单单号
     * @param int $refundMoney //退款金额分单位
     * @return array //[bool,string] 当第一个参数为false时第二个则必定出现错误提示
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderRefund(string $tradeNo, int $refundMoney)
    {
        if (!isset($this->qqPayConfig['certPublic']) && !isset($this->qqPayConfig['certPrivate']))
            return [false, '证书尚未配置无法退款'];
        $param = [
            'mch_id'         => $this->qqPayConfig['mchid'],
            'nonce_str'      => getRandChar(16),
            'out_trade_no'   => $tradeNo,
            'out_refund_no'  => md5(time()),
            'refund_fee'     => $refundMoney,
            'op_user_id'     => $this->qqPayConfig['opUserID'],
            'op_user_passwd' => md5($this->qqPayConfig['opUserPassword'])
        ];

        $param['sign'] = $this->signParam($param);

        $xml    = arrayToXml($param);
        $result = curl('https://api.qpay.qq.com/cgi-bin/pay/qpay_refund.cgi', [], 'post', $xml, 'xml', false, false, [
            'sslCertPath' => FileModel::getFilePath($this->qqPayConfig['certPublic']),
            'sslKeyPath'  => FileModel::getFilePath($this->qqPayConfig['certPrivate'])
        ]);
        if ($result === false)
            return [false, '请求失败，可能证书错误或服务端异常'];

        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return [false, $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (isset($result['err_code'])) {
                if ($result['err_code'] == 'PARAM_ERROR')
                    return json([false, '[' . $result['err_code'] . ']参数请求错误']);
                else if ($result['err_code'] == 'REFUND_MONEY_EXCEEDED')
                    return json([false, '[' . $result['err_code'] . ']退款金额错误']);
                trace('[QQ钱包退款错误] data => ' . json_encode($result), 'warning');
                return json([false, '请联系相关人员 错误 => ' . $result['err_code']]);
            }
        }
        return [true, '申请退款成功'];
    }
}