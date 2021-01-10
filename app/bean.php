<?php declare(strict_types=1);

/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

use Swoft\Crontab\Process\CrontabProcess;
use Swoft\Db\Database;
use Swoft\Db\Pool;
use Swoft\Http\Server\HttpServer;
use Swoft\Http\Server\Swoole\RequestListener;
use Swoft\Log\Handler\FileHandler;
use Swoft\Log\Logger;
use Swoft\Redis\RedisDb;
use Swoft\Rpc\Client\Client as ServiceClient;
use Swoft\Rpc\Client\Pool as ServicePool;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Server\SwooleEvent;
use Swoft\Task\Swoole\FinishListener;
use Swoft\Task\Swoole\TaskListener;
use Swoft\WebSocket\Server\WebSocketServer;

return [
    'lineFormatter'      => [
        'format'     => '[%datetime%] [%level_name%] [%event%] [%channel%]: %messages%',
        'dateFormat' => 'Y-m-d H:i:s',
    ],
    'noticeHandler'      => [
        'logFile' => '@runtime/logs/notice-%d{Y-m-d-H}.log',
    ],
    'applicationHandler' => [
        'logFile' => '@runtime/logs/error-%d{Y-m-d}.log',
    ],
    'logger'             => [
        'flushRequest' => false,
        'enable'       => true,
        'json'         => false,
    ],

    // 通用日志
    'commonLogger'       => [
        'class'        => Logger::class,
        'flushRequest' => false,
        'enable'       => true,
        'json'         => false,
        'handlers'     => [
            'common' => bean('commonHandler'),
        ],
    ],
    'commonHandler'      => [
        'class'     => FileHandler::class,
        'logFile'   => '@runtime/logs/common/common-%d{Y-m-d}.log',
        'levels'    => 'notice,info,debug,trace,error,warning',
        'formatter' => bean('lineFormatter'),
    ],

    // 通用日志
    'spiderLogger'       => [
        'class'        => Logger::class,
        'flushRequest' => false,
        'enable'       => true,
        'json'         => false,
        'handlers'     => [
            'common' => bean('spiderHandler'),
        ],
    ],
    'spiderHandler'      => [
        'class'     => FileHandler::class,
        'logFile'   => '@runtime/logs/spider/spider-%d{Y-m-d}.log',
        'levels'    => 'notice,info,debug,trace,error,warning',
        'formatter' => bean('lineFormatter'),
    ],

    'httpServer'     => [
        'class'    => HttpServer::class,
        'port'     => 18306,
        'listener' => [
            // 'rpc' => bean('rpcServer'),
            // 'tcp' => bean('tcpServer'),
        ],
        'process'  => [
            // 'monitor' => bean(\App\Process\MonitorProcess::class)
            'crontab' => bean(CrontabProcess::class)
        ],
        'on'       => [
            // SwooleEvent::TASK   => bean(SyncTaskListener::class),  // Enable sync task
            SwooleEvent::TASK   => bean(TaskListener::class),  // Enable task must task and finish event
            SwooleEvent::FINISH => bean(FinishListener::class),
        ],
        /* @see HttpServer::$setting */
        'setting'  => [
            'task_worker_num'       => 12,
            'task_enable_coroutine' => true,
            'worker_num'            => 6,
            // static handle
            // 'enable_static_handler'    => true,
            // 'document_root'            => dirname(__DIR__) . '/public',
        ],
    ],
    'httpDispatcher' => [
        // Add global http middleware
        'middlewares'      => [
            \App\Http\Middleware\FavIconMiddleware::class,
            \Swoft\Http\Session\SessionMiddleware::class,
            // \Swoft\Whoops\WhoopsMiddleware::class,
            // Allow use @View tag
            \Swoft\View\Middleware\ViewMiddleware::class,
        ],
        'afterMiddlewares' => [
            \Swoft\Http\Server\Middleware\ValidatorMiddleware::class,
        ],
    ],

    // epay 库（默认）
    'db'             => [
        'class'    => Database::class,
        'dsn'      => env('EPAY_DB_DSN', 'mysql:dbname=test;host=127.0.0.1;port=3306'),
        'username' => env('EPAY_DB_USERNAME', 'root'),
        'password' => env('EPAY_DB_PASSWORD', ''),
        'charset'  => env('EPAY_DB_CHARSET', 'utf8mb4'),
        'prefix'   => env('EPAY_DB_PREFIX', ''),
        'config'   => [
            'timezone'  => env('EPAY_DB_TIMEZONE', '+8:00'),
            'collation' => env('EPAY_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'strict'    => env('EPAY_DB_STRICT_MODE', false),
        ],
        'options'  => [
            // mysql连接超时设置
            PDO::ATTR_TIMEOUT => env('EPAY_DB_CONNECTION_TIMEOUT', 3),
        ],
    ],
    'db.pool'        => [
        'class'       => Pool::class,
        'database'    => bean('db'),
        'minActive'   => env('APP_DEBUG', 1) ? 2 : 5,
        'maxActive'   => 10,
        'maxWait'     => 0,
        'maxWaitTime' => 30,
        'maxIdleTime' => 30,
    ],

    'migrationManager'  => [
        'migrationPath' => '@database/Migration',
    ],
    'redis'             => [
        'class'    => RedisDb::class,
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'database' => 0,
        'option'   => [
            'prefix' => 'swoft:',
        ],
    ],
    'user'              => [
        'class'   => ServiceClient::class,
        'host'    => '127.0.0.1',
        'port'    => 18307,
        'setting' => [
            'timeout'         => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 0.5,
        ],
        'packet'  => bean('rpcClientPacket'),
    ],
    'user.pool'         => [
        'class'  => ServicePool::class,
        'client' => bean('user'),
    ],
    'rpcServer'         => [
        'class'    => ServiceServer::class,
        'listener' => [
            'http' => bean('httpServer'),
        ],
    ],
    'wsServer'          => [
        'class'    => WebSocketServer::class,
        'port'     => 18308,
        'listener' => [
            'rpc' => bean('rpcServer'),
            // 'tcp' => bean('tcpServer'),
        ],
        'on'       => [
            // Enable http handle
            SwooleEvent::REQUEST => bean(RequestListener::class),
            // Enable task must add task and finish event
            SwooleEvent::TASK    => bean(TaskListener::class),
            SwooleEvent::FINISH  => bean(FinishListener::class),
        ],
        'debug'    => 1,
        // 'debug'   => env('SWOFT_DEBUG', 0),
        /* @see WebSocketServer::$setting */
        'setting'  => [
            'task_worker_num'       => 6,
            'task_enable_coroutine' => true,
            'worker_num'            => 6,
            'log_file'              => alias('@runtime/swoole.log'),
            // 'open_websocket_close_frame' => true,
        ],
    ],
    // 'wsConnectionManager' => [
    //     'storage' => bean('wsConnectionStorage')
    // ],
    // 'wsConnectionStorage' => [
    //     'class' => \Swoft\Session\SwooleStorage::class,
    // ],
    /** @see \Swoft\WebSocket\Server\WsMessageDispatcher */
    'wsMsgDispatcher'   => [
        'middlewares' => [
            \App\WebSocket\Middleware\GlobalWsMiddleware::class,
        ],
    ],
    /** @see \Swoft\Tcp\Server\TcpServer */
    'tcpServer'         => [
        'port'  => 18309,
        'debug' => 1,
    ],
    /** @see \Swoft\Tcp\Protocol */
    'tcpServerProtocol' => [
        // 'type' => \Swoft\Tcp\Packer\JsonPacker::TYPE,
        'type' => \Swoft\Tcp\Packer\SimpleTokenPacker::TYPE,
        // 'openLengthCheck' => true,
    ],
    /** @see \Swoft\Tcp\Server\TcpDispatcher */
    'tcpDispatcher'     => [
        'middlewares' => [
            \App\Tcp\Middleware\GlobalTcpMiddleware::class,
        ],
    ],
    'cliRouter'         => [// 'disabledGroups' => ['demo', 'test'],
    ],
];
