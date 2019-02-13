<?php

namespace app\pay\model;

class AliPayModel
{
    private $httpVerifyUrl = 'http://mapi.alipay.com/gateway.do?service=notify_verify&';
    private $aliPayGateway = 'https://mapi.alipay.com/gateway.do?';
    private $aliPayConfig;

    public function __construct(array $config)
    {
        $this->aliPayConfig = $config;
    }

    /**
     * 即时转账函数 已经包装
     * @param string $tradeNo //转账单号自行创建 最长64只支持半角英文、数字，及“-”、“_”。
     * @param string $account //转账方支付宝登录号 支持邮箱和手机号格式
     * @param string $toRealName //转账方真实姓名 为了避免错误填写流失资金
     * @param string $money //转账资金必须大小0.1 支持两位小数
     * @param string $ramrke //转账备注
     * @return bool
     */
    public function toAccountTransfer(string $tradeNo, string $account, string $toRealName, string $money, string $ramrke = '即时转账')
    {
        $requestData         = [
            'app_id'      => $this->aliPayConfig['transferPartner'],
            'method'      => 'alipay.fund.trans.toaccount.transfer',
            'charset'     => 'utf-8',
            'version'     => '1.0',
            'sign_type'   => 'RSA2',
            'timestamp'   => getDateTime(),
            'biz_content' => json_encode([
                'out_biz_no'      => $tradeNo,
                'payee_type'      => 'ALIPAY_LOGONID',
                'payee_account'   => $account,
                'amount'          => $money,
                'payee_real_name' => $toRealName,
                'remark'          => $ramrke
            ])
        ];
        $sign                = $this->buildSignRSA2($requestData);
        $requestData['sign'] = $sign;

        $requestUrl    = createLinkStringUrlEncode($requestData);
        $requestResult = json_decode(curl($this->aliPayGateway . $requestUrl),true);

        if (empty($requestResult['alipay_fund_trans_toaccount_transfer_response']))
            return false;

        $requestResult = $requestResult['alipay_fund_trans_toaccount_transfer_response'];
        $isSuccess     = $requestResult['code'] == '10000';
        if ($isSuccess) {
            trace('支付宝即时转账成功 金额 =>' . $money . ' 转账账号 =>' . $account . ' 转账名字 =>' . $toRealName, 'info');
        } else {
            trace('支付宝即时转账失败 失败原因 =>' . $requestResult['msg'] . ' 请求内容 =>' . json_encode($requestResult), 'info');
        }
        return $isSuccess;
    }

    public function buildRequestForm(array $param, string $requestType, string $tips)
    {
        $sign = $this->buildSignMD5($param);

        $param['sign']      = $sign;
        $param['sign_type'] = 'MD5';
        $html               = '<form id="alipaysubmit" name="alipaysubmit"  accept-charset=\'utf-8\' action="' . $this->aliPayGateway . '" method="' . $requestType . '">';
        foreach ($param as $key => $value) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
        }
        $html .= '<input type="submit" value="' . $tips . '"/></form>';
        $html .= '<script>document.forms["alipaysubmit"].submit();</script>';

        return $html;
    }

    public function buildSignRSA2(array $param)
    {
        if (empty($this->aliPayConfig['transferPrivateKey']))
            return 'private key is empty';
        $privateKey = $this->aliPayConfig['transferPrivateKey'];
        $privateKey = chunk_split($privateKey, 64, "\n");
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";
        //标准结构化秘钥
        unset($param['sign']);
        $param['sign_type'] = 'RSA2';
        //设置签名类型
        $param    = argSort($param);
        $paramUrl = createLinkString($param);
        //结构化请求参数
        openssl_sign($paramUrl, $sign, $privateKey, 'SHA256');
        //签名数据
        return base64_encode($sign);
    }

    public function buildSignMD5(array $param)
    {
        $param = paraFilter($param);
        //param filter
        $param = argSort($param);
        //param sort
        $sign = signMD5(createLinkString($param), $this->aliPayConfig['key']);

        return $sign;
    }

    public function verifyData(string $type)
    {
        $type = strtolower($type);
        if ($type != 'get' && $type != 'post')
            return false;
        $getData = input($type . '.');
        if (empty($getData))
            return false;
        if (!$this->verifySign($getData, $getData['sign']))
            return false;
        //验证签名
        if (!empty($getData['notify_id'])) {
            $responseTxt = $this->getResponse($getData['notify_id']);
            if ($responseTxt != 'true')
                return false;
        }
        //请求服务器验证数据
        return true;
    }

    public function verifySign($paraTemp, $sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $paraFilter = paraFilter($paraTemp);

        //对待签名参数数组排序
        $para_sort = argSort($paraFilter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $preStr = createLinkString($para_sort);
        return verifyMD5($preStr, $this->aliPayConfig['key'], $sign);
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id //通知校验ID
     * @return bool //服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    public function getResponse($notify_id)
    {
        $partner     = trim($this->aliPayConfig['partner']);
        $verifyUrl   = $this->httpVerifyUrl . 'partner=' . $partner . '&notify_id=' . $notify_id;
        $responseTxt = curl($verifyUrl);
        return $responseTxt;
    }
}