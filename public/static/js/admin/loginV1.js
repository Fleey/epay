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
        'ApiServerUrl': baseUrl + 'auth/admin/checkCodeImg/',
        'ResourcesUrl': baseUrl + 'auth/admin/codeImg/',
        'Key': 'AdminLogin',
        'PostInit': PostAuthCode
    });

    $('#login').click(function () {
        var username = $('#username').val();
        var password = $('#password').val();
        if (username.length === 0) {
            swal({
                title:'',
                text: '管理员账号不能为空',
                showConfirmButton: false,
                timer: 1500,
                type: 'info'
            });
            return true;
        }
        if (password.length === 0) {
            swal({
                text: '管理员密码不能为空',
                showConfirmButton: false,
                timer: 1500,
                type: 'info'
            });
            return true;
        }
        if (!isLoginSuccess) {
            swal({
                title: '',
                text: '必须要验证人机身份，您需要按照提示点击下方验证码',
                showConfirmButton: false,
                timer: 1500,
                type: 'info'
            });
            return true;
        }
        $.post(baseUrl + 'auth/admin/Login', {
            username: username,
            password: password
        }, function (data) {
            if (data['status'] === 0) {
                swal({
                    title:'',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'warning'
                });
                return true;
            }
            if (data['status'] === -1) {
                swal({
                    title:'',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'warning'
                });
                $('#password').val('');
                AuthCode.RefreshAuthCode();
                return true;
            }
            swal({
                title:'',
                text: '登陆成功，将为您转跳页面',
                showConfirmButton: false,
                timer: 1500,
                type: 'success'
            });
            setTimeout(function () {
                window.location.href = baseUrl + 'cy2018/Index';
            }, 1500);
        });
    });
});