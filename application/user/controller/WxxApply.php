<?php

namespace app\user\controller;

use app\user\model\WxxApiV1Model;
use think\Controller;
use think\Exception;

class WxxApply extends Controller
{
    private $mid = '1518693491';
    private $appKey = 'SAGNgAYlA8wdShsPPTiZi0xyTT1xFuOU';

    public function indexTemplate()
    {
        return $this->fetch('/WxxApplyTemplate');
    }

    public function postReAutoWithDrawByDate()
    {
        $subMchID = input('post.subMchID/s');
        $date     = input('post.date/s');
        if(empty($subMchID) || empty($date))
            return '<script>alert("请求参数不能为空");</script>';
        $wxxModel = new WxxApiV1Model($this->mid, $this->appKey);
        $result = $wxxModel->reAutoWithDrawByDate($subMchID,$date);
        dump($result);
        echo '具体教程请看 <a href="https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=21_3" target="_blank">这里</a>';
    }

    public function postModifyContactInfo()
    {
        $subMchID     = input('post.subMchID/s');
        $mobilePhone  = input('post.mobilePhone/s');
        $email        = input('post.email/s');
        $merchantName = input('post.merchantName/s');

        if (empty($subMchID))
            return '<script>alert("小微商户号不能为空");</script>';

        $wxxModel = new WxxApiV1Model($this->mid, $this->appKey);
        $result   = $wxxModel->modifyContactInfo($subMchID, $mobilePhone, $email, $merchantName);
        dump($result);
    }

    public function postModifyArchives()
    {
        $subMchID        = input('post.subMchID/s');
        $accountNo       = input('post.accountNo/s');
        $accountBank     = input('post.accountBank/s');
        $bankName        = input('post.bankName/s');
        $bankAddressCode = input('post.bankAddressCode/s');

        if (empty($subMchID) || empty($accountNo) || empty($accountBank) || empty($bankName) || empty($bankAddressCode))
            return '<script>alert("请求参数不能为空");</script>';
        $wxxModel = new WxxApiV1Model($this->mid, $this->appKey);
        $result   = $wxxModel->modifyArchives($subMchID, $accountNo, $accountBank, $bankName, $bankAddressCode);
        dump($result);
    }

    public function postSelectOrderStatus()
    {
        $orderID = input('post.applyNumber/s');
        if (empty($orderID))
            return '<script>alert("请求参数不能为空");</script>';
        $wxxModel = new WxxApiV1Model($this->mid, $this->appKey);
        $result   = $wxxModel->applyStatus($orderID, 'applyment_id');
        dump($result);
        echo 'subMchId => 商户号ID 记住这个就完事<br>';
        echo 'signUrl => 链接 这个打开，然后用手机扫签约完事<br>';
        echo '具体教程请看 <a href="https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_3" target="_blank">这里</a>';
    }

    public function postApply()
    {
        try{
            $idCardCopyFile     = $this->request->file('idCardCopy');
        }catch (Exception $exception){
            return '<script>alert("身份证正面尚未上传")</script>';
        }
        try{
            $idCardNationalFile = $this->request->file('idCardNational');
        }catch (Exception $exception){
            return '<script>alert("身份证背面尚未上传")</script>';
        }

        $idCardName        = input('post.idCardName');
        $idCardNumber      = input('post.idCardNumber');
        $idCardValidTime   = input('post.idCardValidTime');
        $accountName       = input('post.accountName');
        $accountBank       = input('post.accountBank');
        $bankAddressCode   = input('post.bankAddressCode');
        $accountNumber     = input('post.accountNumber');
        $storeName         = input('post.storeName');
        $storeAddressCode  = '441481';
        $storeStreet       = '无';
        $merchantShortName = input('post.merchantShortName');
        $servicePhone      = input('post.servicePhone');
        $contact           = input('post.contact');
        $contactPhone      = input('post.contactPhone');
        $productDesc       = input('post.productDesc');
        $rate              = input('post.rate');

        if (empty($idCardName) || empty($idCardNumber) || empty($idCardValidTime) || empty($accountName) || empty($accountBank)
            || empty($bankAddressCode) || empty($accountNumber) || empty($storeName) || empty($storeAddressCode)
            || empty($storeStreet) || empty($merchantShortName) || empty($servicePhone) || empty($contact) || empty($contactPhone) || empty($productDesc) || empty($rate))
            return '<script>alert("参数不能为空，请检查请求参数")</script>';

        $wxxModel     = new WxxApiV1Model($this->mid, $this->appKey);
        $updateResult = $wxxModel->uploadMedia($idCardCopyFile->getPathname());
        if (!$updateResult['isSuccess'])
            exit(dump($updateResult));
        $idCardCopyFile = $updateResult['data']['media_id'];
        $updateResult   = $wxxModel->uploadMedia($idCardNationalFile->getPathname());
        if (!$updateResult['isSuccess'])
            exit(dump($updateResult));
        $idCardNationalFile = $updateResult['data']['media_id'];

        $storeEntrancePicFile = $wxxModel->uploadMedia(env('root_path') . '/public/static/uploads/e1/a92f386edecbc97e68a55e0cf0f92b8d7756b0e8bc85618485d460d30e420a.png')['data']['media_id'];
        $indoorPicFile        = $wxxModel->uploadMedia(env('root_path') . '/public/static/uploads/7e/d401b3f0f7d63da4cc696a7ce00984c77f56ef60e37c8ec8babb36868d5838.png')['data']['media_id'];

        $result = $wxxModel->applyMicro($idCardCopyFile, $idCardNationalFile, $idCardName, $idCardNumber, $idCardValidTime
            , $accountName, $accountBank, $bankAddressCode, $accountNumber, $storeName, $storeAddressCode, $storeStreet,
            $storeEntrancePicFile, $indoorPicFile, $merchantShortName, $servicePhone, $productDesc, $rate, $contact, $contactPhone);

        dump($result);

        echo 'isSuccess 如果 返回 true 则为成功 false 则为失败 具体询问技术人员<br>';
        echo 'businessCode 请您记住这玩意 因为待会查单要用到！！！ 不懂就直接截图<br>';
        echo '具体教程请看 <a href="https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_2" target="_blank">这里</a>';
    }
}