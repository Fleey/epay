<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 获取随机字符串
 * @param int $length
 * @return null|string
 */
function getRandChar($length = 8)
{
    $str    = null;
    $strPol = "ABCDEFGHIJKMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz";
    $max    = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}


/**
 * 返回当前时间格式 存储数据库专用
 * @return false|string
 */
function getDateTime()
{
    return date('Y-m-d H:i:s', time());
}

/**
 * 全角字符转半角字符
 * @param $str
 * @return null|string|string[]
 */
function sbc2Dbc($str)
{
    $arr = array(
        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
        'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
        'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
        'ｙ' => 'y', 'ｚ' => 'z', '（' => '(', '）' => ')', '〔' => '(', '〕' => ')', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']',
        '“' => '"', '”' => '"', '‘' => '\'', '\'' => '\'', '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%', '＋' => '+',
        '—' => '-', '－' => '-', '～' => '~', '：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => ',', '；' => ';', '？' => '?',
        '！' => '!', '…' => '-', '‖' => '|', '”' => '"', '\'' => '`', '‘' => '`', '｜' => ' | ', '〃' => '"', '　' => ' ', '×' => '*',
        '￣' => '~', '．' => '.', '＊' => '*', '＆' => '&', '＜' => '<', '＞' => '>', '＄' => '$', '＠' => '@', '＾' => '^', '＿' => '_',
        '＂' => '"', '￥' => '$', '＝' => ' = ', '＼' => '\\', '／' => ' / '
    );
    return strtr($str, $arr);
}

/**
 * string 的小数 转int类型
 * @param string $decimals
 * @param int $decimalPlace
 * @return float|int
 */
function decimalsToInt(string $decimals, int $decimalPlace)
{
    $str = explode('.', $decimals);
    if (count($str) == 1) {
        $decimalMultiple = 1;
        for ($i = 1; $i <= $decimalPlace; $i++)
            $decimalMultiple *= 10;
        return intval($str[0]) * $decimalMultiple;
    }
    //保证数位
    if (count($str) != 2)
        return 0;

    $decimalMultiple = 1;
    for ($i = 1; $i <= $decimalPlace; $i++) {
        $decimalMultiple *= 10;
    }
    $decimalsLength = strlen($str[1]);
    $temp1          = intval($str[0]) * $decimalMultiple;
    if ($decimalPlace > $decimalsLength) {
        $temp2           = $decimalPlace - $decimalsLength;
        $decimalMultiple = 1;
        for ($i = 1; $i <= $temp2; $i++) {
            $decimalMultiple *= 10;
        }
        $temp1 += intval($str[1]) * $decimalMultiple;
    }
    if ($decimalPlace == $decimalsLength) {
        $temp1 += intval($str[1]);
    }
    //需求小数位符合
    if ($decimalPlace < $decimalsLength) {
        $str[1] = substr($str[1], 0, $decimalPlace);
        $temp1  += intval($str[1]);
    }
    //需求小数位大于
    return $temp1;
}

/**
 * 整数转小数 string 类型
 * @param $int
 * @param int $decimalPlace
 * @return string
 */
function intToDecimals($int, int $decimalPlace)
{
    $str       = strval($int);
    $strLength = strlen($str);
    $str       = substr($str, 0, $strLength - $decimalPlace) . '.' . substr($str, $strLength - $decimalPlace, $strLength);
    return $str;
}

function is_IntOrDecimal($text)
{
    return preg_match('/^[0-9]+([.]{1}[0-9]+){0,1}$/', $text);
}

function is_mobile($text)
{
    $search = '/^1[34578]{1}\d{9}$/';
    if (preg_match($search, $text)) {
        return true;
    } else {
        return false;
    }
}

function is_email($text)
{
    return filter_var($text, FILTER_VALIDATE_EMAIL) === false ? false : true;
}

