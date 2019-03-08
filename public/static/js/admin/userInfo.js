$(function () {
    var isRequest = false;
    var dataTableConfig = {
        'language': {url: '/static/zh_CN.txt'},
        'serverSide': true,
        'info': true,
        'autoWidth': false,
        'searching': false,
        'aLengthMenu': [15, 25, 50],
        'deferRender': true,
        "order": [[0, 'desc']],
        'ajax': {
            url: baseUrl + 'cy2018/api/SearchTable',
            type: 'post',
            data: {
                searchTable: 'epay_user'
            }
        },
        destroy: true,
        retrieve: true,
        "bRetrieve": true,
        'columns': [
            {}, {}, {
                'render': function (data) {
                    return data / 1000;
                }
            }, {}, {}, {
                'render': function (data) {
                    return data === 0 ? '未封禁' : '已封禁';
                }
            }
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
                'targets': 6
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#orderList1').DataTable(dataTableConfig);

    $('button[data-type="reloadNotify"]').click(function () {
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


    $('#cancelSearchFilter').bind('click', function () {
        var dataTable = $('#orderList1').dataTable();
        dataTable.fnDestroy();
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: {
                searchTable: 'epay_user'
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
        $('#orderList1').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        $('#cancelSearchFilter').hide();
    });
    $('#searchContent').click(function () {
        var uid = $('#uid').val();
        var key = $('#key').val();
        var account = $('#account').val();
        var username = $('#username').val();
        var email = $('#email').val();

        var dataTable = $('#orderList1').dataTable();

        if (!uid && !key && !account && !username && !email) {
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_user'
                }
            };
            dataTable.fnDestroy();
            $('#orderList1').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
            return true;
        }

        dataTable.fnDestroy();
        var data = {'searchTable': 'epay_user', 'search': {}, 'args': {}};
        if (uid)
            data['args']['uid'] = uid;
        if (key)
            data['args']['key'] = key;
        if (account)
            data['args']['account'] = account;
        if (username)
            data['args']['username'] = username;
        if (email)
            data['args']['email'] = email;
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: data
        };
        $('#cancelSearchFilter').show();
        $('#orderList1').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
    });
    $('select[data-name="clearMode"]').change(function () {
        var selectValue = $(this).val();
        if (selectValue === '2') {
            $('#deposit').show();
            $('#settleMoney').show();
            $('select[data-name="clearType"]').html('<option value="4">支付宝转账（自动）</option>');
        } else {
            $('#deposit').hide();
            $('#settleMoney').hide();
            $('select[data-name="clearType"]').html('<option value="1">银行转账（手动）</option><option value="2">微信转账（手动）</option><option value="3">支付宝转账（手动）</option>');
        }
    });
    $('button[data-type="save"]').click(function () {
        if (isRequest)
            return;
        var requestData = {};
        $('#userInfo [data-name]').each(function (key, value) {
            var inputDom = $(value);
            var keyName = inputDom.attr('data-name');
            if (keyName === 'id')
                keyName = 'uid';
            requestData[keyName] = inputDom.val();
        });
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        isRequest = true;
        var requestUrl = $('#userInfo').attr('data-status') == 'add' ? '/cy2018/api/AddUser' : '/cy2018/api/UserInfo';
        $.post(requestUrl, requestData, function (data) {
            isRequest = false;
            if (data['status'] !== 1) {
                swal('请求失败', data['msg'], 'error');
                return true;
            }
            swal('请求成功', data['msg'], 'success');
            $('#orderList1').dataTable().fnDraw(false);
            $('#userInfo').modal('hide');
        });
    });
    $('button[data-type="delete"]').click(function () {
        var uid = $('input[data-name="id"]').val();
        swal({
                title: '操作提示',
                text: '确定要删除该账号吗？',
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
                $.post('/cy2018/api/DeleteUser', {uid: uid}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    swal('请求成功', '账号已经删除', 'success');
                    $('#orderList1').dataTable().fnDraw(false);
                    $('#userInfo').modal('hide');
                }, 'json');
            });
    });
    $('button[data-type="reloadKey"]').click(function () {
        var uid = $('input[data-name="id"]').val();
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        $.post('/cy2018/api/ReloadKey', {uid: uid}, function (data) {
            if (data['status'] !== 1) {
                swal('请求失败', data['msg'], 'error');
                return true;
            }
            $('input[data-name="key"]').val(data['key']);
            $('#orderList1').dataTable().fnDraw(false);
            swal('请求成功', '新的密匙为：' + data['key'], 'success');
        }, 'json');
    });
    $('#addUser').click(function () {
        $('input[data-name="id"]').parent().hide();
        $('input[data-name="key"]').parent().hide();
        $('input[data-name="balance"]').parent().hide();
        $('button[data-type="delete"]').hide();
        $('button[data-type="reloadKey"]').hide();
        $('button[data-type="save"]').text('新增用户');
        $('input[data-name]').val('');
        $('#userInfo').modal('show').attr('data-status', 'add');
        $('select[data-name="clearMode"]').change();
    });
    $('#orderList1>tbody').on('click', 'td>div.btn-group [data-type]', function () {
        var clickDom = $(this);
        var clickType = $(this).attr('data-type');
        var uid = $(this).parent().parent().parent().find('td:nth-child(1)').text();
        if (clickType === 'more') {
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.getJSON(baseUrl + 'cy2018/api/UserInfo', {
                uid: uid
            }, function (data) {
                if (data['status'] !== 1) {
                    swal('获取信息失败', '请稍后再试', 'error');
                    return;
                }
                swal.close();
                $('#deposit').hide();
                $('#settleMoney').hide();
                var setDataNameInfo = function (dataName, info) {
                    if (info === '0' || info === null)
                        info = '暂无记录';
                    $('[data-name="' + dataName + '"]').val(info);
                };
                data = data['data'];
                $.each(data, function (key, value) {
                    if (key === 'rate') {
                        value = value / 100;
                    } else if (key === 'clearMode') {
                        $('select[data-name="clearMode"]').val(value).change();
                    } else if (key === 'balance') {
                        value = value / 1000;
                    } else if (key === 'deposit' || key === 'payDayMoneyMax' || key === 'payMoneyMax' || key === 'settleMoney') {
                        value = value / 100;
                    }
                    setDataNameInfo(key, value);
                });
                //基础信息置入
                $('input[data-name="id"]').parent().show();
                $('input[data-name="key"]').parent().show();
                $('input[data-name="balance"]').parent().show();
                $('button[data-type="delete"]').show();
                $('button[data-type="reloadKey"]').show();
                $('button[data-type="save"]').text('保存');
                $('#userInfo').modal('show').attr('data-status', 'save');
            });
        }
    });
});