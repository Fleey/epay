<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class ConverTable extends Command
{
    /* @var $mysql \think\db\Query */
    private $mysql;


    protected function configure()
    {
        // 指令配置
        $this->setName('converTable')->setDescription('conver table');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $this->mysql = db();

        if ($this->isExistTable('pay_user')) {
            $this->converPayUser();
            $output->info('conver pay_user table success');
        }
        //conver pay_user
        if ($this->isExistTable('pay_settle')) {
            $this->converPaySettle();
            $output->info('conver pay_settle table success');
        }
        //conver pay_settle
        if ($this->isExistTable('pay_order')) {
            $output->info('start conver pay_order table');
            $this->converPayOrder();
            $output->info('conver pay_order table success');
        }
    }

    private function converPayOrder()
    {
        $this->mysql->table('pay_order')->chunk(1000, function ($dataList) {
            foreach ($dataList as $value) {
                $this->mysql->table('epay_order')->insert([
                    'id'          => $value['id'],
                    'uid'         => $value['pid'],
                    'tradeNo'     => $value['trade_no'],
                    'tradeNoOut'  => $value['out_trade_no'],
                    'notify_url'  => $value['notify_url'],
                    'return_url'  => $value['return_url'],
                    'money'       => decimalsToInt($value['money'], 2),
                    'type'        => $this->converPayName($value['type']),
                    'productName' => $value['name'],
                    'ipv4'        => $value['ip'],
                    'status'      => $value['status'],
                    'createTime'  => $value['addtime'],
                    'endTime'     => $value['endtime']
                ]);
            }
            echo 'you need await,maybe data too long.' . PHP_EOL;
        });
    }

    private function converPaySettle()
    {
        $dataList = $this->mysql->table('pay_settle')->select();
        foreach ($dataList as $value) {
            $this->mysql->table('epay_settle')->insert([
                'id'         => $value['id'],
                'uid'        => $value['pid'],
                'addType'    => 1,
                'clearType'  => $this->converPayType($value['type']),
                'account'    => $value['account'],
                'username'   => $value['username'],
                'money'      => decimalsToInt($value['money'], 2),
                'fee'        => 0,
                'status'     => $value['status'],
                'createTime' => $value['time']
            ]);
        }
    }

    private function converPayUser()
    {
        $dataList = $this->mysql->table('pay_user')->select();
        foreach ($dataList as $value) {
            $this->mysql->table('epay_user')->insert([
                'id'         => $value['id'],
                'key'        => $value['key'],
                'rate'       => decimalsToInt($value['rate'], 2),
                'account'    => $value['account'],
                'username'   => $value['username'],
                'balance'    => decimalsToInt($value['money'], 3),
                'email'      => $value['email'],
                'phone'      => $value['phone'],
                'qq'         => $value['qq'],
                'domain'     => $value['url'],
                'clearType'  => $this->converPayType($value['settle_id']),
                'isApply'    => $value['apply'],
                'isClear'    => $value['type'],
                'isBan'      => $value['active'] ? 0 : 1,
                'createTime' => getDateTime()
            ]);
        }
    }

    private function isExistTable(string $tableName)
    {
        return !empty($this->mysql->query('show tables like "' . $tableName . '";'));
    }

    private function converPayName($payName)
    {
        switch ($payName) {
            case 'wxpay':
                $payName = 1;
                break;
            case 'alipay':
                $payName = 3;
                break;
            case 'qqpay':
                $payName = 2;
                break;
            default:
                $payName = 0;
                break;
        }
        return $payName;
    }

    private function converPayType($settleType)
    {
        switch ($settleType) {
            case '1':
                $settleType = 3;
                break;
            case '2':
                $settleType = 2;
                break;
            case '4':
                $settleType = 1;
                break;
            default:
                $settleType = 0;
                break;
        }
        return $settleType;
    }
}
