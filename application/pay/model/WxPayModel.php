<?php

namespace app\pay\model;

use think\Exception;

class WxPayModel
{
    private $wxConfig;
    private $signType = 'MD5';
    private $appID    = '';
    private $mchID    = '';
    private $key      = '';


    /**
     * WxPayModel constructor.
     * @param array $wxConfig
     * @param string $payType //h5 仅H5 jsapi 仅jsapi
     * @throws Exception
     */
    public function __construct(array $wxConfig, $payType = 'h5')
    {
        $this->wxConfig = $wxConfig;
        if ($payType == 'h5') {
            $this->appID = $wxConfig['appid'];
            $this->mchID = $wxConfig['mchid'];
            $this->key   = $wxConfig['key'];
        } else if ($payType == 'jsapi') {
            $this->appID = $wxConfig['jsApiAppid'];
            $this->mchID = $wxConfig['jsApiMchid'];
            $this->key   = $wxConfig['jsApiKey'];
        } else {
            trace('参数异常没填');
            throw new Exception('您丢的参数有误');
        }

        if (empty($this->appID) || empty($this->mchID) || empty($this->key))
            throw new Exception('您丢的参数有误，1001');
        //check data
    }

    /**
     * 获取微信OpenCode
     * @param $returnUrl
     */
    public function getWxOpenCode($returnUrl)
    {
        $baseUrl = urlencode($returnUrl);
        //获取当前请求连接与参数
        $requestData = [
            'appid'         => $this->wxConfig['jsApiAppid'],
            'redirect_uri'  => $baseUrl,
            'response_type' => 'code',
            'scope'         => 'snsapi_base',
            'state'         => 'STATE'
        ];
        $requestUrl  = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . $this->buildUrlParam($requestData) . '#wechat_redirect';
        Header('Location: ' . $requestUrl);
        exit();
    }

    /**
     * 获取微信OpenID
     * @param $code
     * @return string
     */
    public function getWxOpenid($code)
    {
        $requestData   = [
            'appid'      => $this->wxConfig['jsApiAppid'],
            'secret'     => $this->wxConfig['jsApiAppSecret'],
            'code'       => $code,
            'grant_type' => 'authorization_code'
        ];
        $requestResult = json_decode(curl('https://api.weixin.qq.com/sns/oauth2/access_token', [], 'get', $requestData), true);
        if (empty($requestResult['openid']))
            return 'request fail';
        return $requestResult['openid'];
    }

    /**
     * 微信订单退款接口
     * @param string $tradeNo
     * @param int $totalMoney //订单总金额 单位分
     * @param int $refundMoney //退款金额  单位分
     * @param array $sslData //sslCertPath and sslKeyPath
     * @param string $notifyUrl //申请退款回调地址
     * @return array //成功返回 [true] 失败[false,(String)失败原因]
     */
    public function orderRefund(string $tradeNo, int $totalMoney, int $refundMoney, array $sslData, string $notifyUrl = '')
    {
        $requestUrl  = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $requestData = [
            'appid'         => $this->appID,
            'mch_id'        => $this->mchID,
            'nonce_str'     => getRandChar(32),
            'out_trade_no'  => $tradeNo,
            'out_refund_no' => md5(uniqid()),
            'total_fee'     => $totalMoney,
            'refund_fee'    => $refundMoney,
        ];
        if (!empty($this->wxConfig['sub_mch_id']))
            $requestData['sub_mch_id'] = $this->wxConfig['sub_mch_id'];
        if (!empty($notifyUrl)) {
            if (strlen($notifyUrl) > 256)
                return [false, '回调地址长度不能超过 256 个字符'];
            $requestData['notify_url'] = $notifyUrl;
        }
        $requestData['sign_type'] = $this->signType;
        $requestData['sign']      = $this->signParam($requestData);

        $xml = arrayToXml($requestData);
        //build xml
        $result = curl($requestUrl, [], 'post', $xml, 'xml', false, false, $sslData);
        if ($result === false)
            return [false, '请求目标网关失败,请联系管理员处理'];
        $result = xmlToArray($result);
        if ($result['return_code'] == 'FAIL')
            return [false, $result['return_msg']];
        unset($result['refund_channel']);
        if ($this->signParam($result) != $result['sign'])
            return [false, '签名失败,数据被修改,请联系管理员处理'];
        //检查是否被劫持 验签数据
        if ($result['return_code'] != 'SUCCESS')
            return [false, $result['return_msg']];
        if (!empty($result['err_code'])) {
            if ($result['err_code_des'] == '累计退款金额大于支付金额' || $result['err_code_des'] == 'refund_fee大于可退金额') {
                return $this->orderRefund($tradeNo, $totalMoney, $refundMoney - 1, $sslData,$notifyUrl);
            }
            return [false, '[' . $result['err_code'] . ']' . $result['err_code_des']];
        }
        if ($result['result_code'] != 'SUCCESS')
            return [false, '提交业务失败,请重试'];

        return [true];
    }

