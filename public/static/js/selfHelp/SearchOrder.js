$(document).ready(function () {
    var handlerEmbed = function (captchaObj) {

        var isAwait = false;
        var awaitID = 0;
        $('#searchBtn').click(function () {
            var searchValue = $('#searchValue').val();
            if (searchValue.length === 0) {
                layer.msg('查询单号不能不填', {icon: 2});
                return;
            }
            var validate = captchaObj.getValidate();
            if (!validate) {
                layer.msg('必须要验证人机身份，您需要按照提示点击下方验证码', {icon: 2});
                return;
            }
            searchOrder(searchValue);
        });

        function buildHtml(key, value) {
            if (value === null)
                value = '尚未完成';
            if (key === '支付状态') {
                value = value ? '已支付' : '未支付';
            } else if (key === '交易金额') {
                value = '￥' + (value / 100);
            } else if (key === '订单类型') {
                if (value === 1) {
                    value = '微信支付';
                } else if (value === 2) {
                    value = 'QQ钱包';
                } else if (value === 3) {
                    value = '支付宝支付';
                } else if (value === 4) {
                    value = '银联支付';
                } else {
                    value = '未知支付方式';
                }
            }
            return '<div class="item"><span class="title">' + key + '：</span><span>' + value + '</span></div>';
        }

        function searchOrder(tradeNo) {
            if (isAwait) {
                layer.msg('慢慢来别这么着急', {icon: 3});
                return;
            }
            layer.msg('玩命查询中。。。');
            isAwait = true;
            awaitID = layer.load(1, {
                shade: [0.1, '#393E46']
            });
            $.post('./api/OrderInfo', {
                tradeNo: tradeNo,
                geetest_validate: $('input[name="geetest_validate"]').val(),
                geetest_seccode: $('input[name="geetest_seccode"]').val(),
                geetest_challenge: $('input[name="geetest_challenge"]').val()
            }, function (data) {
                isAwait = false;
                layer.close(awaitID);
                captchaObj.reset();
                if (data['status'] !== 1) {
                    layer.msg(data['msg'], {icon: 2});
                    return;
                }
                data = data['data'];
                var html = buildHtml('平台订单号', data['tradeNo']);
                html += buildHtml('商户订单号', data['tradeNoOut']);
                html += buildHtml('支付状态', data['status']);
                html += buildHtml('交易金额', data['money']);
                html += buildHtml('订单类型', data['type']);
                html += buildHtml('创建时间', data['createTime']);
                html += buildHtml('完成时间', data['endTime']);
                html += buildHtml('客服联系方式', data['chatID']);
                html += buildHtml('官网地址', data['webUrl']);
                html = '<div style="padding: 10px 20px;">' + html + '</div>';
                layer.open({
                    type: 1,
                    title: '订单信息',
                    content: html
                });
            }, 'json');
        }

        $('#checkUser').css({'padding-bottom': '1rem'});
        captchaObj.appendTo('#checkUser');
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
                product: 'embed',
                offline: !data.success,
                width: '100%'
            }, handlerEmbed);
        }
    });
});