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

        $nowHour = intval(date('H'));
        if ($nowHour == 4) {
            $output->info('start optimize mysql');
            $this->optimizeDatabase();
            $output->info('end optimize mysql');
        } else if ($nowHour == 0) {
            $output->info('start add cron record user balance data');
            $this->cronUserBalanceRecord();
            $output->info('end add cron record user balance data');

            $output->info('start add settle data');
            $this->addSettleData();
            $output->info('end add settle data');

            $output->info('start add cron Wxx Trade Record data');
            $this->cronWxxTradeRecord();
            $output->info('end add cron Wxx Trade Record data');
        }
        //凌晨4点执行本任务优化数据库
        $output->info('start cron Wx Api');
        $this->cronRequestWxApi();
        $output->info('end cron Wx Api');
    }

    private function cronUserBalanceRecord()
    {
        $userList = Db::table('epay_user')->field('id,balance')->cursor();
        $time     = getDateTime(true);
        foreach ($userList as $content) {
            Db::table('epay_user_data_model')->insertGetId([
                'uid'        => $content['id'],
                'data'       => $content['balance'],
                'attrName'   => 'moneyRecord',
                'createTime' => $time
            ]);
        }
    }

    private function cronRequestWxApi()
    {
        $accountList = Db::table('epay_wxx_account_list')->field('id,desc,appID,appSecret')->cursor();
        foreach ($accountList as $content) {
            $result = curl('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $content['appID'] . '&secret=' . $content['appSecret']);
            if ($result === false)
                continue;
            $result = json_decode($result, true);
            if (isset($result['errcode']) && isset($result['errmsg'])) {
                trace('[微信公众平台] 请求接口异常 id => ' . $content['id'] . ' desc => ' . $content['desc'] . ' errorCode => ' . $result['errcode'] . ' errorMsg => ' . $result['errmsg'], 'error');
                echo '[微信公众平台] 请求接口异常 id => ' . $content['id'] . ' desc => ' . $content['desc'] . ' errorCode => ' . $result['errcode'] . ' errorMsg => ' . $result['errmsg'] . PHP_EOL;
            }
        }
        //主要是防止说太久没用导致密匙失效
    }


    private function cronWxxTradeRecord()
    {
        $yesterday    = date('Y-m-d', strtotime('-1 day')) . ' 00:00:00';
        $wxxApplyList = Db::table('epay_wxx_apply_list')->field('subMchID,money')->cursor();
        foreach ($wxxApplyList as $wxxApplyInfo) {
            if (empty($wxxApplyInfo['money']))
                continue;
            $tradeMoney = $wxxApplyInfo['money'];
            Db::table('epay_wxx_apply_list')->where('subMchID', $wxxApplyInfo['subMchID'])->limit(1)->update([
                'money'     => 0,
                'rounds'    => 0,
                'tempMoney' => 0
            ]);
            Db::table('epay_wxx_trade_record')->insert([
                'subMchID'   => $wxxApplyInfo['subMchID'],
                'totalMoney' => $tradeMoney,
                'createTime' => $yesterday
            ]);
        }
    }

    private function addSettleData()
    {
        $userData       = Db::table('epay_user')->field('id,rate,clearMode')->cursor();
        $totalRateMoney = 0;
        foreach ($userData as $value) {
            $uid            = $value['id'];
            $rate           = $value['rate'] / 100;
            $totalMoney     = Db::table('epay_order')->where([
                'uid'      => $uid,
                'status'   => 1,
                'isShield' => 0
            ])->whereTime('endTime', 'yesterday')->sum('money');
            $totalRateMoney += $totalMoney * ($rate / 100);
        }
        $totalMoney = Db::table('epay_order')->where([
            'status' => 1
        ])->whereTime('endTime', 'yesterday')->sum('money');

        addServerLog(2, 3, 'System', date('Y-m-d', strtotime('- 1 day')) . ' 交易总金额=>' . ($totalMoney / 100) . ' 当日利润金额=>' . (($totalMoney - $totalRateMoney) / 100));
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
        $nowHour        = intval(date('H'));
        $lastSettleData = Db::table('epay_settle')->where('uid', $userData['userInfo']['id'])
            ->field('status,updateTime')->order('id desc')
            ->limit(1)->select();
        if (!empty($lastSettleData))
            if ($lastSettleData[0]['status'] == 0)
                return false;
        //查询到上一次有记录 并且状态尚未结算
        $userData['userInfo']['balance'] = intval($userData['userInfo']['balance'] / 10);
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
