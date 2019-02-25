<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">仪表盘</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">交易成功订单数量</h5>
                <p class="card-text"><?php echo number_format($totalOrder); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">当前用户余额</h5>
                <p class="card-text">￥<?php echo number_format($balance, 3); ?> RMB</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">今日交易额（未扣手续费）</h5>
                <p class="card-text">￥<?php echo number_format($balance * ($rate / 100), 2) ?> RMB</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">昨日交易额（未扣手续费）</h5>
                <p class="card-text">￥<?php echo number_format($beforeSettleRecord / 100, 2) ?> RMB</p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div id="chartMap" style="height: 300px;margin-top: 35px;"></div>
    </div>
    <div class="col-md-12" style="margin-top: 20px;">
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
    <div class="col-md-6" style="margin-top: 20px;">
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
    <script>
        var option = {
            title: {
                text: '一周内结算金额统计',
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    crossStyle: {
                        color: '#999'
                    }
                }
            },
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
        $(function () {
            $.getScript('/static/js/resource/echarts.min.js', function () {
                var chartMap = echarts.init(document.getElementById('chartMap'));
                $(window).resize(function () {
                    chartMap.resize();
                });
                chartMap.setOption(option);
            });
        });
    </script>