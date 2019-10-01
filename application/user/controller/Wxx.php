<?php

namespace app\user\controller;

use app\admin\model\FileModel;
use app\user\model\WxxApiV1Model;
use think\App;
use think\Controller;
use think\Db;
use think\Exception;

class Wxx extends Controller
{
    private $uid = 0;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $uid = session('uid', '', 'user');
        if (empty($uid))
            exit(json_encode(['status' => 0, 'msg' => '您需要登录后才能操作']));
        $this->uid = $uid;
    }

    public function postSearchBankName()
    {
        $title = input('post.title/s');
        $page  = input('post.page/d');

        $selectResult = Db::table('epay_wxx_search_content')->where('type', 1);
        $totalResult  = Db::table('epay_wxx_search_content')->where('type', 1);
        if (!empty($title)) {
            $selectResult = $selectResult->where('content', 'like', '%' . $title . '%');
            $totalResult  = $totalResult->where('content', 'like', '%' . $title . '%');
        }

        $selectResult = $selectResult->page($page, 10)->field('id,content as text')->select();
        $totalResult  = $totalResult->count('id');
        return json([
            'results'    => $selectResult,
            'totalCount' => ceil($totalResult / 10)
        ]);
    }

    public function postSearchArea()
    {
        $title    = input('post.title/s');
        $page     = input('post.page/d');
        $parentID = input('post.parentID/d', 0);

        $selectResult = Db::table('epay_wxx_area_list')->where('parentID', $parentID);
        $totalResult  = Db::table('epay_wxx_area_list')->where('parentID', $parentID);

        if (!empty($title)) {
            $selectResult = $selectResult->where('areaName', 'like', '%' . $title . '%');
            $totalResult  = $totalResult->where('areaName', 'like', '%' . $title . '%');
        }

        $selectResult = $selectResult->page($page, 10)->field('areaID as id,areaName as text')->select();
        $totalResult  = $totalResult->count('id');
        return json([
            'results'    => $selectResult,
            'totalCount' => ceil($totalResult / 10)
        ]);
    }

    public function postSearchIDCardName()
    {
        $title = input('post.title/s');
        $page  = input('post.page/d');

        $selectResult = Db::table('epay_wxx_apply_info');
        $totalResult  = Db::table('epay_wxx_apply_info');
        if (!empty($title)) {
            $selectResult = $selectResult->where('idCardName', 'like', '%' . $title . '%');
            $totalResult  = $totalResult->where('idCardName', 'like', '%' . $title . '%');
        }

        $selectResult = $selectResult->page($page, 10)->field('id,idCardName as text')->select();
        $totalResult  = $totalResult->count('id');
        return json([
            'results'    => $selectResult,
            'totalCount' => ceil($totalResult / 10)
        ]);
    }

    public function postSearchAccountID()
    {
        $title   = input('post.title/s');
        $page    = input('post.page/d');
        $applyID = input('post.applyID/d');

        $selectAccountList = Db::table('epay_wxx_apply_list')->where('applyInfoID', $applyID)->field('accountID')->select();
        $tempAccountList   = [];
        if (!empty($selectAccountList)) {
            foreach ($selectAccountList as $value)
                $tempAccountList[] = $value['accountID'];
        }


        $selectResult = Db::table('epay_wxx_account_list')->whereNotIn('id', $tempAccountList);
        $totalResult  = Db::table('epay_wxx_account_list')->whereNotIn('id', $tempAccountList);
        if (!empty($title)) {
            $selectResult = $selectResult->where('desc', 'like', '%' . $title . '%')->whereOr('appID', 'like', '%' . $title . '%');
            $totalResult  = $totalResult->where('desc', 'like', '%' . $title . '%')->whereOr('appID', 'like', '%' . $title . '%');
        }

        $selectResult = $selectResult->page($page, 10)->field('id,desc,appID')->select();
        $totalResult  = $totalResult->count('id');

        $tempResult = [];

        foreach ($selectResult as $value) {
            $tempResult[] = [
                'id'   => $value['id'],
                'text' => $value['appID'] . '-' . $value['desc']
            ];
        }

        return json([
            'results'    => $tempResult,
            'totalCount' => ceil($totalResult / 10)
        ]);
    }

    public function postApplyInfo()
    {
        $idCardCopy        = input('post.idCardCopy/d', 0);
        $idCardNational    = input('post.idCardNational/d', 0);
        $idCardName        = input('post.idCardName/s', '');
        $idCardNumber      = input('post.idCardNumber/s', '');
        $idCardValidTime   = input('post.idCardValidTime/s', '');
        $accountName       = input('post.accountName/s', '');
        $accountBank       = input('post.accountBank/s', '');
        $bankAddressCode   = input('post.bankAddressCode/s', '');
        $bankName          = input('post.bankName/s', '');
        $accountNumber     = input('post.accountNumber/s', '');
        $merchantShortName = input('post.merchantShortName/s', '');
        $servicePhone      = input('post.servicePhone/s', '');
        $productDesc       = input('post.productDesc/s', '');
        $contact           = input('post.contact/s', '');
        $contactPhone      = input('post.contactPhone/s', '');

        $act = input('post.act/s');
        $id  = input('post.id/s');

        if ($act != 'add' && $act != 'update')
            return json(['status' => 0, 'msg' => '请求状态有误，请重试。']);

        if (empty($idCardName) || empty($idCardNumber))
            return json(['status' => 0, 'msg' => '身份证或身份证号码不能为空']);

        if ($bankName == '请选择开户支行全称')
            $bankName = '';

        if ($act == 'add') {
            $selectResult = Db::table('epay_user_wxx_apply_info')->where('idCardName', $idCardName)
                ->whereOr('idCardNumber', $idCardNumber)->limit(1)->field('id')->select();
            if (!empty($selectResult))
                return json(['status' => 0, 'msg' => '身份证名或身份证号码已经存在，无法新增信息。']);

            $insertResult = Db::table('epay_user_wxx_apply_info')->insertGetId([
                'uid'               => $this->uid,
                'idCardCopy'        => $idCardCopy,
                'idCardNational'    => $idCardNational,
                'idCardName'        => $idCardName,
                'idCardNumber'      => $idCardNumber,
                'idCardValidTime'   => $idCardValidTime,
                'accountName'       => $accountName,
                'accountBank'       => $accountBank,
                'bankAddressCode'   => $bankAddressCode,
                'bankName'          => $bankName,
                'accountNumber'     => $accountNumber,
                'merchantShortName' => $merchantShortName,
                'servicePhone'      => $servicePhone,
                'productDesc'       => $productDesc,
                'contact'           => $contact,
                'contactPhone'      => $contactPhone,
                'status'            => 0,
                'createTime'        => getDateTime()
            ]);
            //设置预留金额
            if (!$insertResult)
                return json(['status' => 0, 'msg' => '新增用户信息失败，数据库异常，请重试。']);
            return json(['status' => 1, 'msg' => '新增用户信息成功']);
        }

        $applyInfoData = Db::table('epay_user_wxx_apply_info')->where('id', $id)->limit(1)->field('id')->select();

        if (empty($applyInfoData))
            return json(['status' => 0, 'msg' => '数据不存在，请刷新页面后再试']);


        Db::table('epay_user_wxx_apply_info')->where('id', $id)->update([
            'uid'               => $this->uid,
            'idCardCopy'        => $idCardCopy,
            'idCardNational'    => $idCardNational,
            'idCardName'        => $idCardName,
            'idCardNumber'      => $idCardNumber,
            'idCardValidTime'   => $idCardValidTime,
            'accountName'       => $accountName,
            'accountBank'       => $accountBank,
            'bankAddressCode'   => $bankAddressCode,
            'bankName'          => $bankName,
            'accountNumber'     => $accountNumber,
            'merchantShortName' => $merchantShortName,
            'servicePhone'      => $servicePhone,
            'productDesc'       => $productDesc,
            'contact'           => $contact,
            'contactPhone'      => $contactPhone,
            'status'            => 0,
            'updateTime'        => getDateTime()
        ]);
        return json(['status' => 1, 'msg' => '更新用户信息成功']);
    }

    /**
     * 获取微信申请信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getApplyInfo()
    {
        $id = input('get.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        $searchResult = Db::table('epay_user_wxx_apply_info')->where('id', $id)->where('uid', $this->uid)->limit(1)->select();
        if (empty($searchResult))
            return json(['status' => 0, 'msg' => '查询记录已经不存在，请刷新页面后重试。']);

        $returnData                           = $searchResult[0];
        $returnData['idCardCopyFilePath']     = FileModel::getFilePath($returnData['idCardCopy'], true);
        $returnData['idCardNationalFilePath'] = FileModel::getFilePath($returnData['idCardNational'], true);

        if (!empty($returnData['bankAddressCode'])) {
            $tempAreaData = [];
            do {
                if (empty($tempAreaData))
                    $searchAreaID = $returnData['bankAddressCode'];
                else
                    $searchAreaID = $tempAreaData[count($tempAreaData) - 1]['parentID'];
                $areaSearch = Db::table('epay_wxx_area_list')->where('areaID', $searchAreaID)->limit(1)->field('areaID,parentID,areaName')->select();

                $tempAreaData[] = $areaSearch[0];
            } while ($tempAreaData[count($tempAreaData) - 1]['parentID'] != 0);

            $returnData['bankAddressAreaData'] = $tempAreaData;
        } else {
            $returnData['bankAddressAreaData'] = [];
        }

        if (isset($returnData['uid']))
            $returnData['reservedMoney'] = getPayUserAttr($returnData['uid'], 'reservedMoney');
        else
            $returnData['reservedMoney'] = '0';

        return json(['status' => 1, 'data' => $returnData]);
    }


    /**
     * 获取申请列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWxxApplyList()
    {
        $limit = 15;
        $page  = input('get.page/d');

        $result = Db::table('epay_user_wxx_apply_info')->order('id desc')->where('uid', $this->uid)->field('id,idCardName,idCardNumber,status,createTime')->page($page, $limit)->select();
        if (empty($result))
            $result = [];

        return json(['status' => 1, 'data' => $result]);
    }
}