    /**
     * 检查订单是否已经支付，注意没有进行订单金额效验
     * @param string $orderID
     * @param string $type
     * @param string $subMid
     * @return bool
     */
    public function checkWxPayStatus(string $orderID, string $type = 'out_trade_no', string $subMid = '')
    {
        $result = $this->selectWxPayRecord($orderID, $type, $subMid);
        if (!$result)
            return false;
        if (empty($result['return_code']) || empty($result['result_code']) || empty($result['trade_state']))
            return false;
        return ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS' && $result['trade_state'] == 'SUCCESS');
    }

    /**
     * 查询微信支付记录
     * @param string $orderID
     * @param string $type
     * @param string $subMid
     * @return array|bool|mixed|object
     */
    public function selectWxPayRecord(string $orderID, string $type = 'out_trade_no', string $subMid = '')
    {
        if ($type != 'out_trade_no' && $type != 'transaction_id')
            return false;
        $requestUrl  = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $requestData = [
            'appid'     => $this->appID,
            'mch_id'    => $this->mchID,
            'nonce_str' => getRandChar(32),
        ];
        if (!empty($this->wxConfig['sub_mch_id']))
            $requestData['sub_mch_id'] = $this->wxConfig['sub_mch_id'];
        if ($type == 'out_trade_no')
            $requestData['out_trade_no'] = $orderID;
        else
            $requestData['transaction_id'] = $orderID;

        $requestData['sign_type'] = $this->signType;
        $requestData['sign']      = $this->signParam($requestData);
        $xml                      = arrayToXml($requestData);
        //build xml
        $result = curl($requestUrl, [], 'post', $xml, 'xml');
        return xmlToArray($result);
    }

    /**
     * 微信PC版本支付
     * @param array $tradeData
     * @param string $type
     * @param string $notifyUrl
     * @param string $openCode
     * @param String $subMid
     * @return array|mixed|object
     */
    public function sendPayRequest(array $tradeData, string $type, string $notifyUrl, string $openCode = '', String $subMid = '')
    {
        $requestUrl  = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $requestData = [
            'appid'            => $this->appID,
            'mch_id'           => $this->mchID,
            'body'             => $tradeData['productName'],
            'out_trade_no'     => $tradeData['tradeNo'],
            'total_fee'        => $tradeData['money'],
            'spbill_create_ip' => getClientIp(),
            'trade_type'       => $type,
            'notify_url'       => $notifyUrl,
            'nonce_str'        => getRandChar(32),
            'product_id'       => md5($tradeData['productName']),
            'time_start'       => date('YmdHis', time()),
            'time_expire'      => date('YmdHis', time() + 360),
            'sign_type'        => $this->signType
        ];
        if (!empty($subMid))
            $requestData['sub_mch_id'] = $subMid;
        //订单失效6分钟
        if ($type == 'JSAPI') {
            $openID                = $this->getWxOpenID($openCode);
            $requestData['openid'] = $openID;
        }


        $requestData['sign'] = $this->signParam($requestData);
        $xml                 = arrayToXml($requestData);
        //build xml
        $result = curl($requestUrl, [], 'post', $xml, 'xml');
        return xmlToArray($result);
    }

    /**
     * 构建签名
     * @param array $param
     * @return string
     */
    public function signParam(array $param)
    {
        ksort($param);
        $stringA = $this->buildUrlParam($param);
        $stringA .= '&key=' . $this->key;
        //排序并组合字符串
        if ($this->signType == 'MD5') {
            $stringA = md5($stringA);
        } else {
            $stringA = hash_hmac('sha256', $stringA, $this->key);
        }
        return strtoupper($stringA);
    }

    /**
     * 构建url请求参数
     * @param array $data
     * @return string
     */
    public function buildUrlParam(array $data)
    {
        $tempBuff = '';
        foreach ($data as $key => $value) {
            if ($key != 'sign')
                $tempBuff .= $key . '=' . $value . '&';
        }
        $tempBuff = trim($tempBuff, '&');
        return $tempBuff;
    }

    /**
     * 构建js api 参数
     * @param array $data
     * @return false|string
     */
    public function buildJsApiParam(array $data)
    {
        if (!isset($data['appid']) || empty($data['prepay_id']))
            return 'param error';

        $initTime         = time();
        $param            = [
            'appId'     => $data['appid'],
            'timeStamp' => (string)$initTime,
            'nonceStr'  => getRandChar(32),
            'package'   => 'prepay_id=' . $data['prepay_id'],
            'signType'  => $this->signType
        ];
        $param['paySign'] = $this->signParam($param);
        return json_encode($param);
    }

}