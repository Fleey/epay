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

        $output->info('[' . getDateTime() . '] start data Statistics');
        $this->dataStatistics();
        $output->info('[' . getDateTime() . '] data Statistics success ');
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

    private function dataStatistics(){
        $cacheDashboardData = cache('DashboardData');
        if (empty($cacheDashboardData)) {
            $data['totalOrder'] = Db::table('epay_order')->count('id');
            $data['totalUser']  = Db::table('epay_user')->count('id');
            $data['totalMoney'] = getServerConfig('totalMoney');
            if (empty($data['totalMoney']))
                $data['totalMoney'] = 0;
            $data['totalMoneyRate'] = getServerConfig('totalMoneyRate');
            if (empty($data['totalMoneyRate']))
                $data['totalMoneyRate'] = 0;
            $data['settleRecord'] = [];
            for ($i = 6; $i >= 1; $i--) {
                $data['settleRecord'][] = ['createTime' => date('Y-m-d', strtotime('-' . $i . ' day'))];
            }
            $data['settleRecord'][] = ['createTime' => date('Y-m-d', strtotime('now'))];
            foreach ($data['settleRecord'] as $key => $value) {
                $data['settleRecord'][$key]['money'] = Db::table('epay_settle')->whereBetweenTime('createTime', $value['createTime'])->sum('money');
            }
            {
                $buildOrderStatistics = function (string $date) {
                    $totalOrder   = Db::table('epay_data_model')->where('attrName','in',[
                        'order_total_count_3',
                        'order_total_count_2',
                        'order_total_count_1'
                    ])->whereBetweenTime('createTime', $date)->sum('data');
                    $successOrder = Db::table('epay_data_model')->where('attrName','in',[
                        'order_total_count_success_3',
                        'order_total_count_success_2',
                        'order_total_count_success_1'
                    ])->whereBetweenTime('createTime', $date)->sum('data');
                    if ($successOrder == 0 || $totalOrder == 0)
                        $ratio = '0';
                    else
                        $ratio = number_format($successOrder / $totalOrder * 100, 2);
                    return [
                        'totalOrder'   => $totalOrder,
                        'successOrder' => $successOrder,
                        'ratio'        => $ratio
                    ];
                };

                $data['orderDataStatistics'] = [];
                $createTimeList              = [
                    date('Y-m-d', strtotime('- 1 day')),
                    date('Y-m-d', strtotime('now'))
                ];
                foreach ($createTimeList as $time) {
                    $data['orderDataStatistics'][$time] = $buildOrderStatistics($time);
                }
            }
            $data['statistics'] = [
                'yesterday' => [
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_1')->whereTime('createTime', 'yesterday')->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_2')->whereTime('createTime', 'yesterday')->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_3')->whereTime('createTime', 'yesterday')->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_4')->whereTime('createTime', 'yesterday')->sum('data')
                    ]
                ],
                'today'     => [
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_1')->whereTime('createTime', 'today')->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_2')->whereTime('createTime', 'today')->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_3')->whereTime('createTime', 'today')->sum('data')
                    ],
                    [
                        'type'       => 1,
                        'totalMoney' => Db::table('epay_data_model')->where('attrName','money_total_4')->whereTime('createTime', 'today')->sum('data')
                    ]
                ]
            ];
            {
                $cacheOrderDataComparison = cache('orderDataComparison');
                if (empty($cacheOrderDataComparison)) {
                    $yesterday           = date('Y-m-d', strtotime('-1 day'));
                    $today               = date('Y-m-d', time());
                    $orderDataComparison = [
                        $yesterday => [],
                        $today     => []
                    ];
                    for ($i = 0; $i < 24; $i++) {
                        $o                                               = $i + 1;
                        $hoursStartStr                                   = ($i >= 10 ? $i . '' : '0' . $i) . ':00:00';
                        $hoursEndStr                                     = ($o >= 10 ? $o . '' : '0' . $o) . ':00:00';
                        $orderDataComparison[$yesterday][$hoursStartStr] = Db::table('epay_data_model')->where('attrName','in',[
                            'order_total_count_3',
                            'order_total_count_2',
                            'order_total_count_1'
                        ])
                            ->whereTime('createTime', '>=', $yesterday . ' ' . $hoursStartStr)
                            ->whereTime('createTime', '<=', $yesterday . ' ' . $hoursEndStr)->sum('data');
                        $orderDataComparison[$today][$hoursStartStr]     = Db::table('epay_data_model')->where('attrName','in',[
                            'order_total_count_3',
                            'order_total_count_2',
                            'order_total_count_1'
                        ])
                            ->whereTime('createTime', '>=', $today . ' ' . $hoursStartStr)
                            ->whereTime('createTime', '<=', $today . ' ' . $hoursEndStr)->sum('data');
                    }
                    $data['orderDataComparison'] = $orderDataComparison;
                    cache('orderDataComparison', json_encode($orderDataComparison), 600);
                } else {
                    $data['orderDataComparison'] = json_decode($cacheOrderDataComparison, true);
                }
            }
            //获取分时订单
            cache('DashboardData', json_encode($data), 600);
        }
    }

    private function delFailOrder()
    {
        Db::table('epay_order')->where('status', 0)->whereTime('createTime', '<=', '-2 day')->delete();
    }

}
