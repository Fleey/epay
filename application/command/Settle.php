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
        $output->info('start settle user');
        $this->settleUser();
        $output->info('end settle success');

        $nowHour = intval(date('h'));
        if ($nowHour == 4) {
            $output->info('start optimize mysql');
            $this->optimizeDatabase();
            $output->info('end optimize mysql');
        }
        //凌晨4点执行本任务
    }

    /**
     * 结算账户
     */
    private function settleUser()
    {
        $userData   = Db::table('epay_user')->field('id,clearType,clearMode,account,username,rate,balance')->cursor();
        $settleTime = getDateTime();
        foreach ($userData as $value) {
            $lastSettleData = Db::table('epay_settle')->where('uid', $value['id'])
                ->field('status,updateTime')->order('id desc')
                ->limit(1)->select();
            if (!empty($lastSettleData))
                if ($lastSettleData[0]['status'] == 0)
                    continue;
            //查询到上一次有记录 并且状态尚未结算
            $userSettleInfo = getPayUserAttr($value['id'], 'settleConfig');
            if (!empty($userSettleInfo)) {
                $userSettleInfo = unserialize($userSettleInfo);
            } else {
                $userSettleInfo = [];
            }
            //初始化用户结算配置

            $isSettle = $this->isSettle([
                'userInfo'     => $value,
                'settleConfig' => $userSettleInfo
            ]);
//                $orderMoney = Db::table('epay_order')->where([
//                    'status'   => 1,
//                    'isShield' => 0,
//                    'uid'      => $value['id']
//                ])->whereBetweenTime('endTime', 'yesterday')->sum('money');
//                $orderMoney = intval($orderMoney * ($value['rate'] / 10000));
//                if ($orderMoney <= 0)
//                    continue;
//                Db::table('epay_settle')->insert([
//                    'uid'        => $value['id'],
//                    'clearType'  => $value['clearType'],
//                    'addType'    => 1,
//                    'account'    => $value['account'],
//                    'username'   => $value['username'],
//                    'money'      => $orderMoney,
//                    'fee'        => 0,
//                    'status'     => 0,
//                    'createTime' => $settleTime
//                ]);

            if ($isSettle) {
                $settleFee = 0;
                //结算手续费

                if (isset($userSettleInfo['settleFee']))
                    $settleFee = $userSettleInfo['settleFee'];
                //置入结算手续费
                Db::table('epay_settle')->insert([
                    'uid'        => $value['id'],
                    'clearType'  => $value['clearType'],
                    'addType'    => 1,
                    'account'    => $value['account'],
                    'username'   => $value['username'],
                    'money'      => $value['balance'] / 10,
                    'fee'        => $settleFee / 10,
                    'status'     => 0,
                    'createTime' => $settleTime
                ]);
                echo '[' . date('Y-m-d h:m:s') . ']成功结算了这位弟弟 uid => ' . $value['id'] . ' ' . PHP_EOL;
            }
        }
    }

    /**
     * 判断是否需要结算
     * @param array $userData
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function isSettle(array $userData)
    {
        $nowHour        = intval(date('h'));
        $lastSettleData = Db::table('epay_settle')->where('uid', $userData['userInfo']['id'])
            ->field('status,updateTime')->order('id desc')
            ->limit(1)->select();
        if (!empty($lastSettleData))
            if ($lastSettleData[0]['status'] == 0)
                return false;
        //查询到上一次有记录 并且状态尚未结算
        if ($userData['userInfo']['balance'] <= 0)
            return false;
        //用户余额为正数不进行结算
        if ($userData['userInfo']['clearMode'] == 0) {
            if ($nowHour != 0)
                return false;
            //如果不为凌晨
        } else if ($userData['userInfo']['clearMode'] == 3) {
            if (!empty($lastSettleData)) {
                $needUpdateTime = strtotime('+ ' . $userData['settleConfig']['settleHour'] . ' hours', strtotime($lastSettleData[0]['updateTime']));
                if ($needUpdateTime >= time())
                    return false;
            }
        } else if ($userData['userInfo']['clearMode'] == 2 || $userData['userInfo']['clearMode'] == 1) {
            return false;
            //自动结算或手动结算用户不作处理
        }
        return true;
    }

    private function optimizeDatabase()
    {
        $getTables = Db::query('show tables;');
        $tempArray = [];
        if (!empty($getTables)) {
            foreach ($getTables as $row) {
                foreach ($row as $col) {
                    $tempArray[] = $col;
                }
            }
        }
        $getTables = $tempArray;
        $tempArray = [];
        foreach ($getTables as $tableName) {
            Db::execute('OPTIMIZE TABLE `' . $tableName . '`');
            Db::execute('ALTER TABLE `' . $tableName . '` ENGINE = InnoDB;');
        }
        //优化表
    }
}
