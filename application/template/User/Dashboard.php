<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">仪表盘</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="#Dashboard">仪表盘</a></li>
                    <li class="breadcrumb-item active" aria-current="page">数据中心</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    站点公告(<span style="color: red">新规定</span>)
                </div>
                <div class="card-body">
                    <ol>
                        <li>本平台只支持彩虹站点接入 其他站点不管什么行业一律不支持 发现直接冻结资金 不管业务正规还是不正规 如发现违规行为罚款20倍订单金额或冻结全部资金</li>
                        <li>禁止接入黄 赌 Q币 抽奖以及cdk 王者荣耀点券 防沉迷 辅助等一切违法诈骗商品 平台新增风控系统 如检测订单异常会检查 发现有问题一律冻结资金 严重者我们将会提交网监大队</li>
                        <li>质保超过三个月的业务不能上架 超过质保三个月的业务必须要显示到期时间 就是官方开的那种 任何商品不得不发货 都要发货 如发现违规行为罚款20倍订单金额或冻结全部资金</li>
                        <li>本平台从2018年至今 接口从未断过 只为真诚合作的商户提供最好的接口 商户切来切去的我们不欢迎 如商户跟我们取消合作不使用的 超过三天我们会封禁账号
                            公司决定：取消合作之后超过三天使用回来的 第一次：费率比其他正常商户多百分之1 延续时长一个月 第二次：比其他正常商户上涨百分之2的费率 延续时长两个月
                        </li>
                    </ol>
                    <p style="color: red;">以上条例即日起开始生效</p>
                    <p>以下条例从<cite>2019-6-7 00:00:00</cite> 开始生效</p>
                    <ol>
                        <li>
                            本平台支持其他翼支付平台对接 但是对接我们的翼支付平台必须保证只开放给彩虹的dsw对接 不能开放二级翼支付或其他翼支付对接 如发现扣除二级翼支付全部资金 多次这样明知还开放的会做出处罚
                            还希望旗下合作平台做好监管准备。
                        </li>
                        <li>
                            翼支付不得接入任何违法商品 只能给彩虹ds站点接入 其他业务不管正规不正规我们都不接受。
                        </li>
                    </ol>
                    <p>如有问题联系客服qq：<code>310512312</code></p>
                    <footer class="blockquote-footer">发布于 <cite>2019-6-5 16:00:00</cite></footer>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-8">
            <div class="card">
                <div class="p-3">
                    <div id="chartMap" style="height: 300px;"></div>
                </div>
                <div class="row no-gutters border-top">
                    <div class="col-md-6 border-right border-bottom">
                        <div class="d-flex align-items-center px-4 py-3">
                            <h2 class="mb-0 text-info display-7">
                                <i class="ti-image"></i>
                            </h2>
                            <div class="ml-4">
                                <h2 class="font-normal"><?php echo number_format($totalOrder); ?></h2>
                                <h4>交易成功订单数量</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 border-bottom">
                        <div class="d-flex align-items-center px-4 py-3">
                            <h2 class="mb-0 text-info display-7">
                                <i class="ti-stats-up"></i>
                            </h2>
                            <div class="ml-4">
                                <h2 class="font-normal">
                                    ￥<?php echo number_format($yesterdayOrderTotalMoney / 100, 2) ?></h2>
                                <h4>昨日交易额（未扣手续费）</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 border-right">
                        <div class="d-flex align-items-center px-4 py-3">
                            <h2 class="mb-0 text-info display-7">
                                <i class="ti-credit-card"></i>
                            </h2>
                            <div class="ml-4">
                                <h2 class="font-normal">￥<?php echo number_format($balance, 3); ?></h2>
                                <h4>当前用户余额</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 border-sm-top">
                        <div class="d-flex align-items-center px-4 py-3">
                            <h2 class="mb-0 text-info display-7">
                                <i class="ti-credit-card"></i>
                            </h2>
                            <div class="ml-4">
                                <h2 class="font-normal">
                                    ￥<?php echo number_format($todayOrderTotalMoney / 100, 2); ?></h2>
                                <h4>今日交易额（未扣手续费）</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">昨天支付类型统计</h5>
                    <div class="mt-3">
                        <div id="chartMap2" style="height: 360px;"></div>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                        <div>
                            <h3 class="font-medium text-uppercase">交易金额统计</h3>
                            <h5 class="text-muted">仅统计昨天交易类型金额（未扣费率）</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">账号信息</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="uid">商户ID</label>
                        <input type="text" class="form-control" id="uid" value="<?php echo $uid; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="password">商户密钥</label>
                        <input type="text" class="form-control" id="password" value="<?php echo $key; ?>" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <script>
        var option1 = {
            title: {
                text: '一周内结算金额统计',
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    crossStyle: {
                        color: '#2cabe3'
                    }
                }
            },
            color: ['#2cabe3'],
            toolbox: {
                feature: {
                    dataView: {show: true, readOnly: false},
                    magicType: {show: true, type: ['bar', 'bar']},
                    restore: {show: true},
                    saveAsImage: {show: true}
                }
            },
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    data: [<?php foreach ($settleRecord as $value) {
                        echo '"' . date('Y-m-d', strtotime($value['createTime'])) . '",';
                    } ?>],
                }
            ],
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '一周内结算金额统计',
                    data: [<?php foreach ($settleRecord as $value) {
                        echo '"' . ($value['money'] / 100) . '",';
                    } ?>],
                    type: 'line',
                    areaStyle: {}
                }
            ]
        };
        var option2 = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data: ['财付通', '微信', '支付宝', '银联']
            },
            color: ['#de4307', '#8bc24c', '#50C1E9', '#005792'],
            series: [
                {
                    name: '支付来源',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '30',
                                fontWeight: 'bold'
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data: [
                        {value: <?php echo $yesterdayOrderTypeCount['qq'] / 100;?>, name: '财付通'},
                        {value: <?php echo $yesterdayOrderTypeCount['wx'] / 100;?>, name: '微信'},
                        {value: <?php echo $yesterdayOrderTypeCount['ali'] / 100;?>, name: '支付宝'},
                        {value:<?php echo $yesterdayOrderTypeCount['bank'] / 100;?>, name: '银联'}
                    ]
                }
            ]
        };

        $(function () {
            $.getScript('/static/js/resource/echarts.min.js', function () {
                var chartMap = echarts.init(document.getElementById('chartMap'));
                chartMap.setOption(option1);
                var chartMap2 = echarts.init(document.getElementById('chartMap2'));
                chartMap2.setOption(option2);
                $(window).resize(function () {
                    setTimeout(function () {
                        chartMap.resize();
                        chartMap2.resize();
                    }, 200)
                });
            });
        });
    </script>