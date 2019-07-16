<!DOCTYPE html>
<html style="height: 100%;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.staticfile.org/ionic/1.3.2/css/ionic.min.css" rel="stylesheet"/>
</head>
<body style="height: 100%;">

<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 center-block" style="float: none;">
    <div class="panel panel-primary">
        <div class="panel-heading" style="text-align: center;"><h3 class="panel-title">
                微信支付手机版
        </div>
        <div class="list-group" style="text-align: center;">
            <div class="list-group-item list-group-item-info">长按保存到相册使用扫码扫码完成支付</div>
            <div class="list-group-item">
                <div class="qr-image" id="qrcode"></div>
            </div>
            <div class="list-group-item list-group-item-info">或复制以下链接到微信打开：</div>
            <div class="list-group-item">
                <a href="<?php echo $codeUrl; ?>"><?php echo $codeUrl; ?></a><br/>
                <button id="copy-btn" data-clipboard-text="<?php echo $codeUrl; ?>"
                        class="btn btn-info btn-sm">一键复制
                </button>
            </div>
            <div class="list-group-item">
                <small>提示：你可以将以上链接发到自己微信的聊天框（在微信顶部搜索框可以搜到自己的微信），即可点击进入支付</small>
            </div>
            <div class="list-group-item">
                <a href="#" target="_blank">
                    <small>
                        <marquee style="font-weight: bold;line-height: 20px;font-size: 20px;color: #FF0000;">
                            投诉QQ：<?php echo htmlentities($qq); ?>-或进入网站首页进行投诉，有任何问题请联系我们.点我跳转
                        </marquee>
                    </small>
                </a>
            </div>
            <div class="list-group-item">
                <a href="weixin://" class="btn btn-primary">打开微信</a> <button style="margin-left:5px;" onclick="getOrderStatus()" class="btn btn-primary">检查订单状态</button>
            </div>
        </div>
    </div>
</div>
<script src="//lib.baomitu.com/clipboard.js/1.7.1/clipboard.min.js"></script>
<script src="/static/js/qq/qrcode.min.js"></script>
<script src="/static/js/qq/qcloud_util.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script>
    var codeUrl = '<?php echo $codeUrl; ?>';
    var qrcode = new QRCode('qrcode', {
        text: codeUrl,
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    var clipboard = new Clipboard('#copy-btn');
    clipboard.on('success', function(e) {
        layer.msg('复制成功，请到微信里面粘贴');
    });
    clipboard.on('error', function(e) {
        layer.msg('复制失败，请长按链接后手动复制');
    });
    $("#save").click(function () {
        var canvas = $('#qrcode').find("canvas").get(0);
        var url = canvas.toDataURL('image/jpeg');
        $("#download").attr('href', url).attr('download', '二维码.png').get(0).click();
        return false;
    });

    function getOrderStatus() {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: '<?php echo url('/Pay/Status', '', false, true); ?>',
            timeout: 10000, //ajax请求超时时间10s
            data: {
                type: 1,
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