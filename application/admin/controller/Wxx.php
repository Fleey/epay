<?php

namespace app\admin\controller;

use app\admin\model\FileModel;
use app\user\model\WxxApiV1Model;
use think\App;
use think\Controller;
use think\Db;
use think\Exception;

class Wxx extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if ($this->request->action() != 'wxopenverify') {
            $username = session('username', '', 'admin');
            if (empty($username))
                exit(json_encode(['status' => 0, 'msg' => '您需要登录后才能操作']));
        }
    }

    /**
     * 公众号验证专用
     * @param string $code
     * @return string
     */
    public function WxOpenVerify(string $code)
    {
        return response($code)->header(['Content-Type' => 'text/plain']);
    }

    public function getAccount()
    {
        $id = input('get.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);

        $searchResult = Db::table('epay_wxx_account_list')->where('id', $id)->limit(1)->select();
        if (empty($searchResult))
            return json(['status' => 0, 'msg' => '查询记录已经不存在，请刷新页面后重试。']);

        $returnData            = $searchResult[0];
        $returnData['apiCert'] = file_get_contents(FileModel::getFilePath($returnData['apiCertID']));
        $returnData['apiKey']  = file_get_contents(FileModel::getFilePath($returnData['apiKeyID']));
        unset($returnData['apiCertID']);
        unset($returnData['apiKeyID']);
        return json(['status' => 1, 'data' => $returnData]);
    }

    public function getApplyInfo()
    {
        $id = input('get.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        $searchResult = Db::table('epay_wxx_apply_info')->where('id', $id)->limit(1)->select();
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

        return json(['status' => 1, 'data' => $returnData]);
    }

    public function getApplyList()
    {
        $id = input('get.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        $selectResult = Db::table('epay_wxx_apply_list')->where('id', $id)->limit(1)->select();
        if (empty($selectResult))
            return json(['status' => 0, 'msg' => '查询记录已经不存在，请刷新页面后重试。']);

        $returnData                = $selectResult[0];
        $returnData['idCardName']  = '未知姓名';
        $returnData['accountName'] = '未知服务商名称';

        $selectResult = Db::table('epay_wxx_apply_info')->where('id', $returnData['applyInfoID'])->field('idCardName')->limit(1)->select();
        if (!empty($selectResult))
            $returnData['idCardName'] = $selectResult[0]['idCardName'];

        $selectResult = Db::table('epay_wxx_account_list')->where('id', $returnData['accountID'])->field('appID,desc')->limit(1)->select();
        if (!empty($selectResult))
            $returnData['accountName'] = $selectResult[0]['appID'] . '-' . $selectResult[0]['desc'];

        return json(['status' => 1, 'data' => $returnData]);
    }

    public function postDeleteApplyList()
    {
        $id = input('post.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        $result = Db::table('epay_wxx_apply_list')->where('id', $id)->limit(1)->delete();
        return json(['status' => $result, 'msg' => $result ? '删除成功' : '删除失败']);
    }

    public function postDeleteApplyInfo()
    {
        $id = input('post.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        $result = Db::table('epay_wxx_apply_info')->where('id', $id)->limit(1)->delete();
        return json(['status' => $result, 'msg' => $result ? '删除成功' : '删除失败']);
    }

    public function postDeleteAccount()
    {
        $id = input('post.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        $result = Db::table('epay_wxx_account_list')->where('id', $id)->limit(1)->delete();
        return json(['status' => $result, 'msg' => $result ? '删除成功' : '删除失败']);
    }

    public function postAccount()
    {
        $apiKey  = input('post.apiKey/s');
        $apiCert = input('post.apiCert/s');

        $appID     = input('post.appID/s');
        $mchID     = input('post.mchID/s');
        $appKey    = input('post.appKey/s');
        $appSecret = input('post.appSecret/s');

        $desc = input('post.desc/s', '');

        $act = input('post.act/s');
        $id  = input('post.id/d');

        if ($act != 'add' && $act != 'update')
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        if ($act == 'update' && empty($id))
            return json(['status' => 0, 'msg' => '请求参数有误，请重试。']);
        if (empty($appKey))
            return json(['status' => 0, 'msg' => 'appKey 不能为空']);
        if (strlen($appKey) != 32)
            return json(['status' => 0, 'msg' => 'appKey 格式不正确']);
        if (empty($appSecret))
            return json(['status' => 0, 'msg' => 'appSecret 不能为空']);
        if (strlen($appSecret) != 32)
            return json(['status' => 0, 'msg' => 'appSecret 格式不正确']);
        if (empty($mchID))
            return json(['status' => 0, 'msg' => 'mchID 不能为空']);
        if (strlen($mchID) != 10)
            return json(['status' => 0, 'msg' => 'mchID 格式不正确']);
        if (empty($appID))
            return json(['status' => 0, 'msg' => 'appID 不能为空']);
        if (strlen($appID) != 18)
            return json(['status' => 0, 'msg' => 'appID 格式不正确']);

        if (mb_strlen($desc) > 100)
            return json(['status' => 0, 'msg' => '备注信息不能超过100个字符']);

        if (empty($apiKey))
            return json(['status' => 0, 'msg' => 'apiKey 不能为空']);
        if (empty($apiCert))
            return json(['status' => 0, 'msg' => 'apiCert 不能为空']);

        $apiKey  = FileModel::saveString($apiKey, 'pem');
        $apiCert = FileModel::saveString($apiCert, 'pem');

        if ($act == 'add') {
            $insertResult = Db::table('epay_wxx_account_list')->insertGetId([
                'apiCertID'  => $apiCert,
                'apiKeyID'   => $apiKey,
                'appID'      => $appID,
                'mchID'      => $mchID,
                'appKey'     => $appKey,
                'appSecret'  => $appSecret,
                'desc'       => $desc,
                'createTime' => getDateTime()
            ]);
            if (!$insertResult)
                return json(['status' => 0, 'msg' => '插入数据库异常,请重试']);
        } else {
            $updateResult = Db::table('epay_wxx_account_list')->where('id', $id)->update([
                'apiCertID' => $apiCert,
                'apiKeyID'  => $apiKey,
                'appID'     => $appID,
                'mchID'     => $mchID,
                'appKey'    => $appKey,
                'appSecret' => $appSecret,
                'desc'      => $desc,
            ]);
            if (!$updateResult)
                return json(['status' => 0, 'msg' => '更新失败，有可能你啥都没改']);
        }
        return json(['status' => 1, 'msg' => '操作成功']);
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
        $uid               = input('post.uid/s', 0);
        $type              = input('post.type/d', 0);
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
        $storeName         = '门店名称';
        $storeAddressCode  = '门店省市编码';
        $storeStreet       = '门店街道名称';
        $storeEntrancePic  = '门店门口照片';
        $indoorPic         = '门店内部照片';
        $merchantShortName = input('post.merchantShortName/s', '');
        $servicePhone      = input('post.servicePhone/s', '');
        $productDesc       = input('post.productDesc/s', '');
        $rate              = input('post.rate/s', '');
        $contact           = input('post.contact/s', '');
        $contactPhone      = input('post.contactPhone/s', '');

        $act = input('post.act/s');
        $id  = input('post.id/s');

        if ($act != 'add' && $act != 'update')
            return json(['status' => 0, 'msg' => '请求状态有误，请重试。']);

        if (empty($idCardName) || empty($idCardNumber))
            return json(['status' => 0, 'msg' => '身份证或身份证号码不能为空']);

        if ($act == 'add') {
            $selectResult = Db::table('epay_wxx_apply_info')->where('idCardName', $idCardName)
                ->whereOr('idCardNumber', $idCardNumber)->limit(1)->field('id')->select();
            if (!empty($selectResult))
                return json(['status' => 0, 'msg' => '身份证名或身份证号码已经存在，无法新增信息。']);

            $insertResult = Db::table('epay_wxx_apply_info')->insertGetId([
                'uid'               => $uid,
                'type'              => $type,
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
                'rate'              => $rate,
                'contact'           => $contact,
                'contactPhone'      => $contactPhone,
                'createTime'        => getDateTime()
            ]);
            if (!$insertResult)
                return json(['status' => 0, 'msg' => '新增用户信息失败，数据库异常，请重试。']);
            return json(['status' => 1, 'msg' => '新增用户信息成功']);
        }

        $updateResult = Db::table('epay_wxx_apply_info')->where('id', $id)->update([
            'uid'               => $uid,
            'type'              => $type,
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
            'rate'              => $rate,
            'contact'           => $contact,
            'contactPhone'      => $contactPhone,
        ]);
        if (!$updateResult)
            return json(['status' => 0, 'msg' => '更新用户信息失败，数据库异常，请重试。']);
        return json(['status' => 1, 'msg' => '更新用户信息成功']);
    }

    public function postApplyList()
    {
        $applyInfoID = input('post.applyInfoID/d');
        $accountIDs  = input('post.accountIDs/s');

        if (empty($applyInfoID) || empty($accountIDs))
            return json(['status' => 0, 'msg' => '请求参数不能为空']);
        $accountIDs = json_decode($accountIDs, true);
        if (empty($accountIDs))
            return json(['status' => 0, 'msg' => '请求服务号信息错误，请重试。']);
        $applyInfo = Db::table('epay_wxx_apply_info')->where('id', $applyInfoID)->limit(1)->select();
        if (empty($applyInfo))
            return json(['status' => 0, 'msg' => '申请账号信息不存在，请刷新页面后重试。']);
        $applyInfo = $applyInfo[0];

        ini_set('max_execution_time', '0');
        //设置不php不超时
        $storeEntrance = [env('ROOT_PATH') . '/public/static/images/storeEntrancePic.jpg'];
        $indoorPic     = [env('ROOT_PATH') . '/public/static/images/indoorPic.png'];
        //default img
        $idCardCopyFile     = FileModel::getFilePath($applyInfo['idCardCopy']);
        $idCardNationalFile = FileModel::getFilePath($applyInfo['idCardNational']);

        foreach ($accountIDs as $accountID) {
            $wxxModel = $this->getWxxApiModel($accountID);
            if ($wxxModel == null)
                return json(['status' => 0, 'msg' => '获取服务商号信息异常，AccountID => ' . $accountID]);

            $idCardCopyReBuildFilePath     = $this->rebuildImage($idCardCopyFile, env('ROOT_PATH') . '/runtime/temp');
            $idCardNationalReBuildFilePath = $this->rebuildImage($idCardNationalFile, env('ROOT_PATH') . '/runtime/temp');

            $idCardNational = $wxxModel->uploadMedia($idCardNationalReBuildFilePath);
            unlink($idCardNationalReBuildFilePath);
            if (!$idCardNational['isSuccess'])
                return json(['status' => 0, 'msg' => 'accountID => ' . $accountID . ' tips => ' . $idCardNational['msg']]);
            $idCardNational = $idCardNational['data']['media_id'];
            $idCardCopy     = $wxxModel->uploadMedia($idCardCopyReBuildFilePath);
            unlink($idCardCopyReBuildFilePath);
            if (!$idCardCopy['isSuccess'])
                return json(['status' => 0, 'msg' => 'accountID => ' . $accountID . ' tips => ' . $idCardCopy['msg']]);
            $idCardCopy = $idCardCopy['data']['media_id'];

            if (is_array($storeEntrance)) {
                $storeEntrance = $wxxModel->uploadMedia($storeEntrance[0]);
                if (!$storeEntrance['isSuccess'])
                    return json(['status' => 0, 'msg' => 'accountID => ' . $accountID . ' tips => ' . $storeEntrance['msg']]);
                $storeEntrance = $storeEntrance['data']['media_id'];
            }
            if (is_array($indoorPic)) {
                $indoorPic = $wxxModel->uploadMedia($indoorPic[0]);
                if (!$indoorPic['isSuccess'])
                    return json(['status' => 0, 'msg' => 'accountID => ' . $accountID . ' tips => ' . $indoorPic['msg']]);
                $indoorPic = $indoorPic['data']['media_id'];
            }

            $businessCode = 'apply-' . substr(md5($accountID . $applyInfo['id']), 0, '20');

            $applyResult = $wxxModel->applyMicro($idCardCopy, $idCardNational, $applyInfo['idCardName'],
                $applyInfo['idCardNumber'], $applyInfo['idCardValidTime'], $applyInfo['accountName'],
                $applyInfo['accountBank'], $applyInfo['bankAddressCode'], $applyInfo['accountNumber'], '广兴百货商店',
                '441481', '无', $storeEntrance, $indoorPic, $applyInfo['merchantShortName'],
                $applyInfo['servicePhone'], $applyInfo['productDesc'], $applyInfo['rate'], $applyInfo['contact'], $applyInfo['contactPhone'], $applyInfo['bankName'], $businessCode);

            $desc = '';

            $status = 0;

            if (!$applyResult['isSuccess']) {
                $desc = $applyResult['msg'];
                if (strpos($desc, '暂不支持此身份证号码入驻') !== false)
                    $status = -2;
                else
                    $status = -1;
            }


            $selectIsExits = Db::table('epay_wxx_apply_list')->where('businessCode', $businessCode)->limit(1)->field('id')->select();
            if (!empty($selectIsExits)) {
                Db::table('epay_wxx_apply_list')->where('id', $selectIsExits[0]['id'])->limit(1)->update([
                    'status' => $status,
                    'desc'   => $desc
                ]);
            } else {
                Db::table('epay_wxx_apply_list')->insert([
                    'accountID'    => $accountID,
                    'applyInfoID'  => $applyInfoID,
                    'businessCode' => $businessCode,
                    'status'       => $status,
                    'createTime'   => getDateTime(),
                    'desc'         => $desc
                ]);
            }

        }

        return json(['status' => 1, 'msg' => '提交申请成功']);
    }

    /**
     * @param int $accountID
     * @return WxxApiV1Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getWxxApiModel(int $accountID)
    {
        if (empty($accountID))
            return null;
        $selectResult = Db::table('epay_wxx_account_list')->where('id', $accountID)->limit(1)->field('desc,createTime', true)->select();
        if (empty($selectResult))
            return null;
        return new WxxApiV1Model($selectResult[0]['mchID'], $selectResult[0]['appKey'],
            FileModel::getFilePath($selectResult[0]['apiCertID']), FileModel::getFilePath($selectResult[0]['apiKeyID']));
    }

    /**
     * 重新构建图片 专门为小微商户申请使用 保存后记得删除
     * @param string $imagePath
     * @param string $tempImageSavePath
     * @return string
     */
    private function rebuildImage(string $imagePath, string $tempImageSavePath)
    {
        $info = getimagesize($imagePath);
        //get images info
        $type = image_type_to_extension($info[2], false);
        //get images ext
        $fun   = 'imagecreatefrom' . $type;
        $image = $fun($imagePath);
        //动态执行函数
        $col = imagecolorallocatealpha($image, 255, 255, 255, 50);
        imagestring($image, 1, rand(0, $info[0]), rand(0, $info[1]), '.', $col);
        $fun               = 'image' . $type;
        $tempImageSavePath = $tempImageSavePath . '/' . uniqid('tempImage_', true) . '.' . $type;
        $fun($image, $tempImageSavePath);
        imagedestroy($image);
        return $tempImageSavePath;
    }
}