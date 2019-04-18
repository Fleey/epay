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
                    return data ? '<span class="text-success">已付款</span>' : '<span class="text-danger">未付款</span>';
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
                    swal(data['msg'], {
                        buttons: false,
                        timer: 1500,
                        icon: 'warning'
                    });
                    return true;
                }
                window.open(data['url']);
            }
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
                swal('请求失败', '更改状态失败,请重试', 'error');
                return;
            }
            swal('更改状态成功', {
                buttons: false,
                timer: 1500,
                icon: 'success'
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

        var dataTable = $('#orderList').dataTable();
        dataTable.fnDestroy();

        if (!uid && !tradeNo && !tradeNoOut && payType === 'all' && status === 'all' && !productMinPrice && !productMaxPrice && !productStartTime && !productEndTime) {
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
        if (productMinPrice)
            data['args']['productMinPrice'] = productMinPrice;
        if (productMaxPrice)
            data['args']['productMaxPrice'] = productMaxPrice;
        if (productStartTime)
            data['args']['productStartTime'] = productStartTime;
        if (productEndTime)
            data['args']['productEndTime'] = productEndTime;

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
                        value = value ? '已付款' : '未付款';
                    } else if (key === 'isShield') {
                        value = value ? '已屏蔽' : '未屏蔽';
                    }
                    setDataNameInfo(key, value);
                    var status = $('span[data-name="isShield"]').text() !== '未屏蔽' ? 1 : 0;
                    $('button[data-type="setShield"]').text(status ? '恢复订单' : '屏蔽订单');
                });
                //基础信息置入
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
            var callbackList = data['data'];
            var windows = window.open('_blank');
            var nowSite = 0;
            var handler = setInterval(function () {
                swal({
                    title: '请稍后...',
                    text: '正在为您努力回调订单中（' + nowSite + ' / ' + callbackList.length + '）。。。',
                    showConfirmButton: false
                });
                if (nowSite > callbackList.length) {
                    windows.close();
                    swal({
                        title: '',
                        text: '已经为您回调完成所有订单',
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'success'
                    });
                    clearInterval(handler)
                }
                windows.location = callbackList[nowSite++];
            }, 2000);

        }, 'json');
    });
});

function strDateTime(str) {
    var reg = /^(\d+)-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/;
    var r = str.match(reg);
    return r != null;
}
