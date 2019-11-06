<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="https://s1.pstatp.com/cdn/expire-1-M/ionic/1.3.2/css/ionic.min.css" rel="stylesheet"/>
</head>
<body>
<div class="bar bar-header bar-light" align-title="center">
    <h1 class="title">订单处理结果</h1>
</div>
<div class="has-header" style="padding: 5px;position: absolute;width: 100%;">
    <div class="text-center" style="color: #a09ee5;">
        <i class="icon ion-information-circled" style="font-size: 80px;"></i><br>
        <span>正在检测付款结果...</span>
        <script src="/static/js/resource/jquery.min.js"></script>
        <script src="https://s1.pstatp.com/cdn/expire-1-M/layer/2.3/layer.js"></script>
        <script>
            $(document).on('touchmove', function (e) {
                e.preventDefault();
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
                        key:'<?php echo md5($tradeNo.'huaji'); ?>'
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
    </div>
</div>
</body>
</html>