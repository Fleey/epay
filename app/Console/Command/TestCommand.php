<?php


namespace App\Console\Command;

use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;

/**
 * Class TestCommand
 * @package App\Console\Command
 * @Command(desc="<green>测试专用命令</green>")
 */
class TestCommand
{
    /**
     * @CommandMapping(desc="<red>测试运行命令备注</red>")
     *
     * @param Input $input
     * @param Output $output
     */
    public function run(Input $input, Output $output): void
    {
        clogLog('test content', 'error', 'spiderLogger');
    }
}
