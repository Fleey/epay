<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Settle extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('settle')->setDescription('settle user money');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $userData   = Db::table('epay_user')->field('id,clearType,account,username,rate')->where([
            ['clearMode', '=', 0]
        ])->cursor();
        $settleTime = getDateTime();
        foreach ($userData as $value) {
            $orderMoney = Db::table('epay_order')->where([
                'status'   => 1,
                'isShield' => 0,
                'uid'      => $value['id']
            ])->whereBetweenTime('endTime', 'yesterday')->sum('money');
            $orderMoney = intval($orderMoney * ($value['rate'] / 10000));
            if ($orderMoney <= 0)
                continue;
            Db::table('epay_settle')->insert([
                'uid'        => $value['id'],
                'clearType'  => $value['clearType'],
                'addType'    => 1,
                'account'    => $value['account'],
                'username'   => $value['username'],
                'money'      => $orderMoney,
                'fee'        => 0,
                'status'     => 0,
                'createTime' => $settleTime
            ]);
        }
        $output->info('settle success');
    }
}
