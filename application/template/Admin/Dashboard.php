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
        <div class="col-lg-3 col-md-6">
            <div class="card border-bottom border-info">
                <div class="card-body">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h2><?php echo number_format($totalUser); ?></h2>
                            <h6 class="text-info">商户数量</h6>
                        </div>
                        <div class="ml-auto">
                            <span class="text-info display-6"><i class="ti-user"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-bottom border-cyan">
                <div class="card-body">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h2><?php echo number_format($totalOrder); ?></h2>
                            <h6 class="text-cyan">累计成功订单数</h6>
                        </div>
                        <div class="ml-auto">
                            <span class="text-cyan display-6"><i class="ti-clipboard"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-bottom border-danger">
                <div class="card-body">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h2>￥<?php echo number_format($totalMoney / 100, 2); ?></h2>
                            <h6 class="text-danger">今日交易金额（未扣手续费）</h6>
                        </div>
                        <div class="ml-auto">
                            <span class="text-danger display-6"><i class="ti-wallet"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-bottom border-orange">
                <div class="card-body">
                    <div class="d-flex no-block align-items-center">
                        <div>
                            <h2>￥<?php echo number_format($totalMoneyRate / 100, 3); ?></h2>
                            <h6 class="text-orange">今日结算金额（已手续费）</h6>
                        </div>
                        <div class="ml-auto">
                            <span class="text-orange display-6"><i class="ti-stats-up"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div id="chartMap" style="height: 360px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <div id="chartMap1" style="height: 360px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">服务器环境</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <p><b>PHP版本</b>
                                <br><?php echo phpversion() . ' ' . (ini_get('safe_mode') ? '' : '非') . '线程安全'; ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><b>Mysql版本</b> <br><?php echo \think\Db::query('select VERSION()')[0]['VERSION()']; ?>
                            </p>
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">检测到新版本发布</h5>
                        <div class="row">
                            <div class="col-md-5">
                                <p><b>最新聚合支付程序</b>
                                    <br><?php echo $result['version']; ?></p>
                            </div>
                            <div class="col-md-7">
                                <button class="btn btn-primary float-right" data-update-program>立刻更新</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    var option = {
        title: {
            text: '近七天结算金额统计',
        },
        color: ['#2cabe3'],
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
    var option1 = {
        title: {
            text: '收入类型统计',
        },
        color: ['#f06966', '#6abe83'],
        legend: {},
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
                restore: {show: true},
                saveAsImage: {show: true}
            }
        },
        xAxis: {type: 'category', data: ["微信", "财付通", "支付宝", '银联']},
        yAxis: {},
        series: [
            {
                name: '今天',
                type: 'bar',
                data: ["<?php  echo ($statistics['today'][0]['totalMoney'] / 100) . '","' . ($statistics['today'][1]['totalMoney'] / 100) . '","' . ($statistics['today'][2]['totalMoney'] / 100) . '","' . ($statistics['today'][3]['totalMoney'] / 100); ?>"]
            },
            {
                name: '昨天',
                type: 'bar',
                data: ["<?php  echo ($statistics['yesterday'][0]['totalMoney'] / 100) . '","' . ($statistics['yesterday'][1]['totalMoney'] / 100) . '","' . ($statistics['yesterday'][2]['totalMoney'] / 100) . '","' . ($statistics['yesterday'][3]['totalMoney'] / 100);?>"]
            }
        ]
    };

    $(function () {
        $.getScript('/static/js/resource/echarts.min.js', function () {
            var chartMap = echarts.init(document.getElementById('chartMap'));
            var chartMap1 = echarts.init(document.getElementById('chartMap1'));
            $(window).resize(function () {
                setTimeout(function () {
                    chartMap.resize();
                    chartMap1.resize();
                }, 200)
            });
            chartMap.setOption(option);
            chartMap1.setOption(option1);
        });
        $('button[data-update-program]').off("click").on('click', function () {
            swal({
                title: '请稍后...',
                text: '切勿关闭浏览器,正在为您更新程序',
                showConfirmButton: false
            });
            $.getJSON('/cy2018/api/UpdateProgram', function (data) {
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