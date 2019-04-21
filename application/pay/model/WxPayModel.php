<?php

namespace app\pay\model;

class WxPayModel
{
    private $wxConfig;
    private $signType = 'MD5';

    public function __construct(array $wxConfig)
    {
        $this->wxConfig = $wxConfig;
    }

    public function getWxOpenCode($returnUrl)
    {
        $baseUrl = urlencode($returnUrl);
        //获取当前请求连接与参数
        $requestData = [
            'appid'         => $this->wxConfig['appid'],
            'redirect_uri'  => $baseUrl,
            'response_type' => 'code',
            'scope'         => 'snsapi_base',
            'state'         => 'STATE'
        ];
        $requestUrl  = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . $this->buildUrlParam($requestData) . '#wechat_redirect';
        Header('Location: ' . $requestUrl);
        exit();
    }

    public function getWxOpenid($code)
    {
        $requestData   = [
            'appid'      => $this->wxConfig['appid'],
            'secret'     => $this->wxConfig['appSecret'],
            'code'       => $code,
            'grant_type' => 'authorization_code'
        ];
        $requestResult = json_decode(curl('https://api.weixin.qq.com/sns/oauth2/access_token', [], 'get', $requestData), true);
        if (empty($requestResult['openid']))
            return 'request fail';
        return $requestResult['openid'];
    }

    /**
     * 检查订单是否已经支付，注意没有进行订单金额效验
     * @param string $orderID
     * @param string $type
     * @return bool
     */
    public function checkWxPayStatus(string $orderID, string $type = 'out_trade_no')
    {
        $result = $this->selectWxPayRecord($orderID, $type);
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
     * @return array|bool|mixed|object
     */
    public function selectWxPayRecord(string $orderID, string $type = 'out_trade_no')
    {
        if ($type != 'out_trade_no' && $type != 'transaction_id')
            return false;
        $requestUrl  = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $requestData = [
            'appid'     => $this->wxConfig['appid'],
            'mch_id'    => $this->wxConfig['mchid'],
            'nonce_str' => getRandChar(32)
        ];
        if ($type == 'out_trade_no')
            $requestData['out_trade_no'] = $orderID;
        else
            $requestData['transaction_id'] = $orderID;

        $requestData['sign'] = $this->signParam($requestData);
        $xml                 = arrayToXml($requestData);
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
     * @return array|mixed|object
     */
    public function sendPayRequest(array $tradeData, string $type, string $notifyUrl, string $openCode = '')
    {
        $requestUrl  = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $requestData = [
            'appid'            => $this->wxConfig['appid'],
            'mch_id'           => $this->wxConfig['mchid'],
            'body'             => '111',
            'out_trade_no'     => $tradeData['tradeNo'],
            'total_fee'        => $tradeData['money'],
            'spbill_create_ip' => getClientIp(),
            'trade_type'       => $type,
            'notify_url'       => $notifyUrl,
            'nonce_str'        => getRandChar(32),
            'product_id'       => md5(time()),
//            'sign_type'        => $this->signType
        ];
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
        $stringA .= '&key=' . $this->wxConfig['key'];
        //排序并组合字符串
//        $stringA = md5($stringA);
        trace($stringA,'info');
        if ($this->signType == 'MD5') {
            $stringA = md5($stringA);
        } else {
            $stringA = hash_hmac('sha256', $stringA, $this->wxConfig['key']);
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
            'timeStamp' => $initTime,
            'nonceStr'  => getRandChar(32),
            'package'   => 'prepay_id=' . $data['prepay_id'],
            'signType'  => $this->signType
        ];
        $param['paySign'] = $this->signParam($param);
        return json_encode($param);
    }

}