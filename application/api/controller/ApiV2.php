<?php

namespace app\api\controller;

use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
use think\App;
use think\Controller;
use think\Db;

class ApiV2 extends Controller
{
    private $requestData = [];
    private $uid = 0;
    private $userKey = '';
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
        if (empty($this->requestData['time']))
            $this->returnJson(['status' => 0, 'msg' => '时间戳不能为空']);
        if (time() - intval($this->requestData['time']) > 180)
            $this->returnJson(['status' => 0, 'msg' => '校验失效,数据请求超时（三分钟）']);
        if (empty($this->requestData['sign_type']))
            $this->returnJson(['status' => 0, 'msg' => '[10001]签名效验失败，仅支持MD5签名']);
        if ($this->requestData['sign_type'] != 'MD5')
            $this->returnJson(['status' => 0, 'msg' => '[10002]签名效验失败，仅支持MD5签名']);
        $userKey = Db::table('epay_user')->where('id', $this->requestData['uid'])->field('key,isBan')->limit(1)->select();
        if (empty($userKey))
            $this->returnJson(['status' => 0, 'msg' => '[10003]签名效验失败，仅支持MD5签名']);
        $userKey = $userKey[0]['key'];
        if (!$this->checkSign($this->requestData, $userKey, $this->requestData['sign']))
            $this->returnJson(['status' => 0, 'msg' => '[10004]签名效验失败，仅支持MD5签名']);
        //check sign
        if ($userKey['isBan'])
            $this->returnJson(['status' => 0, 'msg' => '[10005]账户封禁，详细请联系客服人员']);
        $this->uid         = $this->requestData['uid'];
        $this->requestData = json_decode(urldecode($this->requestData['data']), true);
        $this->userKey     = $userKey;
    }

    public function postPay()
    {
        if (empty($this->requestData['tradeNoOut']))
            $this->returnJson(['status' => 0, 'msg' => '商户订单号不能为空']);
        if (!preg_match('/^[a-zA-Z0-9.\_\-|]{1,64}+$/', $this->requestData['tradeNoOut']))
            $this->returnJson(['status' => 0, 'msg' => '商户订单号格式不正确 最小订单号位一位 最大为64位']);
        if (empty($this->requestData['money']))
            $this->returnJson(['status' => 0, 'msg' => '订单金额不能为空']);
        if (empty($this->requestData['payType']))
            $this->returnJson(['status' => 0, 'msg' => '支付类型不能为空']);
        if (empty($this->requestData['productName']))
            $this->returnJson(['status' => 0, 'msg' => '商品名称不能为空']);
        if (empty($this->requestData['notifyUrl']))
            $this->returnJson(['status' => 0, 'msg' => '异步回调地址不能为空']);
        if (empty($this->requestData['returnUrl']))
            $this->returnJson(['status' => 0, 'msg' => '同步回调地址不能为空']);
        if (!is_IntOrDecimal($this->requestData['money']))
            $this->returnJson(['status' => 0, 'msg' => '订单金额格式不正确']);
        //判断金额格式 禁止那些E
        $requestParam              = [
            'pid'          => $this->uid,
            'type'         => $this->requestData['payType'],
            'out_trade_no' => $this->requestData['tradeNoOut'],
            'notify_url'   => $this->requestData['notifyUrl'],
            'return_url'   => $this->requestData['returnUrl'],
            'name'         => $this->requestData['productName'],
            'money'        => $this->requestData['money'],
            'sitename'     => empty($this->requestData['siteName']) ? '聚合支付平台' : $this->requestData['siteName']
        ];
        $sign                      = signMD5(createLinkString(argSort(paraFilter($requestParam))), $this->userKey);
        $requestParam['sign']      = $sign;
        $requestParam['sign_type'] = 'MD5';
        $buildUrl                  = $this->request->root(true);
        $buildUrl                  .= '/submit.php?' . createLinkStringUrlEncode($requestParam);
        $this->returnJson(['status' => 1, 'msg' => '生成支付链接成功', 'data' => json_encode([
            'url' => $buildUrl
        ])]);
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function postOrderList()
    {
        $limit = 20;
        $page  = 1;
        if (!empty($this->requestData['limit']))
            $limit = intval($this->requestData['limit']);
        if ($limit > 50 || $limit <= 0)
            $limit = 20;
        if (!empty($this->requestData['page']))
            $page = intval($this->requestData['page']);
        if ($page <= 0)
            $page = 1;

        $selectResult = Db::table('epay_order')
            ->field('type,tradeNo,tradeNoOut,productName,createTime,endTime,status,money')
            ->order('createTime desc')->where('uid', $this->uid)->page($page, $limit)->select();
        if (empty($selectResult))
            $this->returnJson(['status' => 0, 'msg' => '暂无查询到更多的订单']);

        foreach ($selectResult as $key => $value) {
            $selectResult[$key]['trade_no'] = (string)$value['trade_no'];
            $selectResult[$key]['money']    = $value['money'] / 100;
        }

        $this->returnJson([
            'status' => 1,
            'msg'    => '查询订单记录成功',
            'data'   => json_encode($selectResult)
        ]);
    }

    public function postSettleList()
    {
        $settleResult = Db::table('epay_settle')->where('uid', $this->uid)
            ->field('clearType,account,username,money,status,createTime,updateTime')
            ->order('id', 'desc')->limit(10)->select();
        if (empty($settleResult))
            $this->returnJson(['status' => 0, 'msg' => '暂无查询到结算记录']);
        $this->returnJson([
            'status' => 1,
            'msg'    => '查询结算记录成功',
            'data'   => $settleResult
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
        $str1 = createLinkString(argSort(paraFilter($data, true)));
        $str1 = md5($str1 . $key);
        return $str1 == $sign;
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
        $data['time']      = time();
        $args              = argSort(paraFilter($data, false));
        $sign              = md5(createLinkString($args) . $this->userKey);
        $data['sign']      = $sign;
        $data['sign_type'] = 'MD5';
        exit(json_encode($data));
    }

}