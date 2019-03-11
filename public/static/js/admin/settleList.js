$(function () {
    var dataTableConfig = {
        'language': {url: '/static/zh_CN.txt'},
        'serverSide': true,
        'info': true,
        'autoWidth': false,
        'searching': false,
        'aLengthMenu': [15, 25, 50],
        'deferRender': true,
        'order': [[0, 'desc']],
        'ajax': {
            url: baseUrl + 'cy2018/api/SearchTable',
            type: 'post',
            data: {
                searchTable: 'epay_settle'
            }
        },
        destroy: true,
        retrieve: true,
        "bRetrieve": true,
        'columns': [
            {}, {}, {
                'render': function (data) {
                    if (data === 1) {
                        return '银行转账（手动）';
                    } else if (data === 2) {
                        return '微信转账（手动）';
                    } else if (data === 3) {
                        return '支付宝转账（手动）';
                    } else if (data === 4) {
                        return '支付宝转账（自动）'
                    } else {
                        return '未知';
                    }
                }
            }, {}, {}, {
                'render': function (data) {
                    return data / 100;
                }
            }, {
                'render': function (data) {
                    return data === 1 ? '<span style="color: green;">已完成</span>' : '<span style="color: red;">未完成</span>';
                }
            },{}
        ],
        'columnDefs': [
            {
                'orderable': false,
                'render': function (data, type, row) {
                    var html = '<div class="btn-group" role="group" aria-label="Button group with nested dropdown">';
                    html += '<button type="button" class="btn btn-sm btn-secondary" data-type="more">查看更多</button>';
                    html += '</div>';
                    return html;
                },
                'targets': 8
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#orderList2').DataTable(dataTableConfig);


    $('#cancelSearchFilter').bind('click', function () {
        var dataTable = $('#orderList2').dataTable();
        dataTable.fnDestroy();
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: {
                searchTable: 'epay_settle'
            }
        };
        $('#uid').val('');
        $('#account').val('');
        $('#username').val('');
        $('#minMoney').val('');
        $('#maxMoney').val('');
        $('#status').val('all');
        $('#clearType').val('all');
        $('#orderList2').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        $('#cancelSearchFilter').hide();
    });
    $('#searchContent').click(function () {
        var uid = $('#uid').val();
        var account = $('#account').val();
        var username = $('#username').val();
        var minMoney = $('#minMoney').val();
        var maxMoney = $('#maxMoney').val();
        var clearType = $('#clearType').val();
        var status = $('#status').val();
        var clearMode = $('#clearMode').val();

        var dataTable = $('#orderList2').dataTable();
        dataTable.fnDestroy();

        if (!uid && !account && !username && clearType === 'all' && status === 'all' && clearMode === 'all' && !minMoney && !maxMoney) {
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_settle'
                },
                destroy: true
            };
            $('#orderList2').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
            return true;
        }

        var data = {'searchTable': 'epay_settle', 'search': {}, 'args': {}};
        if (uid)
            data['args']['uid'] = uid;
        if (account)
            data['args']['account'] = account;
        if (username)
            data['args']['username'] = username;
        if (clearType !== 'all')
            data['args']['clearType'] = clearType;
        if (status !== 'all')
            data['args']['status'] = status;
        if (minMoney)
            data['args']['minMoney'] = minMoney;
        if (maxMoney)
            data['args']['maxMoney'] = maxMoney;
        if (clearMode !== 'all')
            data['args']['clearMode'] = clearMode;
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: data
        };
        $('#cancelSearchFilter').show();
        $('#orderList2').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        if (uid) {
            $.getJSON('/cy2018/api/SettleOperate', {type: 'userSettleInfo', uid: uid}, function (data) {
                if (data['status'] === 1) {
                    $.getScript('/static/js/resource/echarts.min.js', function () {
                        var dateList = [];
                        var dataList = [];
                        $.each(data['data'], function (key, value) {
                            dateList.push(value['createTime']);
                            dataList.push(value['money'] / 100);
                        });
                        var option = {
                                title: {
                                    text: '该用户近七天结算金额统计',
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
                                        data: dateList,
                                    }
                                ],
                                yAxis: {
                                    type: 'value'
                                },
                                series: [
                                    {
                                        name: '一周内结算金额统计',
                                        data: dataList,
                                        type: 'line',
                                        areaStyle: {}
                                    }
                                ]
                            };
                        $('#userSettleInfo').show();
                        var chartMap = echarts.init(document.getElementById('chartMap'));
                        $(window).resize(function () {
                            setTimeout(function () {
                                chartMap.resize();
                            }, 200);
                        });
                        chartMap.setOption(option);
                    });
                }
            });
        }
    });
    $('button[data-type="deleteRecord"]').click(function () {
        var id = $(this).attr('data-settle-id');
        swal({
                title: '操作提示',
                text: '您确定要删除这个结算申请吗？',
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确定",
                cancelButtonText: '取消',
                closeOnConfirm: false
            },
            function () {
                swal({
                    title: '请稍后...',
                    text: '正在积极等待服务器响应',
                    showConfirmButton: false
                });
                $.post('/cy2018/api/deleteSettleRecord', {id: id}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'success'
                    });
                    $('#orderList2').dataTable().fnDraw(false);
                    $('#orderInfo').modal('hide');
                }, 'json');
            });
    });
    $('button[data-type="confirmPay"]').click(function () {
        var id = $(this).attr('data-settle-id');
        swal({
                title: '操作提示',
                text: '确定要结算该数据吗？',
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确定",
                cancelButtonText: '取消',
                closeOnConfirm: false
            },
            function () {
                swal({
                    title: '请稍后...',
                    text: '正在积极等待服务器响应',
                    showConfirmButton: false
                });
                $.post('/cy2018/api/confirmSettle', {id: id}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'success'
                    });
                    $('#orderList2').dataTable().fnDraw(false);
                    $('#orderInfo').modal('hide');
                }, 'json');
            });
    });
    $('#orderList2>tbody').on('click', 'td>div.btn-group [data-type]', function () {
        var clickDom = $(this);
        var clickType = $(this).attr('data-type');
        var id = $(this).parent().parent().parent().find('td:nth-child(1)').text();
        if (clickType === 'more') {
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.getJSON(baseUrl + 'cy2018/api/settleInfo', {
                id: id
            }, function (data) {
                if (data['status'] !== 1) {
                    swal('获取信息失败', '请稍后再试', 'error');
                    return;
                }
                swal.close();
                $('.cef-info').hide();
                var setDataNameInfo = function (dataName, info) {
                    if (info === '0' || info === null)
                        info = '暂无记录';
                    $('[data-name="' + dataName + '"]').text(info);
                };
                data = data['data'];
                $.each(data, function (key, value) {
                    if (key === 'money' || key === 'fee') {
                        value = value / 100;
                    } else if (key === 'clearType') {
                        if (value === 1)
                            value = '银行转账（手动）';
                        else if (value === 2)
                            value = '微信转账（手动）';
                        else if (value === 3)
                            value = '支付宝转账（手动）';
                        else if (value === 4)
                            value = '支付宝转账（自动）';
                    } else if (key === 'status') {
                        value = value ? '已操作' : '未操作';
                    } else if (key === 'addType') {
                        if (value === 1)
                            value = '系统凌晨结账';
                        else if (value === 2)
                            value = '支付宝自动结账';
                        else if (value === 3)
                            value = '用户手动提交结算';
                        else
                            value = '未知结算方式';
                    }
                    setDataNameInfo(key, value);
                });
                //基础信息置入
                if (data['status'] == 0) {
                    $('button[data-type="confirmPay"]').show().attr('data-settle-id', id);
                    $('button[data-type="deleteRecord"]').show().attr('data-settle-id', id);
                } else {
                    $('button[data-type="confirmPay"]').hide();
                    $('button[data-type="deleteRecord"]').hide();
                }
                $('#orderInfo').modal('show');
            });
        }
    });
});