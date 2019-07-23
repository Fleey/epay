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

}
