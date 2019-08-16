<?php

namespace app\command;

use app\pay\model\PayModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

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
        Db::table('epay_wxx_apply_list')->where('id', '<>', 0)->update([
            'rounds'    => 0,
            'tempMoney' => 0
        ]);
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