function curl($url = '', $addHeaders = [], $requestType = 'get', $requestData = '', $postType = '', $urlencode = true, $isProxy = false, $certData = [])
{
    if (empty($url))
        return '';
    //容错处理
    $headers  = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36'
    ];
    $postType = strtolower($postType);
    if ($requestType == 'get' && is_array($requestData)) {
        $tempBuff = '';
        foreach ($requestData as $key => $value) {
            if ($urlencode)
                $tempBuff .= rawurlencode(rawurlencode($key)) . '=' . rawurlencode(rawurlencode($value)) . '&';
            else
                $tempBuff .= $key . '=' . $value . '&';
        }
        $tempBuff = trim($tempBuff, '&');
        $url      .= '?' . $tempBuff;
    }
    //手动build get请求参数
    if (!empty($addHeaders))
        $headers = array_merge($headers, $addHeaders);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
//    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //设置允许302转跳

//    $isProxy = true;
    if ($isProxy) {
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1'); //代理服务器地址
        curl_setopt($ch, CURLOPT_PROXYPORT, 1080); //代理服务器端口
        //set proxy
    }
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    //gzip

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($certData)) {
        if (empty($certData['sslCertPath']) || empty($certData['sslKeyPath']))
            return '证书路径尚未配置，请求失败';
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $certData['sslCertPath']);
        curl_setopt($ch, CURLOPT_SSLKEY, $certData['sslKeyPath']);
        //这个负责强制校验SSL证书
    }
    //add ssl
    if ($requestType == 'get') {
        curl_setopt($ch, CURLOPT_HEADER, false);
    } else if ($requestType == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($requestType));
    }
    //处理类型
    if ($requestType != 'get') {
        if ($postType != 'form-data') {
            if (is_array($requestData) && !empty($requestData)) {
                $temp = '';
                foreach ($requestData as $key => $value) {
                    if ($urlencode) {
                        $temp .= rawurlencode(rawurlencode($key)) . '=' . rawurlencode(rawurlencode($value)) . '&';
                    } else {
                        $temp .= $key . '=' . $value . '&';
                    }
                }
                $requestData = substr($temp, 0, strlen($temp) - 1);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
    }
    //只要不是get姿势都塞东西给他post
    if ($requestType != 'get') {
        if ($postType == 'json') {
            $headers[]   = 'Content-Type: application/json; charset=utf-8';
            $requestData = is_array($requestData) ? json_encode($requestData) : $requestData;
        } else if ($postType == 'xml') {
            $headers[]   = 'Content-Type:text/xml; charset=utf-8';
            $requestData = is_array($requestData) ? arrayToXml($requestData) : $requestData;
        }
        if ($postType != 'form-data')
            $headers[] = 'Content-Length: ' . strlen($requestData);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * +----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
 * +----------------------------------------------------------
 * @param string $string 待转换的字符串
 * @param int $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int $len 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string $glue 分割符
 * +----------------------------------------------------------
 * @return string   处理后的字符串
 * +----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@")
{
    if (empty($string))
        return false;

    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string  = mb_substr($string, 1, $strlen, "utf8");
            $strlen  = mb_strlen($string);
        }
    }
    if ($type == 0) {
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", $array);
    } else if ($type == 1) {
        $array = array_reverse($array);
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", array_reverse($array));
    } else if ($type == 2) {
        $array    = explode($glue, $string);
        $array[0] = hideStr($array[0], $bengin, $len, 1);
        $string   = implode($glue, $array);
    } else if ($type == 3) {
        $array    = explode($glue, $string);
        $array[1] = hideStr($array[1], $bengin, $len, 0);
        $string   = implode($glue, $array);
    } else if ($type == 4) {
        $left  = $bengin;
        $right = $len;
        $tem   = array();
        for ($i = 0; $i < ($length - $right); $i++) {
            if (isset($array[$i]))
                $tem[] = $i >= $left ? "*" : $array[$i];
        }
        $array = array_chunk(array_reverse($array), $right);
        $array = array_reverse($array[0]);
        for ($i = 0; $i < $right; $i++) {
            $tem[] = $array[$i];
        }
        $string = implode("", $tem);
    }
    return $string;
}

/**
 * @return array|mixed
 */
function getConfig()
{
    $configFilePath = env('CONFIG_PATH') . 'config.txt';
    return file_exists($configFilePath) ? unserialize(file_get_contents($configFilePath)) : [];
}

/**
 * @param array $data
 * @return bool|int
 */
function putConfig(array $data)
{
    $configFilePath = env('CONFIG_PATH') . 'config.txt';
    addServerLog(1, 6, getClientIp(), '更新配置文件事件');
    return file_put_contents($configFilePath, serialize($data));
}


/**
 * @param int $uid
 * @param string $type
 * @param string $ipv4
 * @param string $data
 * @return int|string
 */
function addServerLog(int $uid, string $type, string $ipv4, string $data)
{
    $insertResult = \think\Db::table('epay_log')->insert([
        'uid'        => $uid,
        'type'       => $type,
        'ipv4'       => $ipv4,
        'createTime' => getDateTime(),
        'data'       => $data
    ]);
    return $insertResult;
}

/**
 * 签名数据 MD5
 * 这个上个世纪的产品要兼容
 * @param string $preStr //需要签名的字符串
 * @param string $key //密匙
 * @return string
 */
function signMD5(string $preStr, string $key)
{
    return md5($preStr . $key);
}

/**
 * 验证签名 MD5
 * 这个上个世纪的产品要兼容
 * @param string $preStr
 * @param string $sign
 * @param string $key
 * @return bool
 */
function verifyMD5(string $preStr, string $key, string $sign)
{
    return md5($preStr . $key) == $sign;
}

/**
 * 对数组排序
 * @param $para array//排序前的数组
 * @return array //排序后的数组
 */
function argSort($para)
{
    ksort($para);
    reset($para);
    return $para;
}

/**
 * 除去数组中的空值和签名参数
 * @param $para array//签名参数组
 * @param bool $isUrlDecode
 * @return array//去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para, $isUrlDecode = false)
{
    $para_filter = array();
    foreach ($para as $key => $val) {
        if ($key == 'sign' || $key == 'sign_type' || empty($val))
            continue;
        else
            if ($isUrlDecode)
                $para_filter[$key] = urldecode($val);
            else
                $para_filter[$key] = $val;
    }
    return $para_filter;
}

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para array//需要拼接的数组
 * @return string//拼接完成以后的字符串
 */
function createLinkString($para)
{
    $arg = '';
    foreach ($para as $key => $val) {
        $arg .= $key . '=' . $val . '&';
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, strlen($arg) - 1);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

/**
 * @param $para
 * @return bool|string
 */
function createLinkStringUrlEncode($para)
{
    $arg = '';
    foreach ($para as $key => $val) {
        $arg .= $key . '=' . urlencode($val) . '&';
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, strlen($arg) - 1);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }
    return $arg;
}

/**
 * 数组转换成xml字符串
 * @param $arr
 * @return string
 */
function arrayToXml(array $arr)
{
    $xml = '<xml>';
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<$key>$val</$key>";
        } else
            $xml .= "<$key><![CDATA[$val]]></$key>";
    }
    $xml .= '</xml>';
    return $xml;
}

