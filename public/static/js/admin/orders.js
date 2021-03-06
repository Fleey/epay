$(function () {
    var dataTableConfig = {
        'language': {url: '/static/zh_CN.txt'},
        'serverSide': true,
        'info': true,
        'autoWidth': false,
        'searching': false,
        'aLengthMenu': [15, 25, 50],
        'deferRender': true,
        "order": [[0, 'desc']],
        "bRetrieve": true,
        'processing': true,
        'ajax': {
            url: baseUrl + 'cy2018/api/SearchTable',
            type: 'post',
            data: {
                searchTable: 'epay_order'
            }
        },
        retrieve: true,
        destroy: true,
        'columns': [
            {}, {}, {}, {
                'render': function (data) {
                    return data / 100;
                }
            }, {
                'render': function (data) {
                    if (data === 1) {
                        return '微信';
                    } else if (data === 2) {
                        return '财付通';
                    } else if (data === 3) {
                        return '支付宝';
                    } else if (data === 4) {
                        return '银联';
                    } else {
                        return '未知';
                    }
                }
            }, {
                'render': function (data) {
                    if (data === 1)
                        return '<span class="text-success">已付款</span>';
                    else if (data === 0)
                        return '<span class="text-danger">未付款</span>';
                    else if (data === 2)
                        return '<span class="text-danger">已冻结</span>';
                    else if (data === 3)
                        return '<span style="color: #3b4abb;">退款中</span>';
                    else if (data === 4)
                        return '<span class="text-warning">已退款</span>';
                    return '<span class="text-danger">未付款</span>';
                }
            }, {}
        ],
        'columnDefs': [
            {
                'orderable': false,
                'render': function (data, type, row) {
                    var html = '<div class="btn-group" role="group" aria-label="Button group with nested dropdown">';
                    html += '<button type="button" class="btn btn-sm btn-secondary" data-type="status">修改订单状态</button>';
                    html += '<button type="button" class="btn btn-sm btn-secondary" data-type="more">查看更多</button>';
                    html += '</div>';
                    return html;
                },
                'targets': 7
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#orderList').DataTable(dataTableConfig);

    $('button[data-type="reloadNotify"]').off("click").on('click', function () {
        var tradeNo = $('span[data-name="tradeNo"]').text();
        $.ajax({
            url: '/cy2018/api/Notified',
            type: 'post',
            async: false,
            data: {
                tradeNo: tradeNo
            },
            success: function (data) {
                if (data['status'] === 0) {
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'warning'
                    });
                    return true;
                }
                window.open(data['url']);
            }
        });
    });

    $('button[data-type="setFrozen"]').off("click").on('click', function () {
        var tradeNo = $('span[data-name="tradeNo"]').text();
        var status = $('span[data-name="status"]').text() === '已付款' ? 1 : 0;
        var buttonDom = $(this);
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        $.post('/cy2018/api/SetFrozen', {
            tradeNo: tradeNo,
            status: status
        }, function (data) {
            if (data['status'] !== 1) {
                swal('请求失败', data['msg'], 'error');
                return;
            }
            swal({
                title: '更改状态成功',
                text: data['msg'],
                showConfirmButton: false,
                timer: 1500,
                type: 'success'
            });
            $('span[data-name="status"]').text(data['status'] ? '已付款' : '冻结中');
            buttonDom.text(status ? '取消冻结' : '冻结订单');
        }, 'json');
    });
    $('button[data-type="setRefund"]').off('click').on('click', function () {
        var tradeNo = $('span[data-name="tradeNo"]').text();
        var type = $('span[data-name="type"]').text();
        swal({
                title: '操作提示',
                text: '确定要退款吗？',
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
                $.post('/cy2018/api/OrderRefund', {tradeNo: tradeNo, type: type}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    $('span[data-name="status"]').text('退款中');
                    $('button[data-type="setRefund"]').hide();
                    $('#orderList').dataTable().fnDraw(false);
                    swal('请求成功', data['msg'], 'success');
                }, 'json');
            });
    });

    $('button[data-type="setShield"]').off("click").on('click', function () {
        var tradeNo = $('span[data-name="tradeNo"]').text();
        var status = $('span[data-name="isShield"]').text() === '未屏蔽' ? 1 : 0;
        var buttonDom = $(this);
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        $.post('/cy2018/api/SetShield', {
            tradeNo: tradeNo,
            status: status
        }, function (data) {
            if (data['status'] !== 1) {
                swal('请求失败', data['msg'], 'error');
                return;
            }
            swal({
                title: '更改状态成功',
                text: data['msg'],
                showConfirmButton: false,
                timer: 1500,
                type: 'success'
            });
            $('span[data-name="isShield"]').text(!status ? '未屏蔽' : '已屏蔽');
            buttonDom.text(status ? '恢复订单' : '屏蔽订单');
        }, 'json');
    });

    $('#cancelSearchFilter').off("click").on('click', function () {
        var dataTable = $('#orderList').dataTable();
        dataTable.fnDestroy();
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: {
                searchTable: 'epay_order'
            }
        };
        $('#uid').val('');
        $('#productName').val('');
        $('#tradeNo').val('');
        $('#tradeNoOut').val('');
        $('#orderStatus').val('all');
        $('#payType').val('all');
        $('#orderIsShield').val('all');
        $('#productMinPrice').val('');
        $('#productMaxPrice').val('');
        $('#productStartTime').val('');
        $('#productEndTime').val('');
        $('#orderList').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        $('#cancelSearchFilter').hide();
    });
    $('#searchContent').off("click").on('click', function () {
        var uid = $('#uid').val();
        var tradeNo = $('#tradeNo').val();
        var tradeNoOut = $('#tradeNoOut').val();
        var payType = $('#payType').val();
        var status = $('#orderStatus').val();
        var productMinPrice = $('#productMinPrice').val();
        var productMaxPrice = $('#productMaxPrice').val();
        var productStartTime = $('#productStartTime').val();
        var productEndTime = $('#productEndTime').val();
        var productName = $('#productName').val();
        var isShield = $('#orderIsShield').val();

        var dataTable = $('#orderList').dataTable();
        dataTable.fnDestroy();

        if (!uid && !tradeNo && !tradeNoOut && !productName && isShield === 'all' && payType === 'all' && status === 'all' && !productMinPrice && !productMaxPrice && !productStartTime && !productEndTime) {
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_order'
                }
            };
            $('#orderList').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
            return true;
        }

        if (productStartTime) {
            if (!strDateTime(productStartTime)) {
                swal({
                    title: '',
                    text: '开始时间格式有误',
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
                return true;
            }
        }
        if (productEndTime) {
            if (!strDateTime(productEndTime)) {
                swal({
                    title: '',
                    text: '结束时间格式有误',
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
                return true;
            }
        }

        var data = {'searchTable': 'epay_order', 'search': {}, 'args': {}};
        if (uid)
            data['args']['uid'] = uid;
        if (tradeNo)
            data['args']['tradeNo'] = tradeNo;
        if (tradeNoOut)
            data['args']['tradeNoOut'] = tradeNoOut;
        if (payType !== 'all')
            data['args']['type'] = payType;
        if (status !== 'all')
            data['args']['status'] = status;
        if (isShield !== 'all')
            data['args']['isShield'] = isShield;
        if (productMinPrice)
            data['args']['productMinPrice'] = productMinPrice;
        if (productMaxPrice)
            data['args']['productMaxPrice'] = productMaxPrice;
        if (productStartTime)
            data['args']['productStartTime'] = productStartTime;
        if (productEndTime)
            data['args']['productEndTime'] = productEndTime;
        if (productName)
            data['args']['productName'] = productName;

        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: data
        };
        $('#cancelSearchFilter').show();
        $('#orderList').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
    });

    $('#orderList>tbody').on('click', 'td>div.btn-group [data-type]', function () {
        var clickDom = $(this);
        var clickType = $(this).attr('data-type');
        var tradeNo = $(this).parent().parent().parent().find('td:nth-child(1)').text();
        if (clickType === 'more') {
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.getJSON(baseUrl + 'cy2018/api/OrderInfo', {
                tradeNo: tradeNo
            }, function (data) {
                if (data['status'] !== 1) {
                    swal('获取信息失败', '请稍后再试', 'error');
                    return;
                }
                swal.close();
                $('.cef-info').hide();
                $('button[data-type="setRefund"]').show();
                var setDataNameInfo = function (dataName, info) {
                    if (info === '0' || info === null)
                        info = '暂无记录';
                    $('[data-name="' + dataName + '"]').text(info);
                };
                data = data['data'];
                $.each(data, function (key, value) {
                    if (key === 'money') {
                        value = value / 100;
                    } else if (key === 'type') {
                        if (value === 1)
                            value = '微信';
                        else if (value === 2)
                            value = '财付通';
                        else if (value === 3)
                            value = '支付宝';
                        else if (value === 4)
                            value = '银联';
                    } else if (key === 'status') {
                        if (value === 4 || value === 3)
                            $('button[data-type="setRefund"]').hide();


                        if (value === 1)
                            value = '已付款';
                        else if (value === 2)
                            value = '冻结中';
                        else if (value === 3)
                            value = '退款中';
                        else if (value === 4)
                            value = '已退款';
                        else
                            value = '未付款';


                    } else if (key === 'isShield') {
                        value = value ? '已屏蔽' : '未屏蔽';
                    }
                    setDataNameInfo(key, value);
                });
                //基础信息置入
                var orderStatus = $('span[data-name="status"]').text();
                if (orderStatus === '冻结中') {
                    $('button[data-type="setFrozen"]').text('取消冻结');
                }
                if (orderStatus === '已付款') {
                    $('button[data-type="setFrozen"]').text('冻结订单');
                }
                var status = $('span[data-name="isShield"]').text() !== '未屏蔽' ? 1 : 0;
                $('button[data-type="setShield"]').text(status ? '恢复订单' : '屏蔽订单');
                $('#orderInfo').modal('show');
            });
        } else if (clickType === 'status') {
            swal({
                    title: "修改订单状态",
                    html: true,
                    text: '<select class="custom-select" id="changeOrderStatus">' +
                        '  <option value="0">未支付</option>' +
                        '  <option value="1">已支付</option>' +
                        '</select>',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.post('/cy2018/api/OrderStatus', {
                            status: $('#changeOrderStatus').val(),
                            tradeNo: tradeNo
                        }, function (data) {
                            if (data['status'] === 1)
                                swal({
                                    title: '',
                                    text: data['msg'],
                                    showConfirmButton: false,
                                    timer: 1500,
                                    type: 'success'
                                });
                            else
                                swal({
                                    title: '',
                                    text: data['msg'],
                                    showConfirmButton: false,
                                    timer: 1500,
                                    type: 'error'
                                });
                        }, 'json');
                    } else {
                        swal.close()
                    }
                });
        }
    });

    $('#batchCallback').off("click").on('click', function () {
        var uid = $('#uid1').val();
        var payType = $('#payTypeCallback').val();
        var startTime = $('#startCallbackTime').val();
        var endTime = $('#endCallbackTime').val();
        if (!strDateTime(startTime)) {
            swal({
                title: '',
                text: '开始时间不能为空',
                showConfirmButton: false,
                timer: 1500,
                type: 'error'
            });
            return true;
        }
        if (!strDateTime(endTime)) {
            swal({
                title: '',
                text: '结束时间不能为空',
                showConfirmButton: false,
                timer: 1500,
                type: 'error'
            });
            return true;
        }
        if (uid.length === 0) {
            swal({
                title: '',
                text: '用户uid不能为空',
                showConfirmButton: false,
                timer: 1500,
                type: 'error'
            });
            return true;
        }
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        $.post('/cy2018/api/BatchCallback', {
            uid: uid,
            payType: payType,
            startTime: startTime,
            endTime: endTime
        }, function (data) {
            if (data['status'] === 0) {
                swal({
                    title: '',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
                return true;
            }
            swal({
                title: '新增批量回调任务成功',
                text: data['msg'],
                showConfirmButton: false,
                timer: 1500,
                type: 'success'
            });

        }, 'json');
    });
});

function strDateTime(str) {
    var reg = /^(\d+)-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/;
    var r = str.match(reg);
    return r != null;
}
