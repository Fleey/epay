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
        Db::table('epay_order')->whereTime('createTime', '<=', '- 15 day')->delete();
        Db::table('epay_settle')->whereTime('createTime', '<=', '- 15 day')->delete();
        $output->info('settle success');
    }
}
