<?php

namespace app\command;


use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Test extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('test')->setDescription('user test');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $config = getConfig();
        $config = $config['alipay'];
        dump($config);
//        $userData = Db::table('epay_user')->field('id,rate,clearMode')->cursor();
//        foreach ($userData as $value) {
//            $uid        = $value['id'];
//            $rate       = $value['rate'] / 100;
//            $totalMoney = Db::table('epay_order')->where([
//                'uid'      => $uid,
//                'status'   => 1,
//                'isShield' => 0
//            ])->whereTime('endTime', 'today')->sum('money');
//            if ($value['clearMode'] == 0) {
//                $totalMoney  = $totalMoney * ($rate / 100) / 100;
//                $deleteMoney = Db::table('epay_user_money_log')->where('uid', $uid)->whereTime('createTime', 'today')->sum('money');
//                $deleteMoney /= 1000;
//                $balance     = ($totalMoney + $deleteMoney) * 1000;
//                Db::table('epay_user')->where('id', $uid)->limit(1)->update([
//                    'balance' => $balance
//                ]);
//            }
//        }

//        $orderList = Db::table('epay_order')->where('createTime', '>=', '2019-6-6 9:00:00')
//            ->where('createTime', '<=', '2019-6-6 11:00:00')->where([
//                'status' => 0,
//                'type'   => 2
//            ])->field('tradeNo')->select();
//
////        $qqModel   = new QQPayModel([
////            'mchid'  => 0,
////            'mchkey' => ''
////        ]);
//        $wxModel = new WxPayModel([
//            'appid' => '',
//            'mchid' => '',
//            'key'   => ''
//        ], 'h5');
//        $i       = 0;
//        foreach ($orderList as $value) {
//            $value        = $value['tradeNo'];
//            $selectResult = $wxModel->selectWxPayRecord($value);
//            if ($selectResult['return_code'] != 'SUCCESS')
//                continue;
//            if ($selectResult['result_code'] != 'SUCCESS')
//                continue;
//            if ($selectResult['trade_state'] == 'SUCCESS') {
//                $i++;
//                Db::table('epay_order')->where('tradeNo=:tradeNo', ['tradeNo' => $value])->limit(1)->update([
//                    'status'  => 1,
//                    'endTime' => getDateTime()
//                ]);
//                //更新订单状态
//                processOrder($value);
//                //统一处理订单
//                echo $i . ' 成功更新 => ' . $value . PHP_EOL;
//            }
    }


    private
    function dumpSettleResult()
    {
        $userList = Db::table('epay_user')->field('id,username,rate')->cursor();
        $a        = 0;
        $b        = 0;
        $c        = 0;

        $a1 = 0;
        $b2 = 0;
        $c3 = 0;
        foreach ($userList as $key => $value) {
            $userInfo    = $value;
            $rate        = $userInfo['rate'] / 10000;
            $day16       = Db::table('epay_order')->where([
                'status'   => 1,
                'isShield' => 0,
                'uid'      => $userInfo['id']
            ])->whereBetweenTime('endTime', '2019-5-31')->sum('money');
            $day16Settle = Db::table('epay_settle')->where([
                'uid' => $userInfo['id']
            ])->whereBetweenTime('createTime', '2019-6-1')->limit(1)->field('money')->select();
            $day16       /= 100;
            $day16Rate   = $day16 * $rate;
            if (empty($day16Settle))
                $day16Settle = 0;
            else
                $day16Settle = $day16Settle[0]['money'] / 100;
            $a += $day16;
            $b += $day16Rate;
            $c += $day16Settle;

            $day17       = Db::table('epay_order')->where([
                'status'   => 1,
                'isShield' => 0,
                'uid'      => $userInfo['id']
            ])->whereBetweenTime('endTime', '2019-6-1')->sum('money');
            $day17Settle = Db::table('epay_settle')->where([
                'uid' => $userInfo['id']
            ])->whereBetweenTime('createTime', '2019-6-2')->limit(1)->field('money')->select();
            $day17       /= 100;
            $day17Rate   = $day17 * $rate;

            if (empty($day17Settle))
                $day17Settle = 0;
            else
                $day17Settle = $day17Settle[0]['money'] / 100;

            $a1 += $day17;
            $b2 += $day17Rate;
            $c3 += $day17Settle;

            echo 'uid=>(' . $userInfo['id'] . ') username=>(' . $userInfo['username'] . ') 1=>(' . $day16 . ',' . $day16Rate . ',' . $day16Settle . ')' . ' 2=>(' . $day17 . ',' . $day17Rate . ',' . $day17Settle . ')' . PHP_EOL;
        }
        echo "1=>($a,$b,$c) 2=>($a1,$b2,$c3)";
        // 指令输出
    }

    private function buildCallBackUrlA(string $tradeNo, string $type)
    {
        $type = strtolower($type);
        if ($type != 'notify' && $type != 'return')
            return '1';
        //type is error
        $orderData = \think\Db::table('pay_order')->where('trade_no', $tradeNo)->field('pid,trade_no,out_trade_no,type,name,money,' . $type . '_url')->limit(1)->select();
        if (empty($orderData))
            return '2';
        //order type
        $orderData = $orderData[0];

        $userKey = \think\Db::table('pay_user')->where('id', $orderData['pid'])->field('key')->limit(1)->select();
        if (empty($userKey))
            $userKey = '3';
        else
            $userKey = $userKey[0]['key'];
        //get user key
//兼容层
        $args        = [
            'pid'          => $orderData['pid'],
            'trade_no'     => $orderData['trade_no'],
            'out_trade_no' => $orderData['out_trade_no'],
            'type'         => $orderData['type'],
            'name'         => $orderData['name'],
            'money'        => $orderData['money'],
            'trade_status' => 'TRADE_SUCCESS'
        ];
        $args        = argSort(paraFilter($args));
        $sign        = signMD5(createLinkString($args), $userKey);
        $callBackUrl = $orderData[$type . '_url'] . (strpos($orderData[$type . '_url'], '?') ? '&' : '?') . createLinkStringUrlEncode($args) . '&sign=' . $sign . '&sign_type=MD5';
        return $callBackUrl;
    }

    protected
    function curl($url = '', $addHeaders = [], $requestType = 'get', $requestData = '', $postType = '', $urlEncode = true)
    {
        if (empty($url))
            return '';
        //容错处理
        $headers  = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36'
        ];
        $postType = strtolower($postType);
        if ($requestType != 'get') {
            if ($postType == 'json') {
                $headers[]   = 'Content-Type: application/json; charset=utf-8';
                $requestData = is_array($requestData) ? json_encode($requestData) : $requestData;
            } else if ($postType == 'xml') {
                $headers[] = 'Content-Type:text/xml; charset=utf-8';
            }
            $headers[] = 'Content-Length: ' . strlen($requestData);
        }
        if ($requestType == 'get' && is_array($requestData)) {
            $tempBuff = '';
            foreach ($requestData as $key => $value) {
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //设置允许302转跳
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_PROXY, '116.255.172.156'); //代理服务器地址
//        curl_setopt($ch, CURLOPT_PROXYPORT, 16819); //代理服务器端口
        //set proxy
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        //gzip

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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
            if (is_array($requestData) && !empty($requestData)) {
                $temp = '';
                foreach ($requestData as $key => $value) {
                    if ($urlEncode) {
                        $temp .= rawurlencode(rawurlencode($key)) . '=' . rawurlencode(rawurlencode($value)) . '&';
                    } else {
                        $temp .= $key . '=' . $value . '&';
                    }
                }
                $requestData = substr($temp, 0, strlen($temp) - 1);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        }
        //只要不是get姿势都塞东西给他post
        $result   = curl_exec($ch);
        $errorMsg = '';
        if ($result === false)
            $errorMsg = curl_error($ch);
        curl_close($ch);

        return ['isSuccess' => ($result !== false), 'errorMsg' => $errorMsg];
    }
}
