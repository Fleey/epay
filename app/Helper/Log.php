<?php


namespace App\Helper;

use Swoft\Bean\BeanFactory;
use Swoft\Log\Helper\Log as BaseLog;
use Swoft\Log\Logger;

class Log extends BaseLog
{
    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function emergency(string $message, ...$params): bool
    {
        [$message, $context] = static::formatLog($message, $params);

        return static::getLogger()->emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function debug(string $message, ...$params): bool
    {
        [$message, $context] = static::formatLog($message, $params);

        if (APP_DEBUG) {
            return static::getLogger()->debug($message, $context);
        }

        return true;
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function alert(string $message, ...$params): bool
    {
        [$message, $context] = static::formatLog($message, $params);

        return static::getLogger()->alert($message, $context);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function info(string $message, ...$params): bool
    {
        [$message, $context] = static::formatLog($message, $params);

        return static::getLogger()->info($message, $context);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function warning(string $message, ...$params): bool
    {
        [$message, $context] = static::formatLog($message, $params);

        return static::getLogger()->warning($message, $context);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     */
    public static function error(string $message, ...$params): bool
    {
        [$message, $context] = static::formatLog($message, $params);

        return static::getLogger()->error($message, $context);
    }

    /**
     * Push log
     *
     * @param string $key
     * @param mixed  $val
     *
     */
    public static function pushLog(string $key, $val): void
    {
        static::getLogger()->pushLog($key, $val);
    }

    /**
     * Profile start
     *
     * @param string $name
     * @param array  $params
     *
     */
    public static function profileStart(string $name, ...$params): void
    {
        if ($params) {
            $name = sprintf($name, ...$params);
        }

        static::getLogger()->profileStart($name);
    }

    /**
     * @param string   $name
     * @param int      $hit
     * @param int|null $total
     *
     */
    public static function counting(string $name, int $hit, int $total = null): void
    {
        static::getLogger()->counting($name, $hit, $total);
    }

    /**
     * @return Logger
     */
    public static function getLogger(): Logger
    {
        return BeanFactory::getBean('commonLogger');
    }

    /**
     * @param string $loggerName
     * @return \Swoft\Log\Logger
     */
    public static function logger(string $loggerName): Logger
    {
        return BeanFactory::getBean($loggerName);
    }
}
