<?php
/**
 * Created by Fleey.
 * User: Fleey
 * Date: 2018/7/16
 * Time: 9:31
 */

namespace app\admin\model;

use think\db\Query;
use think\db\Where;
use think\Exception;

class SearchTable
{
    private $mysql;
    //数据库句柄
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
     * @param $mysql \think\db\Query
     * @param string $searchTable
     * @param int $startSite
     * @param int $getLength
     * @param $order
     * @param $searchValue
     * @param array $args
     * @throws Exception
     */
    public function __construct(Query $mysql, string $searchTable, int $startSite, int $getLength, $order, $searchValue, $args = [])
    {
        if (!$mysql) {
            throw new Exception('数据库句柄异常', 500);
        }
        $tableList = [
            'epay_order',
            'epay_user',
            'epay_settle'
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
        $this->mysql       = $mysql;
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
        $mysql  = $this->mysql;
        $result = $mysql->table($this->searchTable);
        $result = $this->sortData($result);
        $result = $this->searchValue($result);
        $result = $this->searchArgs($result);
        $result = $result->limit($this->startSite, $this->getLength)->select();

        $recordsFiltered = db()->table($this->searchTable);
        $recordsFiltered = $this->searchArgs($recordsFiltered);
        $recordsFiltered = $this->searchValue($recordsFiltered);
        $recordsFiltered = $recordsFiltered->count();

        if (empty($result)) {
            return [
                'recordsTotal'    => $recordsFiltered,
                'recordsFiltered' => 0,
                'data'            => [],
            ];
        }

        $data = [];
        foreach ($result as $item) {
            $data1 = [];
            foreach ($item as $key => $item1) {
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
                $queryResult = $queryResult->where('uid', $this->args['uid']);
            if (isset($this->args['tradeNo']))
                $queryResult = $queryResult->where('tradeNo', $this->args['tradeNo']);
            if (isset($this->args['tradeNoOut']))
                $queryResult = $queryResult->where('tradeNoOut', $this->args['tradeNoOut']);
            if (isset($this->args['type']))
                $queryResult = $queryResult->where('type', $this->args['type']);
            if (isset($this->args['status']))
                $queryResult = $queryResult->where('status', $this->args['status']);
            if (isset($this->args['productMinPrice']))
                $queryResult = $queryResult->where('money', '>=', decimalsToInt($this->args['productMinPrice'], 2));
            if (isset($this->args['productMaxPrice']))
                $queryResult = $queryResult->where('money', '<=', decimalsToInt($this->args['productMaxPrice'], 2));
            if (isset($this->args['productName']))
                $queryResult = $queryResult->where('productName', 'like', '%' . $this->args['productName'] . '%');
        } else if ($this->searchTable == 'epay_user') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('id', $this->args['uid']);
            if (isset($this->args['key']))
                $queryResult = $queryResult->where('key', $this->args['key']);
            if (isset($this->args['account']))
                $queryResult = $queryResult->where('account', $this->args['account']);
            if (isset($this->args['username']))
                $queryResult = $queryResult->where('username', $this->args['username']);
            if (isset($this->args['email']))
                $queryResult = $queryResult->where('email', $this->args['email']);
            if (isset($this->args['qq']))
                $queryResult = $queryResult->where('qq', $this->args['qq']);
            if (isset($this->args['domain']))
                $queryResult = $queryResult->where('domain', $this->args['domain']);
        } else if ($this->searchTable == 'epay_settle') {
            if (isset($this->args['uid']))
                $queryResult = $queryResult->where('uid', $this->args['uid']);
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
            $searchOrderList = ['tradeNo', 'tradeNoOut', 'productName', 'money', 'type', 'status', 'createTime'];
        } else if ($this->searchTable == 'epay_user') {
            $searchOrderList = ['id', 'key', 'balance', 'account', 'username', 'isBan'];
        } else if ($this->searchTable == 'epay_settle') {
            $searchOrderList = ['id', 'uid', 'clearType', 'account', 'username', 'money', 'status', 'createTime'];
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