<?php declare(strict_types=1);

/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

use App\Helper\Log;
use Swoft\Log\Helper\CLog;

if (!function_exists('clogLog')) {
    /**
     * 控制台输出日志，并记录到日志文件
     * @param $message
     * @param string $level
     * @param string $loggerName
     */
    function clogLog($message, string $level = 'info', string $loggerName = 'commonLogger')
    {
        CLog::$level($message);
        Log::logger($loggerName)->$level($message);
    }
}

if (!function_exists('xmlToArray')) {
    /**
     * xml转数组
     * @param string $xmlContent
     * @return mixed
     */
    function xmlToArray(string $xmlContent)
    {
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}
