<?php

namespace app\command;

use app\admin\controller\Wxx;
use app\admin\model\DataModel;
use app\pay\controller\WxPay;
use app\pay\model\PayModel;
use app\pay\model\QQPayModel;
use app\pay\model\WxPayModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use function GuzzleHttp\Psr7\build_query;


class Test extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('test')->setDescription('user test');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
//        $deleteTime = '- ' . 5 . ' day';
//        Db::table('epay_order')->whereTime('createTime', '<=', $deleteTime)->delete();
//        echo '1'.PHP_EOL;
//        Db::table('epay_order_attr')->whereTime('createTime', '<=', $deleteTime)->delete();
//        echo '2'.PHP_EOL;
//        Db::table('epay_settle')->whereTime('createTime', '<=', $deleteTime)->delete();
//        echo '3'.PHP_EOL;
//        Db::table('epay_log')->whereTime('createTime', '<=', $deleteTime)->delete();
//        echo '4'.PHP_EOL;
//        Db::table('epay_wxx_trade_record')->whereTime('createTime', '<=', $deleteTime)->delete();

        //start 2019-11-01 18:25:28
        //end 2019-11-01 21:30:00


        $errorApplyInfo = Db::table('epay_wxx_apply_info')->where('type', 2)->field('id')->cursor();
        foreach ($errorApplyInfo as $applyInfo) {
            $id          = $applyInfo['id'];
            $errorRelate = Db::table('epay_wxx_apply_info_relate')->where('applyInfoID', $id)->field('uid')->cursor();
            foreach ($errorRelate as $errorRelate) {
                $uid        = $errorRelate['uid'];
                $errorData  = Db::table('epay_order')->where([
                    'type'   => 1,
                    'status' => 1,
                    'uid'    => $uid
                ])->where('createTime', '>=', '2019-11-01 21:15:00')->where('createTime', '<', '2019-11-01 21:30:00')->field('tradeNo,money')->cursor();
                $totalMoney = 0;
                foreach ($errorData as $content) {
                    $data = PayModel::getOrderAttr($content['tradeNo'], 'rateMoney');
                    if (empty($data)) {
                        $removeMoney = PayModel::getOrderRateMoney($uid, $content['money']) + ($content['money'] * 10);
                        $totalMoney  += $removeMoney;

                        DB::table('epay_user')->where('id', $uid)->limit(1)->dec('balance', $removeMoney)->update();
                        //错误单子
                    }
                }
                echo 'uid => ' . $uid . ' money => ' . ($totalMoney / 1000) . PHP_EOL;
            }
        }

        exit();

        $fileContent = file_get_contents('./id.txt');
        $row         = explode(PHP_EOL, $fileContent);
        $a           = [];
        foreach ($row as $tradeNoOut) {
            $orderData    = Db::table('epay_order')->where('tradeNoOut', $tradeNoOut)->field('tradeNo,money,uid,type,status')->limit(1)->select();
            $systemConfig = getConfig();
            $tradeNo      = $orderData[0]['tradeNo'];
            if ($orderData[0]['status'] != 1)
                continue;
            if ($orderData[0]['type'] == 1) {
                try {
                    $payConfig = json_decode(PayModel::getOrderAttr($tradeNo, 'payConfig'), true);
                    $wxPay     = new WxPayModel(WxPay::getWxxPayConfig($tradeNo, $systemConfig));
                    $result    = $wxPay->orderRefund($tradeNo, $orderData[0]['money'], $orderData[0]['money'], Wxx::getWxxCertFilePath($payConfig['accountID']), 'https://ceo.st227.com/Pay/WxPay/RefundNotify', '数据丢失，请重新下单');
                    if (!$result[0]) {
                        echo 'wx tradeNo => ' . $tradeNo . ' error => ' . $result[1] . PHP_EOL;
                        continue;
                    }
                    if ($payConfig['configType'] == 1)
                        Db::table('epay_user')->where('id', $orderData[0]['uid'])->limit(1)->dec('balance', $orderData[0]['money'] * 10)->update();
                    Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
                        'status' => 3
                    ]);
                } catch (\Exception $e) {
                    trace('[订单退款异常]微信支付 tradeNo => ' . $tradeNo . '  ' . $e->getMessage(), 'error');
                    echo '[订单退款异常]微信支付 tradeNo => ' . $tradeNo . '  ' . $e->getMessage() . PHP_EOL;
                }
            } else if ($orderData[0]['type'] == 2) {
                $QQPayModel = new QQPayModel($systemConfig['qqpay']);
                try {
                    $result = $QQPayModel->orderRefund($tradeNo, $orderData[0]['money']);
                    if (!$result[0]) {
                        echo 'qq tradeNo => ' . $tradeNo . ' error => ' . $result[1] . PHP_EOL;
                        continue;
                    }
                    Db::table('epay_user')->where('id', $orderData[0]['uid'])->limit(1)->dec('balance', $orderData[0]['money'] * 10)->update();
                    Db::table('epay_order')->where('tradeNo', $tradeNo)->limit(1)->update([
                        'status' => 4
                    ]);
                } catch (\Exception $e) {
                    trace('[订单退款异常]QQ钱包 tradeNo => ' . $tradeNo . '  ' . $e->getMessage(), 'error');
                    echo '[订单退款异常]微信支付 tradeNo => ' . $tradeNo . '  ' . $e->getMessage() . PHP_EOL;
                }
            } else {
                $a[] = $tradeNo;
            }
        }
        dump($a);
    }


    /**
     * 构建url请求参数
     * @param array $data
     * @return string
     */
    public function buildUrlParam(array $data)
    {
        $tempBuff = '';
        foreach ($data as $key => $value) {
            if ($key != 'sign')
                $tempBuff .= $key . '=' . $value . '&';
        }
        $tempBuff = trim($tempBuff, '&');
        return $tempBuff;
    }


    protected function curl($url = '', $addHeaders = [], $requestType = 'get', $requestData = '', $postType = '', $urlEncode = true)
    {
        if (empty($url))
            return '';
        //容错处理
        $headers  = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36'
        ];
        $postType = strtolower($postType);
        if ($requestType != 'get') {
            if ($postType == 'json') {
                $headers[]   = 'Content-Type: application/json; charset=utf-8';
                $requestData = is_array($requestData) ? json_encode($requestData) : $requestData;
            } else if ($postType == 'xml') {
                $headers[] = 'Content-Type:text/xml; charset=utf-8';
            }
            $headers[] = 'Content-Length: ' . strlen($requestData);
        }
        if ($requestType == 'get' && is_array($requestData)) {
            $tempBuff = '';
            foreach ($requestData as $key => $value) {
                $tempBuff .= $key . '=' . $value . '&';
            }
            $tempBuff = trim($tempBuff, '&');
            $url      .= '?' . $tempBuff;
        }
        //手动build get请求参数

        if (!empty($addHeaders))
            $headers = array_merge($headers, $addHeaders);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //设置允许302转跳
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
//        curl_setopt($ch, CURLOPT_PROXY, '116.255.172.156'); //代理服务器地址
//        curl_setopt($ch, CURLOPT_PROXYPORT, 16819); //代理服务器端口
        //set proxy
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        //gzip

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //add ssl
        if ($requestType == 'get') {
            curl_setopt($ch, CURLOPT_HEADER, false);
        } else if ($requestType == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($requestType));
        }
        //处理类型
        if ($requestType != 'get') {
            if (is_array($requestData) && !empty($requestData)) {
                $temp = '';
                foreach ($requestData as $key => $value) {
                    if ($urlEncode) {
                        $temp .= rawurlencode(rawurlencode($key)) . '=' . rawurlencode(rawurlencode($value)) . '&';
                    } else {
                        $temp .= $key . '=' . $value . '&';
                    }
                }
                $requestData = substr($temp, 0, strlen($temp) - 1);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        }
        //只要不是get姿势都塞东西给他post
        $result   = curl_exec($ch);
        $errorMsg = '';
        if ($result === false)
            $errorMsg = curl_error($ch);
        curl_close($ch);

        return ['isSuccess' => ($result !== false), 'errorMsg' => $errorMsg];
    }
}
