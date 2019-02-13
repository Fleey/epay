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
}
