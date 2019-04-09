<html lang="">
<head>
    <title><?php echo $webName; ?> - 在线测试</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="/static/css/resource/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.staticfile.org/bootswatch/3.3.7/paper/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
        <div class="panel panel-primary">
            <div class="panel-body">
                <form name="alipayment" action="/test/pay" method="post" target="_blank">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-barcode"></span></span>
                        <input size="30" name="WIDout_trade_no"
                               value="<?php echo date("YmdHis") . mt_rand(100, 999); ?>" class="form-control"
                               placeholder="商户订单号"/>
                    </div>
                    <br/>
                    <div class="input-group">
                            <span class="input-group-addon"><span
                                        class="glyphicon glyphicon-shopping-cart"></span></span>
                        <input size="30" name="WIDsubject" value="测试商品" class="form-control" placeholder="商品名称"
                               required="required"/>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-yen"></span></span>
                        <input size="30" name="WIDtotal_fee" value="0.01" class="form-control" placeholder="付款金额"
                               required="required"/>
                    </div>
                    <br/>
                    <div style="text-align: center;">
                        <div class="btn-group btn-group-justified" role="group">
                            <div class="btn-group" role="group">
                                <button type="radio" name="type" value="alipay" class="btn btn-primary">支付宝</button>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="radio" name="type" value="qqpay" class="btn btn-success">QQ</button>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="radio" name="type" value="wxpay" class="btn btn-info">微信</button>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="radio" name="type" value="bankpay" class="btn btn-warning">银联支付</button>
                            </div>
                        </div>
                        <p style="text-align:center"><br>&copy; 技术支持 <a href="/"><?php echo $webName; ?></a>!</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>