/**
 * xml转换成数组
 * @param $xml
 * @return array|mixed|object
 */
function xmlToArray(string $xml)
{
    try {
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    } catch (Exception $exception) {
        header('contact: 2440376771');
        header('remark: CTF');
        exit('<h1 style="text-align: center;margin-top: 10%;">您在传递啥信息呢，反正这东西不是XML格式字符串。</h1><p style="text-align: center;">顺便说下，我留了个后门在这套程序里面，当成CTF去刷吧~</p>');
    }
}

/**
 * 根据订单ID构建回调地址
 * @param $tradeNo
 * @param string $type
 * @return string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function buildCallBackUrl(string $tradeNo, string $type = 'return')
{
    $type = strtolower($type);
    if ($type != 'notify' && $type != 'return')
        return '';
    //type is error
    $orderData = \think\Db::table('epay_order')->alias('a')->where('a.tradeNo', $tradeNo)
        ->leftJoin('epay_order_attr b', 'b.attrKey = "discountMoney" and b.tradeNo = a.tradeNo')
        ->field('a.status,a.uid,a.tradeNo,a.tradeNoOut,a.type,a.productName,a.money,a.' . $type . '_url,b.attrValue as `discountMoney`')->limit(1)->select();
    if (empty($orderData))
        return '';
    //order type
    $orderData = $orderData[0];

    $userKey = \think\Db::table('epay_user')->where('id', $orderData['uid'])->field('key')->limit(1)->select();
    if (empty($userKey))
        $userKey = '';
    else
        $userKey = $userKey[0]['key'];
    //get user key
    switch ($orderData['type']) {
        case 1:
            $payType = 'wxpay';
            break;
        case 2:
            $payType = 'qqpay';
            break;
        case 3:
            $payType = 'alipay';
            break;
        case 4:
            $payType = 'bankpay';
            break;
        default:
            $payType = 'none';
            break;
    }
    //兼容层
    $args        = [
        'pid'          => $orderData['uid'],
        'trade_no'     => $orderData['tradeNo'],
        'out_trade_no' => $orderData['tradeNoOut'],
        'type'         => $payType,
        'name'         => $orderData['productName'],
        'money'        => ($orderData['money'] + $orderData['discountMoney']) / 100,
        'trade_status' => 'TRADE_' . ($orderData['status'] ? 'SUCCESS' : 'FAIL')
    ];
    $args        = argSort(paraFilter($args));
    $sign        = signMD5(createLinkString($args), $userKey);
    $callBackUrl = $orderData[$type . '_url'] . (strpos($orderData[$type . '_url'], '?') ? '&' : '?') . createLinkStringUrlEncode($args) . '&sign=' . $sign . '&sign_type=MD5';
    return $callBackUrl;
}

function buildRequestForm(string $requestUrl, array $param, string $method, string $button_name = '')
{
    //待请求参数数组
    $sHtml = '<form id=\'alipaysubmit\' name=\'alipaysubmit\' action=\'' . $requestUrl . '\' method=\'' . $method . '\'>';
    foreach ($param as $key => $value) {
        $sHtml .= '<input type=\'hidden\' name=\'' . $key . '\' value=\'' . $value . '\'/>';
    }
    $sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";
    $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";
    return $sHtml;
}

/**
 * 组合结算部分
 * @param $tradeNo
 * @param bool $notify
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @throws \think\exception\PDOException
 */
