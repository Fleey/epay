$(function ($) {
    var isLoginSuccess = false;
    var PostAuthCode = function (options, ClickList) {
        $.post(options.ApiServerUrl, {
            action: 'checkCode',
            page: options.Key,
            click_list: ClickList
        }, function (data) {
            if (data.state) {
                AuthCode.SuccessVerify();
                isLoginSuccess = true;
            } else {
                AuthCode.RefreshAuthCode();
            }
        }, 'json');
    };

    var AuthCode = $('.loginCode').DorpHelpAuthCode({
        'ApiServerUrl': baseUrl + 'auth/user/checkCodeImg/',
        'ResourcesUrl': baseUrl + 'auth/user/codeImg/',
        'Key': 'UserLogin',
        'PostInit': PostAuthCode
    });

    $('#login').click(function () {
        var uid = $('#uid').val();
        var password = $('#password').val();
        if (uid.length === 0) {
            swal('商户ID不能为空', {
                buttons: false,
                timer: 1500,
                icon: 'info'
            });
            return true;
        }
        if (password.length === 0) {
            swal('商户密码不能为空', {
                buttons: false,
                timer: 1500,
                icon: 'info'
            });
            return true;
        }
        if (!isLoginSuccess) {
            swal('必须要验证人机身份，您需要按照提示点击下方验证码', {
                buttons: false,
                timer: 1500,
                icon: 'info'
            });
            return true;
        }
        $.post(baseUrl + 'auth/user/Login', {
            uid: uid,
            password: password
        }, function (data) {
            if (data['status'] === 0) {
                swal(data['msg'], {
                    buttons: false,
                    timer: 1500,
                    icon: 'warning'
                });
                return true;
            }
            if (data['status'] === -1) {
                swal(data['msg'], {
                    buttons: false,
                    timer: 1500,
                    icon: 'warning'
                });
                AuthCode.RefreshAuthCode();
                return true;
            }
            swal('登陆成功，将为您转跳页面', {
                buttons: false,
                timer: 1500,
                icon: 'success'
            });
            setTimeout(function () {
                window.location.href = baseUrl + 'user/Index';
            }, 1500);
        });
    });
});