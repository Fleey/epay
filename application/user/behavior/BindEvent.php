<?php

namespace app\user\behavior;
class BindEvent
{
    public function run($params)
    {
        if (config('app_debug')) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }
    }
}