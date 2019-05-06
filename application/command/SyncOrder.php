<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class SyncOrder extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('syncOrder')->setDescription('user balance');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->info('[' . getDateTime() . '] start sync order');
        $this->statisticalOrderMoney();
        $output->info('[' . getDateTime() . '] sync order success ');

//        $output->info('start delete fail order');
//        $this->delFailOrder();
//        $output->info('end delete fail order');

//        $output->info(' start call back order');
//        $this->supplyOrder(2);
//        $output->info('end call back order');
        //不再使用php自动补单了，太慢了
    }

    /**
     * 统计订单数据
     */
    private function statisticalOrderMoney()
    {
        $userData       = Db::table('epay_user')->field('id,rate,clearMode')->cursor();
        $totalRateMoney = 0;
        foreach ($userData as $value) {
            $uid        = $value['id'];
            $rate       = $value['rate'] / 100;
            $totalMoney = Db::table('epay_order')->where([
                'uid'      => $uid,
                'status'   => 1,
                'isShield' => 0
            ])->whereTime('endTime', 'today')->sum('money');
//            if ($value['clearMode'] == 0) {
//                Db::table('epay_user')->where('id', $uid)->limit(1)->update([
//                    'balance' => $totalMoney * ($rate / 100) * 10
//                ]);
//            }
            $totalRateMoney += $totalMoney * ($rate / 100);
        }
        $totalMoney = Db::table('epay_order')->where([
            'status' => 1
        ])->whereTime('endTime', 'today')->sum('money');

        setServerConfig('totalMoney', $totalMoney);
        setServerConfig('totalMoneyRate', $totalRateMoney);
    }

    /**
     * 补发订单
     * @param int $callBackCount //从0开始 1 则为两次 2 则为三次
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function supplyOrder($callBackCount = 1)
    {
        for ($i = $callBackCount; $i >= 0; $i--) {
            $isProxy = false;
            if ($i >= 1)
                $isProxy = true;
            $callbackList = Db::table('epay_callback')->where('status', $i)->field('id,url')->cursor();
            foreach ($callbackList as $value) {
                $result = $this->curl($value['url'], [], 'get', '', '', true, $isProxy);
                if (!$result['isSuccess'])
                    Db::table('epay_callback')->where('id', $value['id'])->limit(1)->update([
                        'errorMessage' => $result['errorMsg'],
                        'status'       => ($i + 1),
                        'updateTime'   => getDateTime()
                    ]);
                else
                    Db::table('epay_callback')->where('id', $value['id'])->limit(1)->delete();
            }
            //采用不代理方式进行更新
        }
    }

    private function delFailOrder()
    {
        Db::table('epay_order')->where('status', 0)->whereTime('createTime', '<=', '-2 day')->delete();
    }

    private function curl($url = '', $addHeaders = [], $requestType = 'get', $requestData = '', $postType = '', $urlEncode = true, $isProxy = false)
    {
        if (empty($url))
            return '';
        //容错处理
        $headers  = [];
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //设置允许302转跳
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        if ($isProxy) {
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, '43.248.187.89');
            //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, 8118);
            //代理服务器端口
        }
        //set proxy
//        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
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

    private function buildCallBackUrlA(string $tradeNo, string $type)
    {
        $type = strtolower($type);
        if ($type != 'notify' && $type != 'return')
            return '1';
        //type is error
        $orderData = Db::table('pay_order')->where('trade_no', $tradeNo)->field('pid,trade_no,out_trade_no,type,name,money,' . $type . '_url')->limit(1)->select();
        if (empty($orderData))
            return '2';
        //order type
        $orderData = $orderData[0];

        $userKey = Db::table('pay_user')->where('id', $orderData['pid'])->field('key')->limit(1)->select();
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

}
