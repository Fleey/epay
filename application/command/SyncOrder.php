<?php

namespace app\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;

class SyncOrder extends Command
{
    /* @var $mysql \think\db\Query */
    private $mysql;


    protected function configure()
    {
        // 指令配置
        $this->setName('syncOrder')->setDescription('user balance');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
//        exit(dump($this->curl($this->buildCallBackUrlA(' 2019030213595927171','notify'))));
//        $userData = \think\Db::table('test')->cursor();
//        $i        = 0;
//        foreach ($userData as $value) {
//            $i++;
//            $isPaySuccess = false;
//            if ($value['type'] == 'wxpay') {
//                $wxModel = new WxPayModel([
//                    'appid' => 'wx9036bbf0548da6ff',
//                    'mchid' => '1514622451',
//                    'key'   => 'TcJFmibU4buZQi9wPWT9RNXyOrI28BaD'
//                ]);
//                $result  = $wxModel->selectWxPayRecord((string)$value['trade_no']);
//                if (!empty($result['trade_state']))
//                    if ($result['trade_state'] == "SUCCESS")
//                        $isPaySuccess = true;
//            } else if ($value['type'] == 'qqpay') {
//                $qqModel = new QQPayModel([
//                    'mchid'  => '1525641811',
//                    'mchkey' => '8tZ2XvlksiuUHdEXwVJGKUV0M3UJaODr'
//                ]);
//                $result  = $qqModel->selectPayRecord((string)$value['trade_no']);
//                if (!empty($result['trade_state']))
//                    if ($result['trade_state'] == "SUCCESS")
//                        $isPaySuccess = true;
//            } else {
//                $alipayModel = new AliPayModel([
//                    'transferPrivateKey' => 'MIIEpAIBAAKCAQEAvKn0sYPwzEQUwwhwnBPTAvBflVIHhkHz9zK5rzV4FXbWHNsqPZ6yuBhEswGRfDYgoSJIml2edz2E+JnphS5Z4QWhIscwHcZPtwnfs1iY2bSm0HEHgyxooHOY44m4er6cbCSjX21WU6k7Q5xkuSRZMHOqCwL9WNqsowrIu41i+JD5VPeoDNvcZDVn2BKusmgzg3gw2DhIIDUy3hjtjM0wLoCk8zILuQVVr5tQKUWvDqSDzjOPM/n9rfFW0xM3Pki1GqBCaQlBZ+c9vuJHNR4v4KgyTc/EH/P3up1StNgEZNkkeEs/1/VDpzN+EXZ89lV/L0yEBK7xOw7bLNYRx0aBkQIDAQABAoIBAQCh/skV17XenyK4qLmQutD3+BAKtgmx9VWXT53y3NWEkKqe0m09xdOtkWED13flkNCJq1dt/K8BsfhIQlgqPDd+qQfIRgKBvnNaNoc3hv6QCLcnybXqoyofg8Kmte2Kr7q+fOMvIEH8yhYSIuq1solGujorBGEnd3S+9paPvIJb7RzKT/CuvnGdBv83aWOv1Qw8iLG6KdWcfk5/WmuN/6ou5Iica0mfqnljNy/NMyLG49srh13MXs3YG132wPcnY0rG7BuELoNyy2Yyc3dvcNo/fZXD9SzI0TcaCel8wsGrTd3XHxWOT9iS3ortX8V7TeeUS0N5tMkJt3tMt59wuep1AoGBAPBwKydzTX6U/0WfmHFGxCaN0PnZCjT0bUPRWH6TC71miPiFBZ5NTzKCp2kM9MBzC0J0CAbT69rLv2ra4J/6I/gHQ4VgbyCNd7y0DazF4E15XKymlTR5mLaUI+1JjON+9q1dhYjwFFBfP7z8HzbWLvq9xEZcyJ7qGyAoil+13TlrAoGBAMjf79mcpichPHlmjdnukJ7Wd+fudFImPF81Zk3dP8Hq1+Pj9781F7yeYr2bJR5K/f0z4+Tofl2E7zToOJGrQ48avwg2lMoIsWsPnimzMU11nxFitCLw4wgkePPW5nzvAOGBzwO9ppansfbBfGapVyiSCYy/0F0M18Z64HbnRkPzAoGBANBBh89i2qOicL0gcEzla66tNW3DZUja0e1k3Y681PVXY5pGtcgY1Fk+u7yNAU3UF9OWZwFq+6YGxqTKMre+VPtXZ0+WaIq8nhKvrgyRVCgmz3On0iKik/jItZmpFERUS1t8XtZuhFndNnr9shewSv7Z8bC0Wvzyb05abwhZoOVDAoGAXA2La80Gs23eub+Oh+10puWfw1CaS78r8XGWNV6LxkDpuIySzzP0ccKfe0ZqxywUowExkYgdyJuPx04YBmFWr3DRVGE25DMBow9gKrnsgRPC1oPGCzEayXN1XkEAFQat/6muBYfWnLmyq2LVsHIv9+6co7yPLuUgyNssnDC2GZMCgYAuJFuAlS5hwU21+Y4SJPe5ZUVHLN/A+NoQil3165+gSe6+O1z+ED0x7CkYguuKVLJpGCS6D67p62Ncsu2+/m61cdANvvWnllTQ4PE9uGstvEorYN72YxGnIX3FJaqTVmGEYd15Rwx90B3QMuM6CHj2mSL8jqVRiU3bLGK2/xJjDQ==',
//                    'transferPartner'    => '2018112062250867'
//                ]);
//                $result      = $alipayModel->selectPayRecord((string)$value['trade_no']);
//                if (!empty($result['trade_status']))
//                    if ($result['trade_status'] == 'TRADE_SUCCESS')
//                        $isPaySuccess = true;
//            }
//            $result = Db::table('pay_order')->where('trade_no', $value['order'])->limit(1)->field('id')->select();
//            if (!empty($result)) {
//                Db::table('pay_order')->where('trade_no', $value['order'])->limit(1)->update(['status' => 1]);
//                $this->curl($this->buildCallBackUrlA($value['order'], 'notify'));
//                echo 'success ';
//            }
//            echo $i . PHP_EOL;
//        }
//        echo 'ok';
//        return;
        $userData       = \think\Db::table('epay_user')->field('id,rate,clearType')->where('clearMode', 0)->cursor();
        $totalRateMoney = 0;
        foreach ($userData as $value) {
            $uid            = $value['id'];
            $rate           = $value['rate'] / 100;
            $totalMoney     = \think\Db::table('epay_order')->where([
                'uid'      => $uid,
                'status'   => 1,
                'isShield' => 0
            ])->whereTime('endTime', 'today')->sum('money');
            $totalRateMoney += $totalMoney * ($rate / 100);
            if ($value['clearType'] != 4) {
                \think\Db::table('epay_user')->where('id', $uid)->limit(1)->update([
                    'balance' => $totalMoney * ($rate / 100) * 10
                ]);
            }
            //not auto settle
        }
        $totalMoney = \think\Db::table('epay_order')->where([
            'status' => 1
        ])->whereTime('endTime', 'today')->sum('money');

        setServerConfig('totalMoney', $totalMoney);
        setServerConfig('totalMoneyRate', $totalRateMoney);

        $output->info('[' . getDateTime() . '] sync order success ');

        $callbackList = \think\Db::table('epay_callback')->where('status', 1)->field('id,url')->cursor();
        foreach ($callbackList as $value) {
            $result = @file_get_contents($value['url'], false, stream_context_create([
                'http' => [
                    'method'  => 'GET',
                    'timeout' => 5
                ]
            ]));
            if ($result === false)
                \think\Db::table('epay_callback')->where('id', $value['id'])->limit(1)->update([
                    'status'     => 2,
                    'updateTime' => getDateTime()
                ]);
            else
                \think\Db::table('epay_callback')->where('id', $value['id'])->limit(1)->delete();
        }
        //采用file_get_contents方式进行更新
        $callbackList = \think\Db::table('epay_callback')->where('status', 0)->field('id,url')->cursor();
        foreach ($callbackList as $value) {
            $result = $this->curl($value['url']);
            if (!$result['isSuccess'])
                \think\Db::table('epay_callback')->where('id', $value['id'])->limit(1)->update([
                    'errorMessage' => $result['errorMsg'],
                    'status'       => 1,
                    'updateTime'   => getDateTime()
                ]);
            else
                \think\Db::table('epay_callback')->where('id', $value['id'])->limit(1)->delete();
        }
        //采用不代理方式进行更新

//        $orderList = \think\Db::table('epay_order')->where([
//            'status'   => 1,
//            'isShield' => 0,
//            'uid'      => 1128
//        ])->whereBetweenTime('createTime', '2019-2-6 00:00:00', '2019-2-7 19:00:00')->field('tradeNo,createTime')->select();
//        foreach ($orderList as $value) {
//            $tradeNo = $value['tradeNo'];
//            $url     = buildCallBackUrl($tradeNo, 'notify');
//            $result  = curl($url);
//            $output->info('请求 tradeNo=>' . $tradeNo . ' 请求结算 =>' . $result . ' 时间 =>' . $value['createTime']);
//        }
//        $output->info('success');
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

    protected function curl($url = '', $addHeaders = [], $requestType = 'get', $requestData = '', $postType = '', $urlEncode = true)
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
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXY, '116.255.172.156'); //代理服务器地址
        curl_setopt($ch, CURLOPT_PROXYPORT, 16819); //代理服务器端口
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
