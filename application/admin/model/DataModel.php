<?php

namespace app\admin\model;

use think\Db;

class DataModel
{
    /**
     * 获取数据模型数据
     * @param string $attrName
     * @param string $strTime
     * @return array
     */
    public static function getData(string $attrName, string $strTime)
    {
        try {
            $result = Db::table('epay_data_model')->where([
                'attrName'   => $attrName,
                'createTime' => $strTime
            ])->limit(1)->field('data')->select();
            if (empty($result))
                return [false, '不存在'];
            return [true, $result[0]['data']];
        } catch (\Exception $exception) {
            return [false, $exception->getMessage()];
        }
    }

    /**
     * 设置数据
     * @param string $attrName
     * @param string $strTime
     * @param int $data
     * @param string $type
     * @return bool
     */
    public static function setData(string $attrName, string $strTime, int $data, string $type = 'add')
    {
        if ($type != 'add' && $type != 'dec')
            return false;
        try {
            if (self::getData($attrName, $strTime)[0]) {
                $result = Db::table('epay_data_model')->where([
                    'attrName'   => $attrName,
                    'createTime' => $strTime
                ]);
                if ($type == 'add') {
                    $result = $result->inc('data', $data);
                } else {
                    $result = $result->dec('data', $data);
                }
                $result = $result->limit(1)->update();
            } else {
                $result = Db::table('epay_data_model')->insertGetId([
                    'attrName'   => $attrName,
                    'createTime' => $strTime,
                    'data'       => $data
                ]);
            }
            return $result != 0;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 删除数据
     * @param string $attrName
     * @param string $strTime
     * @return bool
     */
    public static function removeData(string $attrName, string $strTime)
    {
        try {
            $result = Db::table('epay_data_model')->where([
                'attrName'   => $attrName,
                'createTime' => $strTime
            ])->limit(1)->delete();
            return $result != 0;
        } catch (\Exception $exception) {
            return false;
        }
    }
}