<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>QQ钱包支付</title>
    <link href="/static/css/qq/mqq_pay.css?v=1" rel="stylesheet" media="screen">
    <link href="/static/css/resource/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.staticfile.org/bootswatch/3.3.7/paper/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 center-block" style="float: none;padding-top: 3rem;">
    <div class="panel panel-default">
        <div class="panel-heading" style="text-align: center;">
            <h3 class="panel-title">
                <span class="ico-wechat"></span>QQ钱包支付手机版
            </h3>
        </div>
        <div class="list-group" style="text-align: center;">
            <div class="list-group-item">
                <h1>￥<?php echo $money; ?><h1>
            </div>
            <div class="list-group-item" style="text-align: left;">
                商品名称：<?php echo htmlentities($productName); ?>
                <br>
                商户订单号：<?php echo $tradeNo; ?>
                <br>
                创建时间：<?php echo $addTime; ?>
            </div>
            <div class="list-group-item"><a href="" id="openUrl" class="btn btn-primary btn-block">跳转到QQ支付</a></div>
            <div class="list-group-item"><a href="#" onclick="getOrderStatus()"
                                            class="btn btn-success btn-block">检测支付状态</a></div>
            <div class="list-group-item">
                <a href="/Pay/QQPay/Submit?tradeNo=<?php echo $tradeNo; ?>&siteName=<?php echo $siteName; ?>"
                   class="btn btn-default btn-sm btn-block">使用扫码支付</a></div>
        </div>
    </div>
</div>
<script src="/static/js/qq/qcloud_util.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script>
    var isSafari = navigator.userAgent.indexOf("Safari") > -1;
    var code_url = '<?php echo $codeUrl?>';
    var tencentSeries = 'mqqapi://forward/url?src_type=web&style=default&=1&version=1&url_prefix=' + window.btoa(code_url);
    if (isSafari) {
        location.href = tencentSeries;
    } else {
        var iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = tencentSeries;
        document.body.appendChild(iframe);
    }
    document.getElementById("openUrl").href = tencentSeries;


    function getOrderStatus() {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: '<?php echo url('/Pay/Status', '', false, true); ?>',
            timeout: 10000, //ajax请求超时时间10s
            data: {
                type: 2,
                tradeNo: '<?php echo $tradeNo;?>'
            },
            success: function (data) {
                //从服务器得到数据，显示数据并继续查询
                if (data['status'] === 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.01, time: 15000});
                    setTimeout(window.location.href = data['url'], 1000);
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

    window.onload = getOrderStatus();
</script>
</body>
</html>