function processOrder($tradeNo, $notify = true)
{
    if (empty($tradeNo))
        return;
//    usleep(100000);
    //睡眠50ms 容错主从同步慢问题
    $orderInfo = \think\Db::table('epay_order')->where('tradeNo', $tradeNo)->field('uid,money,status')->limit(1)->select();
    if (empty($orderInfo))
        return;
    if (!$orderInfo[0]['status'])
        return;
    //订单无效
    $userInfo = \think\Db::table('epay_user')->where('id', $orderInfo[0]['uid'])->field('clearType,username,rate')->limit(1)->select();
    if (empty($userInfo))
        return;

    if (empty($orderInfo[0]['rate'])) {
        $config               = getConfig();
        $orderInfo[0]['rate'] = $config['defaultMoneyRate'];
    }
    $rate = $userInfo[0]['rate'] / 100;


    $rateMoney = \app\pay\model\PayModel::getOrderAttr($tradeNo, 'rateMoney');
    if (empty($rateMoney)) {
        $addMoneyRate = $orderInfo[0]['money'] * ($rate / 100);
        $addMoneyRate = $addMoneyRate * 10;
        $addMoneyRate = number_format($addMoneyRate, 2, '.', '');
        //累计金额方便统计 仅仅保留两位小数
        $result = \think\Db::table('epay_user')->limit(1)->where('id', $orderInfo[0]['uid'])->inc('balance', $addMoneyRate)->update();
    } else {
        $result = \think\Db::table('epay_user')->limit(1)->where('id', $orderInfo[0]['uid'])->dec('balance', $rateMoney)->update();
    }
    //处理用户余额部分
    if (!$result) {
        trace('更新用户余额错误 uid =>' . $orderInfo[0]['uid'] . ' tradeNo =>' . $tradeNo . ' 订单金额 =>' . ($orderInfo[0]['money'] / 100), 'error');
//        return;
    }
    //处理更新余额失败部分

    {
        $orderPayConfig = \app\pay\model\PayModel::getOrderAttr($tradeNo, 'payConfig');
        if (!empty($orderPayConfig)) {
            $orderPayConfig = json_decode($orderPayConfig, true);
            \think\Db::table('epay_wxx_apply_list')->where('subMchID', $orderPayConfig['subMchID'])->limit(1)->inc('money', $orderInfo[0]['money'])->update();
        }
    }
    //这个负责轮询

    if ($result && $userInfo[0]['clearType'] == 4) {
        settleUserDepositMoney($orderInfo[0]['uid']);
        //支付宝自动转账
    }
    //必须金额更新成功后才能触发自动结算
    if ($notify) {
        $notifyUrl     = buildCallBackUrl($tradeNo, 'notify');
        $requestResult = curl($notifyUrl);
        //开启高级模式 不为200 直接走重发
        $isReCallback = false;
        if ($requestResult === false)
            $isReCallback = true;
        else if (strpos(strtoupper($requestResult), 'SUCCESS') === false) {
            $isReCallback = true;
        }
        if ($isReCallback)
            addCallBackLog($orderInfo[0]['uid'], $notifyUrl);
        //回调事件
//        trace('日志信息: 请求结果 => '.$requestResult .' 请求url =>' .$notifyUrl,'info');
    }
}

