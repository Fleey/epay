<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

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
        $userData   = \think\Db::table('epay_user')->field('id,clearType,account,username,rate')->where([
            ['clearType', 'in', [1, 2, 3]],
            ['clearMode', '=', 0]
        ])->cursor();
        $settleTime = getDateTime();
        foreach ($userData as $value) {
            $orderMoney = \think\Db::table('epay_order')->where([
                'status'   => 1,
                'isShield' => 0,
                'uid'      => $value['id']
            ])->whereBetweenTime('endTime', 'yesterday')->sum('money');
            if ($orderMoney <= 0)
                continue;
            $orderMoney = $orderMoney * ($value['rate'] / 10000);
            \think\Db::table('epay_settle')->insert([
                'uid'        => $value['id'],
                'clearType'  => $value['clearType'],
                'addType'    => 1,
                'account'    => $value['account'],
                'username'   => $value['username'],
                'money'      => intval($orderMoney),
                'fee'        => 0,
                'status'     => 0,
                'createTime' => $settleTime
            ]);
        }
        $output->info('settle success');
    }
}
