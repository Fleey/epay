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
                <h5 class="card-title">商户数量</h5>
                <p class="card-text"><?php echo number_format($totalUser); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">今日交易金额（未扣手续费）</h5>
                <p class="card-text">￥<?php echo number_format($totalMoney / 100, 3); ?> RMB</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">今日结算金额（已手续费）</h5>
                <p class="card-text">￥<?php echo number_format($totalMoneyRate / 100, 3); ?> RMB</p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div id="chartMap" style="height: 300px;margin-top: 35px;"></div>
    </div>
    <div class="col-md-6" style="margin-top: 2rem;">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">订单收入统计</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">时间</th>
                        <th scope="col">微信支付</th>
                        <th scope="col">QQ钱包</th>
                        <th scope="col">支付宝</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>今天</td>
                        <?php
                        foreach ($statistics['today'] as $value) {
                            echo '<td>￥' . ($value['totalMoney'] / 100) . ' RMB</td>';
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>昨天</td>
                        <?php
                        foreach ($statistics['yesterday'] as $value) {
                            echo '<td>￥' . ($value['totalMoney'] / 100) . ' RMB</td>';
                        }
                        ?>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="margin-top: 2rem;">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">服务器环境</h5>
                <div class="row">
                    <div class="col-md-3">
                        <p><b>PHP版本</b>
                            <br><?php echo phpversion() . ' ' . (ini_get('safe_mode') ? '' : '非') . '线程安全'; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><b>Mysql版本</b> <br><?php echo db()->query('select VERSION()')[0]['VERSION()']; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><b>服务器软件</b> <br><?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><b>程序最大运行时间</b> <br><?php echo ini_get('max_execution_time'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><b>文件上传许可</b> <br><?php echo ini_get('upload_max_filesize'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><b>POST许可</b> <br><?php echo ini_get('post_max_size'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><b>聚合支付程序版本</b> <br><?php echo config('app_version'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $result       = curl('http://update.moxi666.cn/version.json');
    $isNeedUpdate = false;
    if ($result != false) {
        $result = json_decode($result, true);
        if (!empty($result['version'])) {
            if (config('app_version') != $result['version'])
                $isNeedUpdate = true;
        }
    }
    if ($isNeedUpdate) {
        ?>
        <div class="col-md-6" style="margin-top: 2rem;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">检测到新版本发布</h5>
                    <div class="row">
                        <div class="col-md-5">
                            <p><b>最新聚合支付程序</b>
                                <br><?php echo $result['version']; ?></p>
                        </div>
                        <div class="col-md-7">
                            <button class="btn btn-primary float-right btn-sm" data-update-program>立刻更新</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    var option = {
        title: {
            text: '近七天结算金额统计',
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
        $('button[data-update-program]').click(function () {
            swal({
                title: '请稍后...',
                text: '切勿关闭浏览器,正在为您更新程序',
                showConfirmButton: false
            });
            $.getJSON('/admin/api/UpdateProgram', function (data) {
                if (data['status'] === 0) {
                    swal('更新系统失败', data['msg'], 'error');
                    return;
                }
                swal({
                    title: '更新系统成功',
                    text: data['msg'],
                    showConfirmButton: false,
                    type: 'success'
                });
                setTimeout(function () {
                    window.location.reload()
                }, 2000);
            }, 'json');
        });
    });
</script>