/**
 * 新增定时回调
 * @param int $uid
 * @param string $url
 * @param string $method
 * @param array $requestData
 */
function addCallBackLog(int $uid, string $url, string $method = 'get', array $requestData = [])
{
    if (empty($requestData) && $method == 'get') {
        $parseUrl    = parse_url($url);
        $url         = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'];
        $requestData = \GuzzleHttp\Psr7\parse_query($parseUrl['query']);
    }
    //构建兼容层
    $data    = [
        'url'    => $url,
        'method' => $method,
        'param'  => $requestData,
    ];
    $config  = [
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'database' => 0,
        'password' => '',
    ];
    $client  = new \Delayer\Client($config);
    $message = new \Delayer\Message([
        'id'    => md5(uniqid(mt_rand(), true)),
        'topic' => '15_1',
        'body'  => json_encode($data),
    ]);
    $client->push($message, 15, 86400);
}

/**
 * 转账用户金额
 * @param $uid
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @throws \think\exception\PDOException
 */
function settleUserDepositMoney($uid)
{
    $deposit     = getPayUserAttr($uid, 'deposit');
    $settleMoney = getPayUserAttr($uid, 'settleMoney');
    if (empty($deposit) || empty($settleMoney))
        return;
    if (intval($settleMoney) < 20)
        return;
    //不允许低于0.2元
    $deposit     = intval($deposit) * 10;
    $settleMoney = intval($settleMoney) * 10;
    //进制转换
    $userInfo = \think\Db::table('epay_user')->field('balance,account,username')->limit(1)->where('id', $uid)->select();
    if (empty($userInfo))
        return;
    $userBalance = $userInfo[0]['balance'];
    //注意用户余额三位小数
    if (($userBalance - $deposit) < $settleMoney)
        return;
    //判断用户金额是否达到自动结算金额
    $systemConfig = getConfig();
    $settleID     = date('Ymd') . rand(111, 999);
    //结算ID
    $aliPayModel   = new \app\pay\model\AliPayModel($systemConfig['alipay']);
    $transferMoney = number_format($settleMoney / 1000, 2, '.', '');
    //需要转账的金额
    $transferResult = $aliPayModel->toAccountTransfer($settleID, $userInfo[0]['account'], $userInfo[0]['username'], $transferMoney);
    if ($transferResult) {
        $updateUserResult = \think\Db::table('epay_user')->where('id', $uid)->limit(1)->dec('balance', ($transferMoney * 1000))->update();
        if (!$updateUserResult)
            trace('自动结算用户余额有误 uid=>' . $uid . ' 目标金额=>' . $transferMoney, 'error');
        else
            \think\Db::table('epay_settle')->insert([
                'uid'        => $uid,
                'clearType'  => 4,
                'addType'    => 2,
                'account'    => $userInfo[0]['account'],
                'username'   => $userInfo[0]['username'],
                'money'      => $transferMoney * 100,
                'fee'        => 0,
                'status'     => 1,
                'createTime' => getDateTime()
            ]);
        //记录信息
    }
    //开始转账
}


