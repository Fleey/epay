<?php

namespace app\user\model;

use think\Exception;

class WxxApiV1Model
{
    private $mid;
    private $appKey;
    private $sslCertPath;
    private $sslKeyPath;
    private $certSN;
    //证书SN
    private $certNonce;
    //证书加密随机字符串
    private $certContent;
    private $cipherText;

    //证书内容

    public function __construct(String $mchID, String $appKey, String $sslCertPath, String $sslKeyPath)
    {
        $this->mid         = $mchID;
        $this->appKey      = $appKey;
        $this->sslCertPath = $sslCertPath;
        $this->sslKeyPath  = $sslKeyPath;
        $this->getCertificates();
    }

    /**
     * 上传文件到微信小商户
     * @param string $filePath
     * @return array
     */
    public function uploadMedia(string $filePath): array
    {

        if (!file_exists($filePath))
            return ['isSuccess' => false, 'msg' => 'file does not exist'];

        $requestUrl = 'https://api.mch.weixin.qq.com/secapi/mch/uploadmedia';

        $param          = [
            'mch_id'     => $this->mid,
            'media_hash' => md5_file($filePath),
            'sign_type'  => 'HMAC-SHA256'
        ];
        $param['sign']  = self::signParam($param, $this->appKey, 'HMAC-SHA256');
        $param['media'] = new \CURLFile($filePath, 'image/jpeg', 'img.jpg');

        $header = [
            'content-type:multipart/form-data'
        ];

        $result = curl($requestUrl, $header, 'post', $param, 'form-data', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);
        if (!$result)
            return ['isSuccess' => false, 'wxx request fail'];

        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS')
            if (isset($result['error_code']))
                return ['isSuccess' => false, 'msg' => $result['error_code'] . ' ' . $result['error_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . ' ' . $result['err_code_des']];
        if (self::signParam($result, $this->appKey, 'MD5') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];
        //这里好坑。。。居然是用MD5的，不是跟随请求签名类型的
        return ['isSuccess' => true, 'data' => [
            'media_id' => $result['media_id']
        ]];
    }

    /**
     * 重新发起提现
     * @param string $subMchID //小微商户号
     * @param string $date //自动提现单提现日期 YYYYMMDD 20180602
     * @return array
     * https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=21_3
     */
    public function reAutoWithDrawByDate(string $subMchID, string $date)
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/fund/reautowithdrawbydate';

        $param         = [
            'mch_id'     => $this->mid,
            'sub_mch_id' => $subMchID,
            'date'       => $date,
            'nonce_str'  => getRandChar(16),
            'sign_type'  => 'HMAC-SHA256'
        ];
        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');

        $result = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);

        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];

