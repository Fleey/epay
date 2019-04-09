<?php

namespace app\pay\model;

use think\Db;
use think\Exception;

class PayModel
{

    /**
     * 判断违禁词 如果出现违禁词则会抛出异常
     * @param array $systemConfig
     * @param string $name
     * @throws Exception
     */
    public static function checkBadWord(array $systemConfig, string $name)
    {
        $badWordList = str_replace('，', ',', $systemConfig['goodsFilter']['keyWord']);
        $badWordList = str_replace('、', ',', $badWordList);
        $badWordList = explode(',', $badWordList);
        if (!empty($badWordList)) {
            $blackReg = '/' . implode('|', $badWordList) . '/i';
            if (preg_match($blackReg, $name, $matches))
                throw new Exception($systemConfig['goodsFilter']['tips']);
        }
    }

    /**
     * @param $money
     * @param int $uid
     * @param array $systemConfig
     * @throws Exception
     */
    public static function checkUserMaxPayMoney($money, int $uid, array $systemConfig)
    {
        $maxPayMoney    = getPayUserAttr($uid, 'payMoneyMax');
        $maxPayMoneyDay = getPayUserAttr($uid, 'payDayMoneyMax');
        if (!empty($maxPayMoneyDay)) {
            $maxPayMoneyDay = intval($maxPayMoneyDay);
            $todayMoney     = Db::table('epay_order')->where([
                'uid'    => $uid,
                'status' => 1
            ])->whereTime('endTime', 'today')->sum('money');
            if ($maxPayMoneyDay < $todayMoney)
                throw new Exception('[10003]超出商户单日订单总金额上限');
        }

        if (!empty($maxPayMoney)) {
            $maxPayMoney = intval($maxPayMoney);
            if ($money > $maxPayMoney)
                throw new Exception('[10001]超出商户单个订单最大支付金额');
        } else {
            if ($money > $systemConfig['defaultMaxPayMoney'])
                throw new Exception('[10002]超出商户单个订单最大支付金额');
        }
    }

    /**
     * 返回订单减免金额
     * @param int $uid
     * @param $orderMoney
     * @return int
     */
    public static function getOrderDiscountMoney(int $uid, $orderMoney)
    {
        if (empty($uid) || empty($orderMoney))
            return 0;
        $discountData = getPayUserAttr($uid, 'orderDiscounts');
        if (empty($discountData))
            return 0;
        $discountData = unserialize($discountData);
        if (empty($discountData))
            return 0;
        //判断数据准确性
        if (!$discountData['isOpen'])
            return 0;
        //减免功能关闭
        if ($discountData['minMoney'] * 100 >= $orderMoney)
            return 0;
        //小于等于 最小金额则不进行减免操作
        $discountMoney = 0;

        if ($discountData['type'] == 0) {
            if (count($discountData['moneyList']) != 0)
                $discountMoney = number_format($discountData['moneyList'][0], 2) * 100;
            //固定减免
        } else if ($discountData['type'] == 1) {
            if (count($discountData['moneyList']) == 0)
                return 0;
            $randNumber    = rand(0, count($discountData['moneyList']) - 1);
            $discountMoney = number_format($discountData['moneyList'][$randNumber], 2) * 100;
        }

        return $discountMoney;
    }

    /**
     * 转换支付名称 主要为了兼容老接口 和 优化数据库
     * @param $payName
     * @param bool $isReversal
     * @return int
     */
    public static function converPayName($payName, $isReversal = false)
    {
        if ($isReversal) {
            switch ($payName) {
                case 1:
                    $payName = 'wxpay';
                    break;
                case 3:
                    $payName = 'alipay';
                    break;
                case 2:
                    $payName = 'tenpay';
                    break;
                case 4:
                    $payName = 'bankpay';
                    break;
                default:
                    $payName = 'null';
                    break;
            }
        } else {
            switch ($payName) {
                case 'wxpay':
                    $payName = 1;
                    break;
                case 'alipay':
                    $payName = 3;
                    break;
                case 'qqpay':
                    $payName = 2;
                    break;
                case 'tenpay':
                    $payName = 2;
                    break;
                case 'bankpay':
                    $payName = 4;
                    break;
                default:
                    $payName = 0;
                    break;
            }
        }
        return $payName;
    }

    /**
     * 效验签名
     * @param array $data
     * @param $key
     * @param $sign
     * @return bool
     */
    public static function checkSign(array $data, string $key, string $sign)
    {
        return verifyMD5(createLinkString(argSort(paraFilter($data))), $key, $sign);
    }

}
