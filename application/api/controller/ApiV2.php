<?php

namespace app\api\controller;

use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
use think\App;
use think\Controller;
use think\Db;

class ApiV2 extends Controller
{
    private $userKey = '';
    private $requestData = [];
    private $systemConfig;

    public function loadTemplate()
    {
        $this->systemConfig = getConfig();
        return $this->fetch('/ApiDocTemplateV2', [
            'webName' => $this->systemConfig['webName'],
            'webQQ'   => $this->systemConfig['webQQ']
        ]);
    }

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if ($this->request->url() == '/doc/v2')
            return;
        $this->requestData = input('post.');
        if (empty($this->requestData))
            $this->returnJson(['status' => 0, 'msg' => '请求数据不能为空，仅支持POST方式传递参数']);
        if (empty($this->requestData['uid']))
            $this->returnJson(['status' => 0, 'msg' => '商户号不能为空']);
        if(empty($this->requestData['sign_type']))
            $this->returnJson(['status' => 0, 'msg' => '签名效验失败，仅支持MD5签名']);
        if($this->requestData['sign_type'] != 'MD5')
            $this->returnJson(['status' => 0, 'msg' => '签名效验失败，仅支持MD5签名']);
        $userKey = Db::table('epay_user')->where('id', $this->requestData['uid'])->field('key')->limit(1)->select();
        if (empty($userKey))
            $this->returnJson(['status' => 0, 'msg' => '签名效验失败，仅支持MD5签名']);
        if (!$this->checkSign($this->requestData, $userKey, $this->requestData['sign']))
            $this->returnJson(['status' => 0, 'msg' => '签名效验失败，仅支持MD5签名']);
        //check sign
        $this->userKey = $userKey;
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function postOrder()
    {
        if (empty($this->requestData['tradeNo']) && empty($this->requestData['tradeNoOut']))
            $this->returnJson(['status' => 0, 'msg' => '平台单号 或 商家单号不能为空']);
        $searchData = [
            'uid' => $this->requestData['uid']
        ];
        if (!empty($tradeNo))
            $selectData['tradeNo'] = $tradeNo;
        if (!empty($tradeNoOut))
            $selectData['tradeNoOut'] = $tradeNoOut;
        $selectResult = Db::table('epay_order')->limit(1)->field('tradeNo,tradeNoOut,productName,money,createTime,endTime,status,type')->where($searchData)->select();
        if (empty($selectResult))
            $this->returnJson(['status' => 0, 'msg' => '订单不存在']);
        $this->returnJson([
            'status' => 1,
            'msg'    => '查询订单成功',
            'data'   => json_encode([
                'uid'          => $this->requestData['uid'],
                'trade_no'     => (string)$selectResult[0]['tradeNo'],
                'out_trade_no' => (string)$selectResult[0]['tradeNoOut'],
                'name'         => $selectResult[0]['productName'],
                'createTime'   => $selectResult[0]['createTime'],
                'endTime'      => $selectResult[0]['endTime'],
                'status'       => $selectResult[0]['status'],
                'money'        => (string)($selectResult[0]['money'] / 100),
                'type'         => $selectResult[0]['type']
            ])
        ]);
    }

    /**
     * 效验签名
     * @param array $data
     * @param $key
     * @param $sign
     * @return bool
     */
    private function checkSign(array $data, string $key, string $sign)
    {
        return verifyMD5(createLinkString(argSort(paraFilter($data))), $key, $sign);
    }

    /**
     * 签名返回数据
     * @param array $data
     */
    private function returnJson(array $data)
    {
        if (empty($this->userKey))
            exit(json_encode($data));
        //key is empty
        $args              = argSort(paraFilter($data));
        $sign              = signMD5(createLinkString($args), $this->userKey);
        $data['sign']      = $sign;
        $data['sign_type'] = 'MD5';
        exit(json_encode($data));
    }
}