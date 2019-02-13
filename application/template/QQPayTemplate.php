<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta name="renderer" content="webkit">
    <title>QQ钱包安全支付 - <?php echo $siteName ?></title>
    <link href="/static/css/qq/mqq_pay.css?v=1" rel="stylesheet" media="screen">
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico-wechat"></span><span class="text">QQ钱包支付</span>
    </h1>
    <div class="mod-ct">
        <div class="order">
        </div>
        <div class="amount">￥<?php echo $money; ?></div>
        <div class="qr-image" id="qrcode"></div>

        <div class="detail" id="orderDetail">
            <dl class="detail-ct" style="display: none;">
                <dt>商家</dt>
                <dd id="storeName"><?php echo $siteName ?></dd>
                <dt>购买物品</dt>
                <dd id="productName"><?php echo $productName; ?></dd>
                <dt>商户订单号</dt>
                <dd id="billId"><?php echo $tradeNo; ?></dd>
                <dt>创建时间</dt>
                <dd id="createTime"><?php echo $addTime; ?></dd>
            </dl>
            <a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
        </div>
        <div class="tip">
            <span class="dec dec-left"></span>
            <span class="dec dec-right"></span>
            <div class="ico-scan"></div>
            <div class="tip-text">
                <p>请使用手机QQ扫一扫</p>
                <p>扫描二维码完成支付</p>
            </div>
        </div>
        <div class="tip-text">
        </div>
    </div>
    <div class="foot">
        <div class="inner">
            <p>手机用户可保存上方二维码到手机中</p>
            <p>在手机QQ扫一扫中选择“相册”即可</p>
        </div>
    </div>
</div>
<script src="/static/js/qq/qrcode.min.js"></script>
<script src="/static/js/qq/qcloud_util.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script>
    var isSafari = navigator.userAgent.indexOf("Safari") > -1;
    var code_url = '<?php echo $codeUrl;?>';
    var qrcode = new QRCode('qrcode', {
        text: code_url,
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    var tencentSeries = 'mqqapi://forward/url?src_type=web&style=default&=1&version=1&url_prefix=' + window.btoa(code_url);
    if (isSafari) {
        location.href = tencentSeries;
    } else {
        var iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = tencentSeries;
        document.body.appendChild(iframe);
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
                type: 2,
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

    window.onload = getOrderStatus();
</script>
</body>
</html>