<?php

use Swoft\Log\Handler\FileHandler;
use Swoft\Log\Logger;

return [
    'lineFormatter'      => [
        'format'     => '[%datetime%] [%level_name%] [%event%] [%channel%]: %messages%',
        'dateFormat' => 'Y-m-d H:i:s',
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
];
