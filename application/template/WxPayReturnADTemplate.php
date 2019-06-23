<?php

function getNews(int $count = 0){
    if($count == 0)
        return [];
    $data = curl('http://3g.163.com/touch/jsonp/sy/recommend/0-9.html?hasad=1&miss=48&refresh=B02&offset=0&size=10&callback=syrec4');
    $data = trim($data);
    $data = substr($data,7,strlen($data)-8);
    $data = json_decode($data,true)['news'];
    $returnData = [];
    $i = 0;
    foreach ($data as $value){
        $returnData[] = [
            'href'         => $value['link'],
            'title'        => $value['digest'],
            'img'          => $value['picInfo'][0]['url'],
            'commentCount' => '99+',
            'createTime'   => $value['ptime']
        ];
        $i++;
        if($i == $count)
            break;
    }
    return $returnData;

}

$newData = cache('newsData');
if(empty($newData)){
    $tempData = getNews(5);
    $newData =[];
    $newData[] = $tempData[0];
    $newData[]=         [
        'href'         => 'https://c19515.818tu.com/referral_link/tmp/wr2ysAZmAo',
        'title'        => '那晚，他拼尽全力让我湿的歇斯底里，太爽了',
        'img'          => 'https://cloud.zmz999.com/wl/?id=AhrxCrx2qebvdZM1GX3btjBntQ2QG6fY',
        'commentCount' => '99+',
        'createTime'   => '2019-6-20 16:36'
    ];
    $newData[] =[
        'href'         => 'https://c19515.818tu.com/referral_link/tmp/AojgCKQBPN',
        'title'        => '她被强拉入酒店，一夜七次被宠到全身酸痛...',
        'img'          => 'http://cloud.zmz999.com/wl/?id=g7CAckSb0Ion3poDBjjDeTgIHSaspfQo',
        'commentCount' => '99+',
        'createTime'   => '2019-6-18 15:10'
    ];
    $newData[] = $tempData[1];
    $newData[] = $tempData[2];
    $newData[] = $tempData[3];
    $newData[] = $tempData[4];
    cache('newsData',json_encode($newData),3600);
}else{
    $newData = json_decode($newData,true);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="https://cdn.staticfile.org/ionic/1.3.2/css/ionic.min.css" rel="stylesheet"/>
    <title>正在等待订单校验。。。</title>
    <style>
        .clear{display:block;clear:both}ul.item{list-style:none;padding:0;margin:0}ul.item>li{border-top:1px solid #f2f2f2;position:relative;min-height:60px}ul.item>li>a{padding:10px 15px;display:block}ul.item>li>a:link,ul.item>li>a:visited{color:#46b535;text-decoration:none}ul.item>li>a>.title{box-sizing:border-box;padding-right:10px;width:67%;float:left;font:17px Arial,sans-serif;line-height:21px;height:42px;overflow:hidden;color:#222;text-overflow:inherit;white-space:normal}ul.item>li>a>.img{width:33%;float:right;border:0}ul.item>li>a>.img>img{width:100%;border:0}p.footer{margin:0;padding:0;position:absolute;bottom:10px;font-size:12px}.pr10{padding-right:10px}
    </style>
</head>
<body>
<div class="bar bar-header bar-light" align-title="center">
    <h1 class="title">订单处理结果</h1>
</div>
<div class="text-center" style="color: #a09ee5;color: #a09ee5;margin-top: 44px;padding-top: 8%;padding-bottom: 10%;">
    <i class="icon ion-information-circled" style="font-size: 80px;"></i><br>
    <span>正在检测付款结果...</span>
</div>
<div>
    <?php function printAD(string $href, string $title, string $imgUrl, string $commentCount, string $createTime)
    { ?>
        <li>
            <a href="<?php echo $href; ?>">
                <p class="title"><?php echo $title; ?></p>
                <div class="img">
                    <img src="<?php echo $imgUrl; ?>" alt="">
                </div>
                <p class="footer"><span class="pr10"><?php echo $commentCount; ?>评论</span><?php echo $createTime; ?></p>
                <div class="clear"></div>
            </a>
        </li>
    <?php } ?>
    <ul class="item">
        <?php
        foreach ($newData as $value) {
            printAD($value['href'], $value['title'], $value['img'], $value['commentCount'], $value['createTime']);
        }
        ?>
    </ul>
</div>
<script src="/static/js/resource/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/layer/2.3/layer.js"></script>
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
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : "http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1277741472'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s5.cnzz.com/z_stat.php%3Fid%3D1277741472%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
</body>
</html>