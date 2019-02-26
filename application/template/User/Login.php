<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php include_once env('APP_PATH') . '/template/User/Head.php'; ?>
    <title>登陆账号 - <?php echo $webName; ?></title>
    <link rel="stylesheet" href="/static/css/user/login.css">
</head>

<body>
<form class="form-signin">
    <div class="text-center mb-4">
        <h1 class="h3 mb-3 font-weight-normal">登陆系统</h1>
        <p>请输入商户ID和商户密匙，并且验证人机身份</p>
    </div>

    <div class="form-label-group">
        <input type="email" id="uid" class="form-control" placeholder="商户ID" required autofocus>
        <label for="uid">商户ID</label>
    </div>

    <div class="form-label-group">
        <input type="password" id="password" class="form-control" placeholder="商户密匙" required autofocus>
        <label for="password">商户密匙</label>
    </div>
    <div class="loginCode"></div>
    <button class="btn btn-lg btn-primary btn-block" type="button" id="login">登陆</button>
    <p class="mt-5 mb-3 text-muted text-center"><?php echo $webName; ?>&copy; 2017-2018</p>
</form>
<?php
include_once env('APP_PATH') . '/template/User/Footer.php';
if (!$isGeetest)
    echo '<script src="/static/js/AuthCode.js"></script><script src="/static/js/user/loginV1.js"></script>';
else
    echo '<script src="/static/js/user/gt.js"></script><script src="/static/js/user/loginV2.js"></script>'
?>
</body>
</html>
