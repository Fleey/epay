<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="https://cdn.staticfile.org/ionic/1.3.2/css/ionic.min.css" rel="stylesheet"/>
</head>
<body>
<div class="bar bar-header bar-light" align-title="center">
    <h1 class="title">微信安全支付</h1>
</div>
<div class="has-header" style="padding: 5px;position: absolute;width: 100%;">
    <div class="text-center" style="color: #a09ee5;">
        <i class="icon ion-information-circled" style="font-size: 80px;"></i><br>
        <span>正在跳转...</span>
        <script src="/static/js/qq/qcloud_util.js"></script>
        <script src="/static/js/layer/layer.js"></script>
        <script>
            $(document).on('touchmove', function (e) {
                e.preventDefault();
            });

            //调用微信JS api 支付
            function jsApiCall() {
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    <?php echo $jsApiParam; ?>,
                    function (res) {
                        if (res['err_msg'] === 'get_brand_wcpay_request:ok') {
                            getOrderStatus();
                        } else if (res['err_msg'] === 'get_brand_wcpay_request:fail') {
                            // layer.msg('取消支付，正在转跳回商户页面...', {icon: 16, shade: 0.01, time: 15000});
                            //setTimeout(window.location.href = '<?php //echo $cancelCallback; ?>//', 1000);
                            jsApiCall();
                        } else {
                            WeixinJSBridge.log('您似乎遇到了错误,请截图本页面联系管理员 ' + res.err_msg);
                        }
                    }
                );
            }

            function callPay() {
                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                } else {
                    jsApiCall();
                }
            }

            // 订单详情
            $('#orderDetail .arrow').click(function (event) {
                if ($('#orderDetail').hasClass('detail-open')) {
                    $('#orderDetail .detail-ct').slideUp(500, function () {
                        $('#orderDetail').removeClass('detail-open');
                    });
                } else {
                    $('#orderDetail .detail-ct').slideDown(500, function () {
                        $('#orderDetail').addClass('detail-open');
                    });
                }
            });

            // 检查是否支付完成
            function getOrderStatus() {
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '<?php echo url('/Pay/Status', '', false, true); ?>',
                    timeout: 10000, //ajax请求超时时间10s
                    data: {
                        type: 1,
                        tradeNo: '<?php echo $tradeNo;?>',
                        key: '<?php echo md5($tradeNo . 'huaji'); ?>'
                    },
                    success: function (data) {
                        //从服务器得到数据，显示数据并继续查询
                        if (data['status'] === 1) {
                            layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.01, time: 15000});
                            setTimeout(function () {
                                window.location.href = '/Pay/WxPay/WapResult';
                            }, 1000);
                        } else {
                            setTimeout('getOrderStatus()', 2000);
                        }
                    },
                    //Ajax请求超时，继续查询
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        if (textStatus === 'timeout') {
                            setTimeout('getOrderStatus()', 1000);
                        } else { //异常
                            setTimeout('getOrderStatus()', 2000);
                        }
                    }
                });
            }

            window.onload = callPay();
        </script>
    </div>
</div>
</body>
</html>