        return ['isSuccess' => true, 'data' => [
            'date'        => $result['date'],
            'mch_id'      => $result['mch_id'],
            'sub_mch_id'  => $result['sub_mch_id'],
            'withdraw_id' => $result['withdraw_id'],
            'amount'      => $result['amount'],
            'create_time' => $result['create_time']
        ], 'msg'            => '发起自动提现成功'];
    }

    /**
     * 修改联系信息
     * @param string $subMchID //小微商户号
     * @param string $mobilePhone //手机号
     * @param string $email //邮箱
     * @param string $merchantName //商户简称
     * @return array
     * https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=21_4
     */
    public function modifyContactInfo(string $subMchID, string $mobilePhone = '', string $email = '', string $merchantName = '')
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/applyment/micro/modifycontactinfo';

        $param = [
            'version'    => '1.0',
            'mch_id'     => $this->mid,
            'nonce_str'  => getRandChar(16),
            'sign_type'  => 'HMAC-SHA256',
            'sub_mch_id' => $subMchID,
            'cert_sn'    => $this->certSN
        ];
        if (!empty($mobilePhone))
            $param['mobile_phone'] = $this->getEncrypt($mobilePhone);
        if (!empty($email))
            $param['email'] = $this->getEncrypt($email);
        if (!empty($merchantName))
            $param['merchant_name'] = $merchantName;
        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');

        $result = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);

        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => 'wxx -> ' . $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => ' wxx -> return data sign fail'];

        return ['isSuccess' => true, 'data' => [
            'mch_id'     => $result['mch_id'],
            'sub_mch_id' => $result['sub_mch_id']
        ], 'msg'            => '修改小微商户信息成功'];
    }

    /**
     * 修改结算银行卡
     * @param string $subMchID //小微商户号
     * @param string $accountNo //商户结算银行卡号,该字段属于敏感字段
     * @param string $accountBank //开户银行
     * @param string $bankName //开户银行全称（含支行）
     * @param string $bankAddressCode //开户银行省市编码
     * https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=21_2
     * @return array
     */
    public function modifyArchives(string $subMchID, string $bankAddressCode, string $accountNo = '', string $accountBank = '', string $bankName = '')
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/applyment/micro/modifyarchives';
        $param      = [
            'version'           => '1.0',
            'mch_id'            => $this->mid,
            'nonce_str'         => getRandChar(16),
            'bank_address_code' => $bankAddressCode,
            'cert_sn'           => $this->certSN,
            'sign_type'         => 'HMAC-SHA256',
            'sub_mch_id'        => $subMchID
        ];
        if (!empty($bankName))
            $param['bank_name'] = $bankName;
        if (!empty($accountNo))
            $param['account_number'] = $this->getEncrypt($accountNo);
        if (!empty($accountBank))
            $param['account_bank'] = $accountBank;

        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');

        $result = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);

        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            trace('[小微服务商]修改结算银行卡失败 ' . json_encode($result), 'warning');
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];

        trace('[小微服务商]修改结算银行卡成功 mch_id => ' . $result['mch_id'], 'info');

        return ['isSuccess' => true, 'data' => [
            'mch_id'     => $result['mch_id'],
            'sub_mch_id' => $result['sub_mch_id']
        ], 'msg'            => '修改小微商户结算银行卡成功'];
    }

    /**
     * 获取证书信息
     * @return array
     */
    public function getCertificates(): array
    {
        if (!empty($this->certSN) && !empty($this->certContent))
            return ['isSuccess' => true, 'data' => [
                'serial_no'  => $this->certSN,
                'ciphertext' => $this->cipherText
            ]];
        $requestUrl = 'https://api.mch.weixin.qq.com/risk/getcertficates';

        $param         = [
            'mch_id'    => $this->mid,
            'nonce_str' => getRandChar(16),
            'sign_type' => 'HMAC-SHA256'
        ];
        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');

        $result = curl($requestUrl, [], 'post', arrayToXml($param), 'xml');

        if (!$result)
            return ['isSuccess' => false, 'msg' => 'wxx request cer error'];

        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];
        $data = json_decode($result['certificates'], true);
        $data = $data['data'][0];

        $this->certSN     = $data['serial_no'];
        $this->ciphertext = $data['encrypt_certificate']['ciphertext'];

        return ['isSuccess' => true, 'data' => [
            'serial_no'  => $data['serial_no'],
            'ciphertext' => $data['encrypt_certificate']['ciphertext'],
            'nonce'      => $data['encrypt_certificate']['nonce']
        ]];
    }

    /**
     * 小微商户进阶升级
     * https://pay.weixin.qq.com/wiki/doc/api/mch_xiaowei.php?chapter=28_2&index=2
     * @param string $subMchID //小微商户号
     * @param string $organizationType //2-企业 4-个体工商户  3-党政、机关及事业单位  1708-其他组织
     * @param string $businessLicenseCopy //营业执照扫描件 需要预先上传好的图片ID
     * @param string $businessLicenseNumber // 请填写营业执照上的营业执照注册号
     * @param string $merchantName //支持括号 个体工商户不能以“公司”结尾
     * @param string $companyAddress //注册地址
     * @param string $legalPerson //经营者姓名/法定代表人
     * @param string $businessTime //营业期限 ["1970-01-01","长期"]
     * @param string $businessLicenceType //1762-已三证合一    1763-未三证合一
     * @param string $merchantShortName //商户名称
     * @param string $business //费率结算规则ID https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=22_1
     * @param string $contactEmail //联系邮箱 必填
     * @param string $accountName //开户名称 这里开始 企业必填
     * @param string $accountBank //开户银行
     * @param string $bankAddressCode //开户银行省市编码
     * @param string $bankName //开户银行全称
     * @param string $accountNumber //银行卡号
     * @param string $organizationCopy //组织机构代码证照片
     * @param string $organizationNumber //组织机构代码
     * @param string $organizationTime //组织机构代码有效期限 ["1970-01-01","长期"]
     * @return array|mixed
     */
    public function applyMicroUpgrade(string $subMchID, string $organizationType, string $businessLicenseCopy,
                                      string $businessLicenseNumber, string $merchantName, string $companyAddress,
                                      string $legalPerson, string $businessTime, string $businessLicenceType,
                                      string $merchantShortName, string $business, string $contactEmail,
                                      string $accountName = '', string $accountBank = '', string $bankAddressCode = '',
                                      string $bankName = '', string $accountNumber = '', string $organizationCopy = '',
                                      string $organizationNumber = '', string $organizationTime = '')
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/applyment/micro/submitupgrade';

        $certSN = $this->getCertificates();
        if (!$certSN['isSuccess']) {
            return $certSN;
        }
        $certSN = $certSN['data']['serial_no'];

        $param = [
            'version'                 => '1.0',
            'mch_id'                  => $this->mid,
            'nonce_str'               => getRandChar(16),
            'sign_type'               => 'HMAC-SHA256',
            'cert_sn'                 => $certSN,
            'sub_mch_id'              => $subMchID,
            'organization_type'       => $organizationType,
            'business_license_copy'   => $businessLicenseCopy,
            'business_license_number' => $businessLicenseNumber,
            'merchant_name'           => $merchantName,
            'company_address'         => $companyAddress,
            'legal_person'            => $this->getEncrypt($legalPerson),
            'business_time'           => $businessTime,
            'business_licence_type'   => $businessLicenceType,
            'merchant_shortname'      => $merchantShortName,
            'business'                => $business,
            'business_scene'          => '[1721]',
            'contact_email'           => $this->getEncrypt($contactEmail)
        ];
        if (!empty($organizationCopy))
            $param['organization_copy'] = $organizationCopy;
        if (!empty($organizationNumber))
            $param['organization_number'] = $organizationNumber;
        if (!empty($organizationTime))
            $param['organization_time'] = $organizationTime;

        if (!empty($accountName))
            $param['account_name'] = $this->getEncrypt($accountName);
        if (!empty($accountBank))
            $param['account_bank'] = $accountBank;
        if (!empty($bankAddressCode))
            $param['bank_address_code'] = $bankAddressCode;
        if (!empty($bankName))
            $param['bank_name'] = $bankName;
        if (!empty($accountNumber))
            $param['account_number'] = $accountNumber;


        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');

        $result = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);
        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if ($result['err_code'] == 'PARAM_ERROR')
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_param']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];

        return ['isSuccess' => true, 'data' => $result];
    }

    /**
     * 申请微信小微商户
     * https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_2
     * @param String $idCardCopy //身份证人像面照片
     * @param String $idCardNational //身份证国徽面照片
     * @param String $idCardName //身份证姓名
     * @param String $idCardNumber //身份证号码
     * @param String $idCardValidTime //身份证有效期限  例子 ["1970-01-01","长期"]
     * @param String $accountName //开户名称
     * @param String $accountBank //开户银行
     * @param String $bankAddressCode //开户银行省市编码
     * @param String $accountNumber //银行账号
     * @param String $storeName //门店名称
     * @param String $storeAddressCode //门店省市编码
     * @param String $storeStreet //门店街道名称
     * @param String $storeEntrancePic //门店门口照片
     * @param String $indoorPic //店内环境照片
     * @param String $merchantShortName //商户简称
     * @param String $servicePhone //客服电话
     * @param String $productDesc //售卖商品/提供服务描述
     * @param String $rate //费率
     * @param String $contact //联系人姓名
     * @param String $contactPhone //手机号码
     * @param String $bankName
     * @param string $businessCode
     * @return array|mixed
     */
    public function applyMicro(String $idCardCopy, String $idCardNational, String $idCardName,
                               String $idCardNumber, String $idCardValidTime, String $accountName, String $accountBank,
                               String $bankAddressCode, String $accountNumber, String $storeName, String $storeAddressCode,
                               String $storeStreet, String $storeEntrancePic, String $indoorPic, String $merchantShortName,
                               String $servicePhone, String $productDesc, String $rate, String $contact, String $contactPhone,
                               string $bankName = '', string $businessCode = '')
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/applyment/micro/submit';

        $certSN = $this->getCertificates();
        if (!$certSN['isSuccess']) {
            return $certSN;
        }
        $certSN = $certSN['data']['serial_no'];
        //获取证书SN
        if (empty($businessCode))
            $businessCode = time() . '-' . uniqid();
        //生成业务编号
        $param = [
            'version'            => '3.0',
            'cert_sn'            => $certSN,
            'mch_id'             => $this->mid,
            'nonce_str'          => getRandChar(16),
            'sign_type'          => 'HMAC-SHA256',
            'business_code'      => $businessCode,
            'id_card_copy'       => $idCardCopy,
            'id_card_national'   => $idCardNational,
            'id_card_name'       => $this->getEncrypt($idCardName),
            'id_card_number'     => $this->getEncrypt($idCardNumber),
            'id_card_valid_time' => $idCardValidTime,
            'account_name'       => $this->getEncrypt($accountName),
            'account_bank'       => $accountBank,
            'bank_address_code'  => $bankAddressCode,
            'account_number'     => $this->getEncrypt($accountNumber),
            'store_name'         => $storeName,
            'store_address_code' => $storeAddressCode,
            'store_street'       => $storeStreet,
            'store_entrance_pic' => $storeEntrancePic,
            'indoor_pic'         => $indoorPic,
            'merchant_shortname' => $merchantShortName,
            'service_phone'      => $servicePhone,
            'product_desc'       => $productDesc,
            'rate'               => $rate,
            'contact'            => $this->getEncrypt($contact),
            'contact_phone'      => $this->getEncrypt($contactPhone)
        ];
        if (!empty($bankName))
            $param['bank_name'] = $bankName;
        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');

        $result = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);
        $result = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];

        return ['isSuccess' => true, 'data' => [
            'applymentID'  => $result['applyment_id'],
            'businessCode' => $businessCode
        ]];
    }

    public function applyStatusUpgrade(string $subMchID)
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/applyment/micro/getupgradestate';

        $param = [
            'version'    => '1.0',
            'mch_id'     => $this->mid,
            'nonce_str'  => getRandChar(16),
            'sign_type'  => 'HMAC-SHA256',
            'sub_mch_id' => $subMchID
        ];

        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');
        $result        = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);

        $result        = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
