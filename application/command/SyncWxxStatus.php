<?php

namespace app\command;

use app\admin\controller\Wxx;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class SyncWxxStatus extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('syncWxxStatus')->setDescription('sync get wechat xiao wei status');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        if(getServerConfig('isRunningSyncWxxApply') == 'true')
            return;
        setServerConfig('isRunningSyncWxxApply', 'true');
        $selectResult = Db::table('epay_wxx_apply_list')->where('status', 0)->whereOr('status', 1)->field('accountID,businessCode,id')->cursor();
        foreach ($selectResult as $data) {
            $wxxModel = Wxx::getWxxApiModel($data['accountID']);
            if ($wxxModel == null)
                continue;
            $statusResult = $wxxModel->applyStatus($data['businessCode']);
            if (!$statusResult['isSuccess'])
                continue;
            $updateData = [
                'status'   => 0,
                'desc'     => '',
                'subMchID' => 0
            ];

            switch ($statusResult['data']['applyState']) {
                case 'FROZEN':
                    $updateData['status'] = -2;
                    $updateData['desc']   = $statusResult['data']['applyStateDesc'];
                    break;
                case 'REJECTED':
                    $updateData['status']    = -1;
                    $updateData['desc']      = $statusResult['data']['applyStateDesc'];
                    $updateData['applyData'] = $statusResult['data']['auditDetail'];
                    break;
                case 'AUDITING':
                    $updateData['status'] = 0;
                    $updateData['desc']   = $statusResult['data']['applyStateDesc'];
                    break;
                case 'TO_BE_SIGNED':
                    $updateData['status']    = 1;
                    $updateData['desc']      = $statusResult['data']['applyStateDesc'];
                    $updateData['applyData'] = json_encode(['signUrl' => $statusResult['data']['signUrl']]);
                    $updateData['subMchID']  = $statusResult['data']['subMchId'];
                    break;
                case 'FINISH':
                    $updateData['status']   = 2;
                    $updateData['desc']     = $statusResult['data']['applyStateDesc'];
                    $updateData['subMchID'] = $statusResult['data']['subMchId'];
                    break;
            }
            Db::table('epay_wxx_apply_list')->where('id', $data['id'])->limit(1)->update($updateData);
        }
        setServerConfig('isRunningSyncWxxApply', 'false');
    }
}
