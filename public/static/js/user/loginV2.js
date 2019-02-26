$(function ($) {
    var handlerEmbed = function (captchaObj) {
        $("#login").click(function (e) {
            var validate = captchaObj.getValidate();
            if (!validate) {
                swal('必须要验证人机身份，您需要按照提示点击下方验证码', {
                    buttons: false,
                    timer: 1500,
                    icon: 'info'
                });
                e.preventDefault();
                return true;
            }
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
            $.post(baseUrl + 'auth/user/Login', {
                uid: uid,
                password: password,
                geetest_validate:$('input[name="geetest_validate"]').val(),
                geetest_seccode:$('input[name="geetest_seccode"]').val(),
                geetest_challenge:$('input[name="geetest_challenge"]').val()
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
                    captchaObj.reset();
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
        $('.loginCode').css({'padding-bottom':'1rem'});
        captchaObj.appendTo('.loginCode');
    };
    $.ajax({
        url: "/auth/user/GeetestInfo?t=" + (new Date()).getTime(), // 加随机数防止缓存
        type: "get",
        dataType: "json",
        success: function (data) {
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                new_captcha: data.new_captcha,
                product: "embed",
                offline: !data.success,
                width: '100%'
            }, handlerEmbed);
        }
    });

});