//        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
//            return ['isSuccess' => false, 'msg' => 'return data sign fail'];

        exit(dump($result));
    }

    /**
     * 获取申请状态
     * @param String $applyID
     * @param String $selectType
     * @return array
     */
    public function applyStatus(String $applyID, String $selectType = 'business')
    {
        $requestUrl = 'https://api.mch.weixin.qq.com/applyment/micro/getstate';

        $param = [
            'version'   => '1.0',
            'mch_id'    => $this->mid,
            'nonce_str' => getRandChar(16),
            'sign_type' => 'HMAC-SHA256'
        ];
        if ($selectType == 'business')
            $param['business_code'] = $applyID;
        else
            $param['applyment_id'] = $applyID;

        $param['sign'] = self::signParam($param, $this->appKey, 'HMAC-SHA256');
        $result        = curl($requestUrl, [], 'post', arrayToXml($param), 'xml', false, false, [
            'sslCertPath' => $this->sslCertPath,
            'sslKeyPath'  => $this->sslKeyPath
        ]);
        $result        = xmlToArray($result);
        if ($result['return_code'] != 'SUCCESS')
            return ['isSuccess' => false, 'msg' => $result['return_msg']];
        if ($result['result_code'] != 'SUCCESS') {
            if (!empty($result['err_code_desc']))
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_desc']];
            else
                return ['isSuccess' => false, 'msg' => $result['err_code'] . '  ' . $result['err_code_des']];
        }
        if (self::signParam($result, $this->appKey, 'HMAC-SHA256') != $result['sign'])
            return ['isSuccess' => false, 'msg' => 'return data sign fail'];


        $data = [
            'applyID'        => $result['applyment_id'],
            'applyState'     => $result['applyment_state'],
            'applyStateDesc' => $result['applyment_state_desc']
        ];
        if ($data['applyState'] == 'TO_BE_SIGNED' || $data['applyState'] == 'FINISH') {
            if (!empty($result['sub_mch_id']))
                $data['subMchId'] = $result['sub_mch_id'];
            if (!empty($result['sign_url']))
                $data['signUrl'] = $result['sign_url'];
        }
        if ($data['applyState'] == 'REJECTED') {
            $data['auditDetail'] = $result['audit_detail'];
        }
        return ['isSuccess' => true, 'data' => $data];
    }

    /**
     * 根据微信小商户证书进行加密数据
     * https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_12
     * @param $str
     * @return string
     */
    public function getEncrypt($str)
    {
        //$str是待加密字符串
        if (empty($this->certContent)) {
            $certData          = $this->getCertificates();
            $public_key        = self::decodePem($this->appKey, $certData['data']['ciphertext'], $certData['data']['nonce']);
            $this->certContent = $public_key;
        } else {
            $public_key = $this->certContent;
        }
        $encrypted = '';
        openssl_public_encrypt($str, $encrypted, $public_key);
        //base64编码
        $sign = base64_encode($encrypted);
        return $sign;
    }

    /**
     * 退款解密函数
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi_sl.php?chapter=9_16#menu1
     * @param string $base64Data
     * @return string
     */
    public function getDecrypt(string $base64Data)
    {
        $encryption = base64_decode($base64Data);
        $key        = md5($this->appKey);
        $return     = openssl_decrypt($encryption, 'AES-256-ECB', $key, OPENSSL_RAW_DATA);
        return $return;
    }

    private static function decodePem(string $appKey, string $cipherText, string $nonce)
    {
        $associated_data  = 'certificate';
        $key              = $appKey;
        $check_sodium_mod = extension_loaded('sodium');
        if ($check_sodium_mod === false)
            exit(dump('没有安装sodium模块'));
        $check_aes256gcm = sodium_crypto_aead_aes256gcm_is_available();
        if ($check_aes256gcm === false)
            exit(dump('当前不支持aes256gcm'));

        $pem = sodium_crypto_aead_aes256gcm_decrypt(base64_decode($cipherText), $associated_data, $nonce, $key);
        return $pem;
    }

    /**
     * 构建签名
     * @param array $param
     * @param string $appKey
     * @param string $signType
     * @return string
     */
    public static function signParam(array $param, string $appKey, string $signType = 'MD5')
    {
        ksort($param);
        $stringA = self::buildUrlParam($param);
        $stringA .= '&key=' . $appKey;
        //排序并组合字符串
        if ($signType == 'MD5') {
            $stringA = md5($stringA);
        } else if ($signType == 'HMAC-SHA256') {
            $stringA = hash_hmac('sha256', $stringA, $appKey);
        }
        return strtoupper($stringA);
    }


    /**
     * 构建url请求参数
     * @param array $data
     * @return string
     */
    private static function buildUrlParam(array $data)
    {
        $tempBuff = '';
        foreach ($data as $key => $value) {
            if ($key != 'sign' && !empty($value))
                $tempBuff .= $key . '=' . $value . '&';
        }
        $tempBuff = trim($tempBuff, '&');
        return $tempBuff;
    }

}