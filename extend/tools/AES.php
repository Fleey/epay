<?php
/**
 * Created by Fleey.
 * User: Fleey
 * Date: 2018/12/10
 * Time: 14:33
 */

namespace tools;

/**
 * Class AES
 * 用于AES加解密数据
 */
class AES
{
    public static function encrypt($input, $key)
    {
        $data = openssl_encrypt($input, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return $data;
    }

    public static function decrypt($sStr, $sKey)
    {
        $decrypted = openssl_decrypt(base64_decode($sStr), 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA);
        return $decrypted;
    }
}