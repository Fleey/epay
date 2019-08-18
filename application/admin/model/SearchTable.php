<?php
/**
 * Created by Fleey.
 * User: Fleey
 * Date: 2018/7/16
 * Time: 9:31
 */

namespace app\admin\model;

use think\Db;
use think\db\Query;
use think\Exception;

class SearchTable
{
    private $searchTable;
    //直接查询表
    private $startSite;
    //开始搜索行位置
    private $getLength;
    //获取行数量
    private $columns;
    //行参数
    private $order;
    //排序参数
    private $draw;

    private $searchValue;

    private $args;
    //附加参数

    /**
     * SearchTable constructor.
     * @param string $searchTable
     * @param int $startSite
     * @param int $getLength
     * @param $order
     * @param $searchValue
     * @param array $args
     * @throws Exception
     */
    public function __construct(string $searchTable, int $startSite, int $getLength, $order, $searchValue, $args = [])
    {
        $tableList = [
            'epay_order',
            'epay_user',
            'epay_settle',
            'epay_user_money_log',
            'epay_log',
            'epay_ad_content',
            'epay_wxx_account_list',
            'epay_wxx_apply_info',
            'epay_wxx_apply_list'
        ];
        if (!in_array($searchTable, $tableList)) {
            throw new Exception('该表不存在', 404);
        }
        if ($getLength > 100) {
            $getLength = 100;
        }
        if ($startSite < 0) {
            $startSite = 0;
        }
        $this->searchTable = $searchTable;
        $this->startSite   = $startSite;
        $this->getLength   = $getLength;
        $this->order       = $order;
        $this->searchValue = $searchValue['value'];
        $this->args        = $args;
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getData()
    {
        $result = Db::table($this->searchTable);
        $result = $this->sortData($result);
        $result = $this->searchValue($result);
        $result = $this->searchArgs($result);

        $result = $result->limit($this->startSite, $this->getLength)->select();

        $recordsFiltered = Db::table($this->searchTable);
        $recordsFiltered = $this->searchArgs($recordsFiltered);
        $recordsFiltered = $this->searchValue($recordsFiltered);
        $recordsFiltered = $recordsFiltered->fetchSql(true)->select();
        $recordsFiltered = Db::query('explain ' . $recordsFiltered)[0]['rows'];

        if (empty($result)) {
            return [
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => 0,
                'data'            => [],
            ];
        }

        if ($this->searchTable == 'epay_wxx_apply_list') {
            foreach ($result as $key => $value) {
                $result[$key]['accountID'] = $value['appID'] . '-' . $value['desc'];
                unset($result[$key]['appID']);
                unset($result[$key]['desc']);
            }
        }

        $data = [];
        foreach ($result as $item) {
            $data1 = [];
            foreach ($item as $key => $item1) {
                if ($this->searchTable == 'epay_user')
                    if ($key == 'balance')
                        $item1 = intval($item1);
                if ($key == 'tradeNo')
                    $data1[] = (string)$item1;
                else
                    $data1[] = $item1;
            }
            $data[] = $data1;
        }
        return [
            'recordsTotal'    => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ];
    }

    /**
     * @param $queryResult \think\db\Query
     * @return \think\db\Query
     */
    public function searchArgs(Query $queryResult)
    {
        if (empty($this->args))
            return $queryResult;

        if ($this->searchTable == 'epay_order') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('uid', intval($this->args['uid']));
            if (isset($this->args['tradeNo']))
                $queryResult = $queryResult->where('tradeNo', $this->args['tradeNo']);
            if (isset($this->args['tradeNoOut']))
                $queryResult = $queryResult->where('tradeNoOut', $this->args['tradeNoOut']);
            if (isset($this->args['type']))
                $queryResult = $queryResult->where('type', $this->args['type']);
            if (isset($this->args['status']))
                $queryResult = $queryResult->where('status', $this->args['status']);
            if (isset($this->args['isShield']))
                $queryResult = $queryResult->where('isShield', $this->args['isShield']);
            if (isset($this->args['productMinPrice']))
                $queryResult = $queryResult->where('money', '>=', decimalsToInt($this->args['productMinPrice'], 2));
            if (isset($this->args['productMaxPrice']))
                $queryResult = $queryResult->where('money', '<=', decimalsToInt($this->args['productMaxPrice'], 2));
            if (isset($this->args['productName']))
                $queryResult = $queryResult->where('productName', 'like', '%' . $this->args['productName'] . '%');
            if (isset($this->args['productStartTime']))
                $queryResult = $queryResult->where('createTime', '>=', $this->args['productStartTime']);
            if (isset($this->args['productEndTime']))
                $queryResult = $queryResult->where('createTime', '<=', $this->args['productEndTime']);
        } else if ($this->searchTable == 'epay_user') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('epay_user.id', intval($this->args['uid']));
            if (isset($this->args['key']))
                $queryResult = $queryResult->where('epay_user.key', $this->args['key']);
            if (isset($this->args['account']))
                $queryResult = $queryResult->where('epay_user.account', $this->args['account']);
            if (isset($this->args['username']))
                $queryResult = $queryResult->where('epay_user.username', $this->args['username']);
            if (isset($this->args['email']))
                $queryResult = $queryResult->where('epay_user.email', $this->args['email']);
            if (isset($this->args['qq']))
                $queryResult = $queryResult->where('epay_user.qq', $this->args['qq']);
            if (isset($this->args['domain']))
                $queryResult = $queryResult->where('epay_user.domain', $this->args['domain']);
        } else if ($this->searchTable == 'epay_settle') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('uid', intval($this->args['uid']));
            if (isset($this->args['clearType']))
                $queryResult = $queryResult->where('clearType', $this->args['clearType']);
            if (isset($this->args['clearMode']))
                $queryResult = $queryResult->where('addType', $this->args['clearMode']);
            if (isset($this->args['account']))
                $queryResult = $queryResult->where('account', 'like', '%' . $this->args['account'] . '%');
            if (isset($this->args['username']))
                $queryResult = $queryResult->where('username', 'like', '%' . $this->args['username'] . '%');
            if (isset($this->args['minMoney']))
                $queryResult = $queryResult->where('money', '>=', decimalsToInt($this->args['minMoney'], 2));
            if (isset($this->args['maxMoney']))
                $queryResult = $queryResult->where('money', '<=', decimalsToInt($this->args['maxMoney'], 2));
            if (isset($this->args['status']))
                $queryResult = $queryResult->where('status', $this->args['status']);
        } else if ($this->searchTable == 'epay_user_money_log') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('uid', intval($this->args['uid']));
        } else if ($this->searchTable == 'epay_log') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('uid', intval($this->args['uid']));
            if (isset($this->args['type']))
                $queryResult = $queryResult->where('type', $this->args['type']);
            if (isset($this->args['ipv4']))
                $queryResult = $queryResult->where('ipv4=:ip', ['ip' => $this->args['ipv4']]);
            if (isset($this->args['data']))
                $queryResult = $queryResult->where('data', 'like', '%' . $this->args['data'] . '%');
        } else if ($this->searchTable == 'epay_wxx_account_list') {
            if (isset($this->args['appID']))
                $queryResult = $queryResult->where('appID', $this->args['appID']);
            if (isset($this->args['mchID']))
                $queryResult = $queryResult->where('mchID', $this->args['mchID']);
            if (isset($this->args['desc']))
                $queryResult = $queryResult->where('desc', 'like', '%' . $this->args['desc'] . '%');
        } else if ($this->searchTable == 'epay_wxx_apply_info') {
            if (isset($this->args['idCardName']))
                $queryResult = $queryResult->where('idCardName', 'like', '%' . $this->args['idCardName'] . '%');
            if (isset($this->args['idCardNumber']))
                $queryResult = $queryResult->where('idCardNumber', $this->args['idCardNumber']);
            if (isset($this->args['type']))
                $queryResult = $queryResult->where('type', $this->args['type']);
        } else if ($this->searchTable == 'epay_wxx_apply_list') {
            if (isset($this->args['applyInfoID']))
                $queryResult = $queryResult->where('epay_wxx_apply_list.applyInfoID', $this->args['applyInfoID']);
            if (isset($this->args['subMchID']))
                $queryResult = $queryResult->where('epay_wxx_apply_list.subMchID', $this->args['subMchID']);
            if (isset($this->args['type']))
                $queryResult = $queryResult->where('epay_wxx_apply_list.status', $this->args['type']);
            if (isset($this->args['desc']))
                $queryResult = $queryResult->where('epay_wxx_apply_list.desc', 'like', '%' . $this->args['desc'] . '%');

        }
        return $queryResult;
    }

    /**
     * @param $queryResult \think\db\Query
     * @return \think\db\Query
     */
    private function searchValue(Query $queryResult)
    {
        if (empty($this->searchValue))
            return $queryResult;

        $keyName = '';
        switch ($this->searchTable) {
            case 'epay_ad_content':
                $keyName = 'title';
                break;
            default:
                return $queryResult;
                break;
        }
        return $queryResult->where($keyName, 'like', '%' . $this->searchValue . '%');
    }

    /**
     * @param $queryResult \think\db\Query
     * @return \think\db\Query
     */
    private function sortData(Query $queryResult)
    {
        $searchOrderList = [];

        if ($this->searchTable == 'epay_order') {
            $searchOrderList = ['epay_order.tradeNo', 'epay_order.tradeNoOut', 'epay_order.productName', 'epay_order.money', 'epay_order.type', 'epay_order.status', 'epay_order.createTime'];
        } else if ($this->searchTable == 'epay_user') {
            $searchOrderList = ['epay_user.id', 'epay_user.key', 'epay_user.balance', 'epay_user.account', 'epay_user.username', 'IF(epay_wxx_apply_info.type is NULL,1,epay_wxx_apply_info.type) as type', 'epay_user.isBan'];
            $queryResult     = $queryResult->leftJoin('epay_wxx_apply_info', 'epay_user.id = epay_wxx_apply_info.uid');
            $queryResult     = $queryResult->group('epay_user.id');
        } else if ($this->searchTable == 'epay_settle') {
            $searchOrderList = ['id', 'uid', 'clearType', 'account', 'username', 'money', 'fee', 'status', 'createTime'];
        } else if ($this->searchTable == 'epay_user_money_log') {
            $searchOrderList = ['money', 'desc', 'createTime'];
        } else if ($this->searchTable == 'epay_log') {
            $searchOrderList = ['id', 'uid', 'type', 'ipv4', 'createTime', 'data'];
        } else if ($this->searchTable == 'epay_ad_content') {
            $searchOrderList = ['id', 'title', 'status', 'visitsCount', 'createTime'];
        } else if ($this->searchTable == 'epay_wxx_account_list') {
            $searchOrderList = ['id', 'appID', 'mchID', 'desc', 'createTime'];
        } else if ($this->searchTable == 'epay_wxx_apply_info') {
            $searchOrderList = ['id', 'idCardName', 'idCardNumber', 'type', 'createTime'];
        } else if ($this->searchTable == 'epay_wxx_apply_list') {
            $searchOrderList = ['epay_wxx_apply_list.id', 'epay_wxx_apply_list.accountID', 'epay_wxx_apply_list.money', 'epay_wxx_apply_list.subMchID', 'epay_wxx_apply_info.idCardName', 'epay_wxx_apply_list.status', 'epay_wxx_apply_list.createTime', 'epay_wxx_account_list.desc', 'epay_wxx_account_list.appID'];
            $queryResult     = $queryResult->leftJoin('epay_wxx_account_list', 'epay_wxx_apply_list.accountID = epay_wxx_account_list.id');
            $queryResult     = $queryResult->leftJoin('epay_wxx_apply_info', 'epay_wxx_apply_list.applyInfoID = epay_wxx_apply_info.id');
        }
        $field = '';
        foreach ($searchOrderList as $item) {
            $field .= $item . ',';
        }
        $field       = substr($field, 0, strlen($field) - 1);
        $queryResult = $queryResult->field($field);
        if (!empty($this->order)) {
            if ($this->order[0]['column'] > count($searchOrderList)) {
                $order = 'asc';
            } else {
                $order = $this->order[0]['dir'];
                if ($order != 'asc' && $order != 'desc') {
                    $order = 'asc';
                }
            }
            $queryResult = $queryResult->order($searchOrderList[$this->order[0]['column']], $order);
        }
        //塞排序进去
        return $queryResult;
    }

}