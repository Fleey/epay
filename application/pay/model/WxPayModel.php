<?php

namespace app\pay\model;

class WxPayModel
{
    private $wxConfig;

    public function __construct(array $wxConfig)
    {
        $this->wxConfig = $wxConfig;
    }

    public function getWxOpenID()
    {
        $code = input('get.code');
        if (empty($code)) {
            $baseUrl = urlencode(request()->url(true));
            //获取当前请求连接与参数
            $requestData = [
                'appid'         => $this->wxConfig['appid'],
                'redirect_uri'  => $this->$baseUrl,
                'response_type' => 'code',
                'scope'         => 'snsapi_base',
                'state'         => 'STATE#wechat_redirect'
            ];
            $requestUrl  = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . $this->buildUrlParam($requestData);
            Header('Location: ' . $requestUrl);
            exit();
        }
        $requestData   = [
            'appid'      => $this->wxConfig['appid'],
            'secret'     => $this->wxConfig['appSecret'],
            'code'       => $code,
            'grant_type' => 'authorization_code'
        ];
        $requestResult = json_decode(curl('https://api.weixin.qq.com/sns/oauth2/access_token', [], 'get', $requestData));
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
        if ($type == 'JSAPI')
            $requestData['openid'] = $this->getWxOpenID();
        //get open id
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
     * @return array|mixed|object
     */
    public function sendPayRequest(array $tradeData, string $type, string $notifyUrl)
    {
        $productName = '充值不到账联系客服QQ：21101787';
        $requestUrl  = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $requestData = [
            'appid'            => $this->wxConfig['appid'],
            'mch_id'           => $this->wxConfig['mchid'],
            'body'             => $productName,
            'out_trade_no'     => $tradeData['tradeNo'],
            'total_fee'        => $tradeData['money'],
            'spbill_create_ip' => getClientIp(),
            'trade_type'       => $type,
            'notify_url'       => $notifyUrl,
            'nonce_str'        => getRandChar(32)
        ];
        if ($type == 'NATIVE')
            $requestData['product_id'] = '010086';
        if ($type == 'MWEB')
            $requestData['scene_info'] = json_encode(['h5_info' => ['type' => '', 'wap_url' => url('/', '', false, true), 'wap_name' => '余额充值']]);

        $requestData['sign'] = $this->signParam($requestData);

        $xml = arrayToXml($requestData);
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
        $stringA = '';
        foreach ($param as $key => $value) {
            if ($value != '' && $key != 'sign')
                $stringA .= $key . '=' . $value . '&';
        }
        //排序并组合字符串
        $stringA .= 'key=' . $this->wxConfig['key'];
        $stringA = strtoupper(md5($stringA));
        return $stringA;
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
            'signType'  => 'md5'
        ];
        $param['paySign'] = $this->signParam($param);
        return json_encode($param);
    }

}