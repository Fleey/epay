<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class DeleteRecord extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('deleteRecord')->setDescription('delete order and settle record');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $deleteTime = '- 15 day';
        //delete 15 day before data
        $data = Db::table('epay_order')->whereTime('createTime', '<=', '- 3 day')->where('status', 0)->field('tradeNo')->cursor();
        foreach ($data as $content) {
            Db::table('epay_order_attr')->where('tradeNo', $content['tradeNo'])->delete();
        }
        Db::table('epay_order')->whereTime('createTime', '<=', '- 3 day')->where('status', 0)->delete();
        Db::table('epay_order')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_order_attr')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_settle')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_log')->whereTime('createTime', '<=', $deleteTime)->delete();
        Db::table('epay_user_money_log')->whereTime('createTime', '<=', $deleteTime)->delete();
        $output->info('delete data success');
    }
}
