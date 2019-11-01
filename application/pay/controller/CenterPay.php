<?php

namespace app\pay\controller;

use app\pay\model\CenterPayModel;
use app\pay\model\PayModel;
use think\App;
use think\Controller;
use think\Db;

class CenterPay extends Controller
{
    private $systemConfig;
    private $notifyUrl;
    private $returnUrl;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->systemConfig = getConfig();
        $this->returnUrl    = url('/Pay/CenterPay/Return', '', false, true);
        if (empty($this->systemConfig['notifyDomain'])) {
            $this->notifyUrl = url('/Pay/CenterPay/Notify', '', false, true);
        } else {
            $this->notifyUrl = $this->systemConfig['notifyDomain'] . '/Pay/CenterPay/Notify';
        }
    }

    public function getSubmit()
    {
        $tradeNo = input('get.tradeNo');
//        $siteName = htmlentities(base64_decode(input('get.siteName')));
//        if (empty($siteName))
//            $siteName = '易支付';
        $sign = input('get.sign/s');
        if (md5($tradeNo . 'huaji') != $sign)
            return $this->fetch('/SystemMessage', ['msg' => '签名有误！']);
        if (strlen($tradeNo) != 19) {
            $tradeNo = substr($tradeNo, 0, 19);
        }
        //这里负责纠正一些人错误复制访问链接导致失败
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('uid,money,status,type')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID无效！']);
        if ($result[0]['status'])
            return redirect(buildCallBackUrl($tradeNo, 'return'));

        $payName = PayModel::converPayName($result[0]['type'], true);
        if (empty($this->systemConfig[$payName]))
            return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 系统异常请联系管理员处理！']);
        $userPayConfig   = unserialize(getPayUserAttr($result[0]['uid'], 'payConfig'));
        $systemPayConfig = $this->systemConfig[$payName];
        if (empty($userPayConfig)) {
            if (!isset($systemPayConfig['apiType']))
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 该订单尚不支持中央支付！']);
            if (empty($systemPayConfig['isOpen']))
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] ' . $systemPayConfig['tips'] . '！']);
            if (!$systemPayConfig['isOpen'])
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] ' . $systemPayConfig['tips'] . '！']);
            if ($systemPayConfig['apiType'] != 1)
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 该订单尚不支持中央支付！']);
        } else {
            if ($userPayConfig[$payName]['apiType'] != 1)
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 该订单尚不支持中央支付！']);
            if (empty($systemPayConfig['epayCenterUid']))
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 易支付中心系统接口商户号不能为空！']);
            if (empty($systemPayConfig['epayCenterKey']))
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 易支付中心系统接口密匙不能为空！']);
            if (empty($userPayConfig[$payName]['payAisle']))
                return $this->fetch('/SystemMessage', ['msg' => '[EpayCenter] 支付接口方式有误，请联系管理员处理！']);
        }

        $config            = $systemPayConfig;
        $config['gateway'] = 'http://center.zmz999.com';
        $centerPayModel    = new CenterPayModel($config);

        $sellerEmail = '';

        if ($result[0]['type'] == 3) {
            $sellerEmail = getPayUserAttr($result[0]['uid'], 'aliSellerEmail');
        }

        $requestResult = $centerPayModel->getPayUrl($tradeNo, $payName, $userPayConfig[$payName]['payAisle'], ($result[0]['money'] / 100), $this->notifyUrl, $this->returnUrl, $sellerEmail);
        if ($requestResult['isSuccess'] && (empty($requestResult['html'] && empty($requestResult['url']))))
            $requestResult = $centerPayModel->getPayUrl($tradeNo, $payName, $userPayConfig[$payName]['payAisle'], ($result[0]['money'] / 100), $this->notifyUrl, $this->returnUrl, $sellerEmail);
        if ($requestResult['isSuccess']) {
//            if (empty($requestResult['html'])) {
//                return redirect($requestResult['url'], [], 302);
//            } else {
//                return $this->fetch('/CenterPayTemplate', ['url' => $requestResult['html']]);
//            }
            if (!empty($requestResult['url']))
//                return $this->fetch('/CenterPayTemplate', ['url' => $requestResult['url'], 'tradeNo' => $tradeNo, 'payType' => $result[0]['type']]);
                return redirect($requestResult['url'], [], 302);
            if (!empty($requestResult['html']))
                return $requestResult['html'];
        }
        if (!empty($requestResult['msg']))
            return $this->fetch('/SystemMessage', ['msg' => $requestResult['msg']]);
        trace('CenterPay errorContent => ' . json_encode($requestResult, true), 'info');
        return $this->fetch('/SystemMessage', ['msg' => '系统似乎开小差了，请重新发起请求']);
    }

    public function postNotify()
    {
        $requestData = input('post.');
        if (empty($requestData['sign']) || empty($requestData['sign_type']) || empty($requestData['data']) || empty($requestData['status']))
            return json(['status' => 0, 'msg' => '[10001]sign error']);
        if (!$requestData['status'])
            return json(['status' => 0, 'msg' => 'notify error']);
        if ($requestData['sign_type'] != 'MD5')
            return json(['status' => 0, 'msg' => 'sign type error']);
        $returnData = json_decode($requestData['data'], true);
        if ($requestData == null)
            return json(['status' => 0, 'msg' => 'return data error']);

        $payType = $this->defaultValue($returnData['payType']);

        $payName = PayModel::converPayName($payType, true);
        //转换支付类型
        $totalMoney   = $this->defaultValue($returnData['money']);
        $tradeNoOut   = $this->defaultValue($returnData['tradeNoOut']);
        $callBackTime = $this->defaultValue($requestData['time'], 0);

        $config            = $this->systemConfig[$payName];
        $config['gateway'] = 'http://center.zmz999.com';
        $centerPayModel    = new CenterPayModel($config);

        if ($centerPayModel->buildSignMD5($requestData) != $requestData['sign'])
            return json(['status' => 0, 'msg' => '[10002]sign error']);
        //签名确实错了
        //if ((time() - $callBackTime) > 300)
        //   return json(['status' => 0, 'msg' => 'sign time out']);
        //签名5分钟超时
        $tradeData = Db::table('epay_order')->where([
            'tradeNo' => $tradeNoOut
        ])->limit(1)->field('status,money')->select();
        if (empty($tradeData))
            return json(['status' => 0, 'msg' => 'order data empty']);
        if ($tradeData[0]['status'])
            return json(['status' => 1, 'msg' => 'order status paid']);
        if ($tradeData[0]['money'] != $totalMoney)
            return json(['status' => 0, 'msg' => 'order money error']);
        if (!$centerPayModel->isPay($tradeNoOut))
            return json(['status' => 0, 'msg' => 'order invalid']);
        $updateResult = Db::table('epay_order')->where('tradeNo', $tradeNoOut)->limit(1)->update([
            'status'  => 1,
            'endTime' => getDateTime()
        ]);
        if ($updateResult)
            processOrder($tradeNoOut, true);
        else
            trace('[EpayCenterModel] 更新订单状态异常 tradeNo => ' . $tradeNoOut, 'error');
        return json(['status' => 1, 'msg' => 'update order status success']);
    }

    public function getReturn()
    {
        $tradeNoOut  = input('get.tradeNoOut/s');
        $money       = input('get.money/s');
        $tradeStatus = input('get.tradeStatus/s');
        $payType     = input('get.payType/s');
        $payTime     = input('get.endTime/s');
        $signType    = input('get.sign_type/s');
        $sign        = input('get.sign');

        if ($signType != 'MD5')
            return $this->fetch('/SystemMessage', ['msg' => '支付同步回调异常，签名类型目前仅支持MD5！']);
        if (empty($this->systemConfig[$payType]))
            return $this->fetch('/SystemMessage', ['msg' => '支付类型尚不支持回调，请联系站点管理员处理！']);

        //if (time() - strtotime($payTime) > 300)
        //    return $this->fetch('/SystemMessage', ['msg' => '同步回调超时，可能已经支付成功了。。。但是超过5分钟了！']);

        if (empty($this->systemConfig[$payType]['epayCenterKey']))
            return $this->fetch('/SystemMessage', ['msg' => '后台参数配置缺失，请联系站点管理员处理！']);
        $md5 = md5(createLinkString(argSort(paraFilter(input('get.')))) . $this->systemConfig[$payType]['epayCenterKey']);
        if ($md5 != $sign)
            return $this->fetch('/SystemMessage', ['msg' => '回调签名校验失败,请联系站点管理员处理！']);

        if ($tradeStatus != 'SUCCESS')
            return $this->fetch('/SystemMessage', ['msg' => '订单尚未支付,请支付后再操作！']);

        return redirect(buildCallBackUrl($tradeNoOut, 'return'));
    }

    /**
     * 显示默认参数 免得报错
     * @param $value
     * @param string $default
     * @return string
     */
    private function defaultValue($value, $default = '')
    {
        if (!isset($value))
            return $default;
        return $value;
    }
}