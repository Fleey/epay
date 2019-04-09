<?php

namespace app\pay\controller;

use app\pay\model\WxPayModel;
use think\App;
use think\Controller;
use think\Db;

class WxPay extends Controller
{
    private $systemConfig;
    private $notifyUrl;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->systemConfig = getConfig();
        if (empty($this->systemConfig['notifyDomain'])) {
            $this->notifyUrl = url('/Pay/WxPay/Notify', '', false, true);
        } else {
            $this->notifyUrl = $this->systemConfig['notifyDomain'] . '/Pay/WxPay/Notify';
        }
    }

    /**
     * @return mixed|\think\response\Redirect
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubmit()
    {
        $tradeNo  = input('get.tradeNo');
        $siteName = htmlentities(base64_decode(input('get.siteName', '易支付')));
        if (empty($siteName))
            $siteName = '易支付';
        if (empty($tradeNo))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID有误！']);
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('uid,money,productName,status,type,createTime')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '交易ID无效！']);
        if ($result[0]['type'] != 1)
            return $this->fetch('/SystemMessage', ['msg' => '支付方式有误！']);
        if ($result[0]['status'])
            return $this->fetch('/SystemMessage', ['msg' => '交易已经完成无法再次支付！']);

        if (isset($this->systemConfig['wxpay']['apiType']))
            if ($this->systemConfig['wxpay']['apiType'] != 0)
                return $this->fetch('/SystemMessage', ['msg' => '该订单尚不支持原生支付！']);

        $productNameShowMode = intval(getPayUserAttr($result[0]['uid'], 'productNameShowMode'));
        $productName         = empty($this->systemConfig['defaultProductName']) ? '这个是默认商品名称' : $this->systemConfig['defaultProductName'];
        if ($productNameShowMode == 1) {
            $tempData    = getPayUserAttr($result[0]['uid'], 'productName');
            $productName = empty($tempData) ? '商户尚未设置默认商品名称' : $tempData;
        } else if ($productNameShowMode == 2) {
            $productName = $result[0]['productName'];
        }

        $tradeData                = $result[0];
        $tradeData['tradeNo']     = $tradeNo;
        $tradeData['productName'] = $productName;
        //build trade data
        $isWxBrowser = strpos($this->request->header('user-agent'), 'MicroMessenger') !== false;
        //is wx browser
        $wxPayModel = new WxPayModel($this->systemConfig['wxpay']);
        if ($isWxBrowser) {
            $requestResult = $wxPayModel->sendPayRequest($tradeData, 'JSAPI', $this->notifyUrl);
            //手机微信内置浏览器支付
        } else if ($this->request->isMobile()) {
            $requestResult = $wxPayModel->sendPayRequest($tradeData, 'MWEB', $this->notifyUrl);
            //手机端微信支付
        } else {
            $requestResult = $wxPayModel->sendPayRequest($tradeData, 'NATIVE', $this->notifyUrl);
            //PC端微信支付
        }
        if ($requestResult['return_code'] != 'SUCCESS')
            return $this->fetch('/SystemMessage', ['msg' => '微信支付下单失败！<br>[' . $requestResult['return_code'] . '] ' . $requestResult['return_msg']]);
        if ($requestResult['result_code'] != 'SUCCESS')
            return $this->fetch('/SystemMessage', ['msg' => '微信支付下单失败！<br>[' . $requestResult['err_code'] . '] ' . $requestResult['err_code_des']]);
        if ($requestResult['return_code'] == 'SUCCESS') {
            if ($isWxBrowser) {
                return $this->fetch('/WxPayJsTemplate', [
                    'jsApiParam'  => $wxPayModel->buildJsApiParam($requestResult),
                    'redirectUrl' => input('get.d/d', 0) ? 'data.backurl' : url('/Pay/WxPay/WapResult', '', false, true),
                    'tradeNo'     => $tradeNo
                ]);
            } else if ($this->request->isMobile()) {
                $returnUrl = url('/Pay/WxPay/WapReturn?tradeNo=' . $tradeNo, '', false, true);
                return '<script>window.location.replace(\'' . $requestResult['mweb_url'] . '&redirect_url=' . urlencode($returnUrl) . '\');</script>';
            } else {
                return $this->fetch('/WxPayPcTemplate', [
                    'siteName'    => $siteName,
                    'productName' => $result[0]['productName'],
                    'money'       => $result[0]['money'] / 100,
                    'tradeNo'     => $tradeNo,
                    'addTime'     => $result[0]['createTime'],
                    'codeUrl'     => $requestResult['code_url']
                ]);
            }
        }
        return $this->fetch('/SystemMessage', ['msg' => '微信支付下单失败！<br>[' . $requestResult['err_code'] . '] ' . $requestResult['err_code_des']]);
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWapReturn()
    {
        $tradeNo = input('get.tradeNo');
        if (empty($tradeNo))
            return $this->fetch('/SystemMessage', ['msg' => '订单ID无效！']);
        $result = Db::table('epay_order')->where('tradeNo', $tradeNo)->field('id')->limit(1)->select();
        if (empty($result))
            return $this->fetch('/SystemMessage', ['msg' => '订单ID无效！']);
        return $this->fetch('/WxPayReturnTemplate', ['tradeNo' => $tradeNo]);
    }

    /**
     * @return mixed
     */
    public function getWapResult()
    {
        return $this->fetch('/WxPaySuccessTemplate');
    }

    /**
     * 微信统一回调地址
     * @return \think\response\Xml
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function postNotify()
    {
        $requestData = file_get_contents('php://input');
        //get post xml
        $requestData = xmlToArray($requestData);
        //数据转换
        $sign       = $requestData['sign'];
        $wxPayModel = new WxPayModel($this->systemConfig['wxpay']);
        if ($wxPayModel->signParam($requestData) != $sign)
            return xml(['return_code' => 'FAIL', 'return_msg' => '签名效验有误']);
        //check sign
        if (empty($requestData['return_code']) || empty($requestData['result_code']))
            return xml(['return_code' => 'FAIL', 'return_msg' => '订单状态无效']);
        if ($requestData['return_code'] != 'SUCCESS' && $requestData['result_code'] != 'SUCCESS')
            return xml(['return_code' => 'FAIL', 'return_msg' => '订单状态无效']);
        //check order status
        if (!$wxPayModel->checkWxPayStatus($requestData['transaction_id'], 'transaction_id'))
            return xml(['return_code' => 'FAIL', 'return_msg' => '订单付款状态效验失败']);
        //check order pay status


        $result = Db::table('epay_order')->where('tradeNo', $requestData['out_trade_no'])->field('status')->limit(1)->select();
        if (empty($result))
            return xml(['return_code' => 'FAIL', 'return_msg' => '订单无效']);
        if ($result[0]['status'])
            return xml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']);
        //订单已经付款成功

        Db::table('epay_order')->where('tradeNo', $requestData['out_trade_no'])->limit(1)->update([
            'status'  => 1,
            'endTime' => getDateTime()
        ]);
        //更新订单状态
        processOrder($requestData['out_trade_no']);
        //统一处理订单
        return xml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']);
    }

}