/**
 * @param string $key
 * @return array|PDOStatement|string|\think\Collection
 */
function getServerConfig(string $key)
{
    try {
        $result = \think\Db::table('epay_config')->field('value')->limit(1)->where([
            'key' => $key
        ])->select();
        if (empty($result))
            $result = '';
        else
            $result = $result[0]['value'];
    } catch (Exception $exception) {
        $result = '';
    }
    return $result;
}

function getIpSite(String $ip)
{
    $content = curl("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
    if ($content === false)
        return '未知登陆地区';
    $json = json_decode($content, true);
    if ($json['code'] != 0)
        return '未知登陆地区';
    $json = $json['data'];
    return $json['country'] . '-' . $json['region'] . '-' . $json['city'] . '   ' . $json['isp'];
}

function getClientIp()
{
//    $cip = 'unknown';
//    if ($_SERVER['REMOTE_ADDR']) {
//        $cip = $_SERVER['REMOTE_ADDR'];
//    } else if (getenv('REMOTE_ADDR')) {
//        $cip = getenv('REMOTE_ADDR');
//    }
//    return $cip;
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) != false)
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}

/**
 * @param string $key
 * @param string $value
 * @return int|string
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function setServerConfig(string $key, string $value)
{
    if (getServerConfig($key) != '') {
        return \think\Db::table('epay_config')->where([
            'key' => $key
        ])->limit(1)->update([
            'value' => $value
        ]);
    } else {
        return \think\Db::table('epay_config')->insertGetId([
            'key'   => $key,
            'value' => $value
        ]);
    }
}

/**
 * @param $uid
 * @param string $key
 * @return array|PDOStatement|string|\think\Collection
 */
function getPayUserAttr($uid, string $key)
{
    try {
        $result = \think\Db::table('epay_user_attr')->field('value')->limit(1)->where([
            'uid' => $uid,
            'key' => $key
        ])->select();
        if (empty($result))
            $result = '';
        else
            $result = $result[0]['value'];
    } catch (Exception $exception) {
        $result = '';
    }
    return $result;
}

/**
 * @param $uid
 * @param string $key
 * @param string $value
 * @return int|string
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function setPayUserAttr($uid, string $key, string $value)
{
    if (getPayUserAttr($uid, $key) != '') {
        return \think\Db::table('epay_user_attr')->where([
            'uid' => $uid,
            'key' => $key
        ])->limit(1)->update([
            'value' => $value
        ]);
    } else {
        return \think\Db::table('epay_user_attr')->insertGetId([
            'uid'   => $uid,
            'key'   => $key,
            'value' => $value
        ]);
    }
}

function exportToExcel($filename, $tileArray = [], $dataArray = [])
{
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 0);
    ob_end_clean();
    ob_start();
    header("Content-Type: text/csv");
    header("Content-Disposition:filename=" . $filename);
    $fp = fopen('php://output', 'w');
    fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
    //转码 防止乱码(比如微信昵称(乱七八糟的))
    fputcsv($fp, $tileArray);
    $index = 0;
    foreach ($dataArray as $item) {
        if ($index == 1000) {
            $index = 0;
            ob_flush();
            flush();
        }
        $index++;
        fputcsv($fp, $item);
    }

    ob_flush();
    flush();
    ob_end_clean();
}

/**
 * 百度获取短链接
 * @param string $url
 * @return string
 */
function shortenUrl(string $url): string
{
    return $url;
    $requestUrl = 'http://api.suolink.cn/api.php';
    $param      = [
        'url' => urlencode($url),
        'key' => '5d205cddb1a9c70e343cfc84@73e9912eaa3ce9c238fb8de0a3488d72'
    ];
    $result     = curl($requestUrl, [], 'get', $param, '', false);
    if ($result === false)
        return 'get shorten fail';
    return $result;

}