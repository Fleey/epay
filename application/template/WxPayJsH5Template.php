<!DOCTYPE html>
<html style="height: 100%;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="https://cdn.staticfile.org/ionic/1.3.2/css/ionic.min.css" rel="stylesheet"/>
    <style>
        .weui-btn {
            position: relative;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding-left: 14px;
            padding-right: 14px;
            box-sizing: border-box;
            font-size: 18px;
            text-align: center;
            text-decoration: none;
            color: #FFFFFF;
            line-height: 2.55555556;
            border-radius: 5px;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            overflow: hidden;
        }

        .weui-btn_primary {
            background-color: #1AAD19;
        }

        html, body {
            overflow-x: scroll;
        }
    </style>
</head>
<body style="height: 100%;">
<div class="bar bar-header bar-light" align-title="center">
    <h1 class="title">微信安全支付</h1>
</div>
<div class="qr-image" id="qrcode"
     style="display: block;position: relative;top: 80px;width: 230px;margin: 0 auto;"></div>
<div class="tips" style="position: relative;top: 90px;width: 230px;margin: 0 auto;">
    <p>订单号码：<?php echo $tradeNo; ?></p>
    <p>支付金额：<?php echo $money; ?> RMB</p>
    <div style="margin-top: 18%;font-weight: 600;font-size: 17px;">
        <p style="text-align: center;">长按保存二维码到相册</p>
        <p style="text-align: center;">微信打开扫一扫</p>
        <p style="text-align: center;">如长按无法保存 请截屏页面 到微信扫一扫即可</p>
    </div>
    <a id="download" style="display: none;"></a>
</div>
<div style="width: 100%;padding: 0 20px;position: relative;top: 16%;">
    <a href="weixin://" style="width: 100%;" class="weui-btn weui-btn_primary">点击打开微信</a>
    <a download="qrcode.jpg" class="weui-btn_primary weui-btn" id="save"
       style="margin-top: 20px;">一键保存二维码</a>
</div>
</body>
<script src="/static/js/qq/qrcode.min.js"></script>
<script src="/static/js/qq/qcloud_util.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script>
    var qrcode = new QRCode('qrcode', {
        text: '<?php echo $codeUrl;?>',
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    $("#save").click(function () {
        var canvas = $('#qrcode').find("canvas").get(0);
        var url = canvas.toDataURL('image/jpeg');
        $("#download").attr('href', url).attr("download", "二维码.png").get(0).click();
        return false;
    });
</script>
</html>