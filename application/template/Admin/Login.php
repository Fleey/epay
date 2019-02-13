<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php include_once env('APP_PATH') . '/template/Admin/Head.php'; ?>
    <title>登陆账号 - <?php echo $webName; ?></title>
    <link rel="stylesheet" href="/static/css/user/login.css">
</head>

<body>
<form class="form-signin">
    <div class="text-center mb-4">
        <h1 class="h3 mb-3 font-weight-normal">登陆系统</h1>
        <p>请输入管理员账号和密码，并且验证人机身份</p>
    </div>

    <div class="form-label-group">
        <input type="text" id="username" class="form-control" placeholder="管理员账号" required autofocus>
        <label for="username">管理员账号</label>
    </div>

    <div class="form-label-group">
        <input type="password" id="password" class="form-control" placeholder="管理员密码" required autofocus>
        <label for="password">管理员密码</label>
    </div>
    <div class="loginCode"></div>
    <button class="btn btn-lg btn-primary btn-block" type="button" id="login">登陆</button>
    <p class="mt-5 mb-3 text-muted text-center"><?php echo $webName; ?>&copy; 2017-2018</p>
</form>
<?php include_once env('APP_PATH') . '/template/Admin/Footer.php'; ?>
<script src="/static/js/AuthCode.js"></script>
<script src="/static/js/admin/login.js"></script>
</body>
</html>
