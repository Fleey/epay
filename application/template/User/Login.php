<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>登陆账号 - <?php echo $webName; ?></title>
    <link rel="stylesheet" href="/static/css/resource/style.css">
    <link rel="stylesheet" href="/static/css/user/login.css">
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
                                <input type="text" class="form-control form-control-lg" placeholder="商户号"
                                       aria-label="Username" id="uid" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" class="form-control form-control-lg" id="password" placeholder="密匙"
                                       aria-label="Password" aria-describedby="basic-addon1">
                            </div>
                            <div class="loginCode"></div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck1">
                                        <label class="custom-control-label" for="customCheck1">记住账号</label>
                                        <a href="javascript:void(0)" id="to-recover" class="text-dark float-right"><i
                                                    class="fa fa-lock mr-1"></i> 忘记密码?</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-xs-12 pb-3">
                                    <button class="btn btn-block btn-lg btn-info" type="button" id="login">登陆</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="recoverform">
                <div class="logo">
                    <span class="db"><img src="/static/images/logo-icon.png" alt="logo"></span>
                    <h5 class="font-medium mb-3">聚合支付平台</h5>
                    <span>看起来您忘记了忘记了密匙<br>您需要联系管理员进行重置密匙。</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/resource/jquery.min.js"></script>
<script src="/static/js/resource/bootstrap.min.js"></script>
<script src="/static/js/resource/sweetalert2.min.js"></script>

<script>
    var baseUrl = '<?php echo url('/', '', false, true);?>';
</script>
<?php
if (!$isGeetest)
    echo '<script src="/static/js/ToolsFunction.js"></script><script src="/static/js/AuthCode.js"></script><script src="/static/js/user/loginV1.js"></script>';
else
    echo '<script src="/static/js/user/gt.js"></script><script src="/static/js/user/loginV2.js"></script>'
?>
<script>
    $('[data-toggle="tooltip"]').tooltip();
    $(".preloader").fadeOut();
    $('#to-recover').off("click").on('click', function () {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });
</script>
</body>
</html>
