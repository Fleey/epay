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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    站点公告
                </div>
                <div class="card-body">
                    <ol>
                        <li>发现网站进行诈骗，虚假发货，直接冻结！如遇到问题可以联系我们的官方客服QQ：50518105，感谢您的使用。</li>
                        <li>禁止接入黄，赌，诈骗，刷Q币，抽奖以及cdk，王者荣耀点券及CDK，发现直接冻结接口，不给于结算，欢迎举报违规用户，核实真实以后有奖励。</li>
                        <li>提现时间：T+1结算，即今日交易订单将会于明日的下午6点到9点之间进行统一结算。如遇节假日顺延，请留意公告。</li>
                        <li>客服QQ：50518105---有问题请联系我们。</li>
                        <li>官方结算群：965267277</li>
                    </ol>
                    <footer class="blockquote-footer">发布于 <cite>2019-1-31 14:00</cite></footer>
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
            color: ['#de4307', '#8bc24c', '#50C1E9','#005792'],
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