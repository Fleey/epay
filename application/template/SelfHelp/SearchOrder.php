<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="/static/css/resource/style.css">
    <link rel="stylesheet" href="/static/css/resource/prism.css">
    <style>
        .item{margin-bottom:2px}.item>.title{width:100px;text-align:right;display:inline-block}.ad{height:160px;width:100%;background-color:#edf1f5}.ad>.tips{top:54px;position:relative}.search{padding-top:11rem;margin-bottom:2rem}#searchValue{margin-top:3rem}#searchBtn,#checkUser{margin-top:1rem}
    </style>
    <title>自助订单查询</title>
</head>
<body>
<div class="container">
    <div class="search">
        <h2 class="text-center">自助订单查询</h2>
        <input class="form-control form-control-lg" type="text" id="searchValue" placeholder="平台订单或商户订单号"/>
        <div id="checkUser"></div>
        <button type="button" class="btn btn-primary btn-lg btn-block" id="searchBtn">点击查询</button>
    </div>
    <div class="ad">
        <h2 class="tips text-center">这里是广告投放区域</h2>
    </div>
</div>
<script src="/static/js/resource/jquery.min.js"></script>
<script src="/static/js/user/gt.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script src="/static/js/selfHelp/SearchOrder.js"></script>
</body>
</html>