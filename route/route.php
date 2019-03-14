<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::rule('', 'admin/Index/index');
Route::group('test', function () {
    Route::post('pay', 'user/Test/pay');
    Route::get('return', 'user/Test/getReturn');
    Route::get('notify', 'user/Test/getNotify');
    Route::rule('', 'user/Test/loadTemplate');
});
Route::group('Pay', function () {
    Route::controller('Alipay', 'pay/Alipay');
    Route::controller('QQPay', 'pay/QQPay');
    Route::controller('WxPay', 'pay/WxPay');
    Route::rule('Status', 'pay/Index/OrderStatus');
});
Route::rule('install', function () {
    if (file_exists(env('CONFIG_PATH') . 'install.lock'))
        return '您已经安装过了 如果不是已经安装过请删掉 install.lock文件';

    $sql = file_get_contents(env('CONFIG_PATH') . 'install.sql');
    \think\Db::execute($sql);
    $username = 'root';
    $password = 'root';

    $salt     = getRandChar(6);
    $saveData = [
        'salt'     => $salt,
        'username' => $username,
        'password' => hash('sha256', hash('sha256', $password) . $salt)
    ];
    setServerConfig('adminAccount', serialize($saveData));
    file_put_contents(env('CONFIG_PATH') . 'install.lock', 'lock');
    return '安装成功 管理员账号密码 root root';
});
Route::group('auth', function () {
    Route::controller('user', 'user/Auth');
    Route::controller('admin', 'admin/Auth');
});
Route::group('cy2018', function () {
    Route::controller('file', 'admin/File');
    Route::get('file/filePath/<fileID>.json', 'admin/File/getFilePath', ['cache' => 3600], ['fileID' => '\d+']);
    Route::controller('api', 'admin/Index');
    Route::rule('[:templateName]', 'admin/Index/loadTemplate');
});
Route::group('user', function () {
    Route::controller('api', 'user/Index');
    Route::rule('[:templateName]', 'user/Index/loadTemplate');
});
Route::group('api', function () {
    Route::controller('v2', 'api/ApiV2');
});
Route::group('doc', function () {
    Route::rule('v1', 'api/ApiV1/loadTemplate');
    Route::rule('v2', 'api/ApiV2/loadTemplate');
});
Route::rule('submit.php', 'pay/Index/submit');
Route::rule('api.php', 'api/ApiV1/apiCtrl');
Route::rule('qrcode.php', 'api/ApiV1/apiQrCode');
return [

];
