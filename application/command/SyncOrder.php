<?php

namespace app\command;

use app\pay\model\AliPayModel;
use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
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
        $userData       = \think\Db::table('epay_user')->field('id,rate,clearType')->cursor();
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
            return '';
        //type is error
        $orderData = \think\Db::table('pay_order')->where('trade_no', $tradeNo)->field('pid,trade_no,out_trade_no,type,name,money,' . $type . '_url')->limit(1)->select();
        if (empty($orderData))
            return '';
        //order type
        $orderData = $orderData[0];

        $userKey = \think\Db::table('pay_user')->where('id', $orderData['pid'])->field('key')->limit(1)->select();
        if (empty($userKey))
            $userKey = '';
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
