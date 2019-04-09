<?php

namespace app\pay\model;

class CenterPayModel
{
    private $centerConfig;

    public function __construct(array $centerConfig)
    {
        $this->centerConfig = $centerConfig;
    }

    public function getPayUrl(string $tradeNo, string $payType, string $money, string $notifyUrl)
    {
        if (empty($tradeNo) || empty($payType) || empty($money) || empty($notifyUrl))
            return ['isSuccess' => false, 'msg' => '[server] param empty'];

        $url    = $this->centerConfig['gateway'] . '/api/v1/PayUrl';
        $param  = [
            'tradeNo'   => $tradeNo,
            'payType'   => $payType,
            'money'     => $money,
            'notifyUrl' => $notifyUrl
        ];
        $result = $this->sendRequest($url, $param);
        if ($result === false)
            return ['isSuccess' => false, 'msg' => '[server] request error'];
        if ($result['status'] != 1 || empty($result['data']))
            return ['isSuccess' => false, 'msg' => $result['msg']];
        $result = json_decode($result['data'], true);
        return ['isSuccess' => true, 'url' => $result['url']];
    }

    /**
     * 查询订单是否支付 True为已经支付 False未支付
     * @param string $tradeNo
     * @return bool
     */
    public function isPay(string $tradeNo)
    {
        if (empty($tradeNo))
            return false;
        $url    = $this->centerConfig['gateway'] . '/api/v1/PayStatus';
        $param  = [
            'tradeNo' => $tradeNo
        ];
        $result = $this->sendRequest($url, $param);
        if ($result === false)
            return false;
        if ($result['status'] == 0 || empty($result['data']))
            return false;
        $result = json_decode($result['data'], true);
        return $result['payStatus'] == 1;
    }


    public function sendRequest(string $url, array $param)
    {
        $requestParam              = [
            'data' => json_encode($param)
        ];
        $requestParam['uid']       = $this->centerConfig['epayCenterUid'];
        $requestParam['sign']      = $this->buildSignMD5($requestParam);
        $requestParam['sign_type'] = 'MD5';
        //build sign
        $requestResult = curl($url, [], 'post', $requestParam);
        if ($requestResult === false)
            return false;
        $requestResult = json_decode($requestResult, true);
        if ($requestResult['status']) {
            if ($requestResult['sign_type'] != 'MD5')
                return false;
            //sign type error 签名类型有误
            $verifySign = $this->buildSignMD5($requestResult);
            if ($verifySign != $requestResult['sign'])
                return false;
            //sign error 签名有误数据异常
        }
        //当请求状态为成功时有必要效验sign
        return $requestResult;
    }


    /**
     * MD5方式验证签名
     * @param array $data
     * @return bool
     */
    public function buildSignMD5(array $data)
    {
        $args = argSort(paraFilter($data, false));
        $sign = signMD5(createLinkString($args), $this->centerConfig['epayCenterKey']);
        return $sign;
    }
}