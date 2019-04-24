<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>易支付</title>
    <style>
        iframe {
            display: block;
            border: none;
            height: 100vh;
            width: 100vw;
        }
    </style>
</head>
<body style="margin: 0;">
<iframe src="<?php echo $url; ?>"></iframe>

<script src="/static/js/qq/qcloud_util.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script>
    function getOrderStatus() {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: '<?php echo url('/Pay/Status', '', false, true); ?>',
            timeout: 10000, //ajax请求超时时间10s
            data: {
                type: <?php echo $payType; ?>,
                tradeNo: '<?php echo $tradeNo;?>'
            },
            success: function (data) {
                //从服务器得到数据，显示数据并继续查询
                if (data['status'] === 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.01, time: 15000});
                    setTimeout(window.location.href = data['url'], 1000);
                } else {
                    setTimeout('getOrderStatus()', 4000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    setTimeout('getOrderStatus()', 1000);
                } else { //异常
                    setTimeout('getOrderStatus()', 4000);
                }
            }
        });
    }

    window.onload = getOrderStatus;
</script>
</body>
</html>