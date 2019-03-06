<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/static/css/resource/style.css">
    <link rel="stylesheet" href="/static/css/user/login.css">
    <title>登陆账号 - <?php echo $webName; ?></title>
</head>
<body>
<div class="main-wrapper">
    <div class="preloader" style="display: none;">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
        <div class="auth-box">
            <div id="loginform">
                <div class="logo">
                    <span class="db"><img src="/static/images/logo-icon.png" alt="logo"></span>
                    <h5 class="font-medium mb-3"><?php echo $webName; ?>聚合支付平台</h5>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form class="form-horizontal mt-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-lg" placeholder="管理员账号"
                                       aria-label="Username" id="username" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" class="form-control form-control-lg" id="password" placeholder="管理员密码"
                                       aria-label="Password" aria-describedby="basic-addon1">
                            </div>
                            <div class="loginCode"></div>
                            <div class="form-group text-center">
                                <div class="col-xs-12 pb-3">
                                    <button class="btn btn-block btn-lg btn-info" type="button" id="login">登陆</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var baseUrl = '<?php echo url('/', '', false, true);?>';
</script>
<script src="/static/js/resource/jquery.min.js"></script>
<script src="/static/js/resource/bootstrap.min.js"></script>
<script src="/static/js/resource/sweetalert2.min.js"></script>
<?php
if (!$isGeetest)
    echo '<script src="/static/js/AuthCode.js"></script><script src="/static/js/admin/loginV1.js"></script>';
else
    echo '<script src="/static/js/user/gt.js"></script><script src="/static/js/admin/loginV2.js"></script>'
?>
</body>
</html>
