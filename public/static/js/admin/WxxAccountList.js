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
                searchTable: 'epay_wxx_account_list'
            }
        },
        destroy: true,
        retrieve: true,
        "bRetrieve": true,
        'columns': [
            {}, {}, {}, {}
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
                'targets': 5
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#orderList1').DataTable(dataTableConfig);

    $('#cancelSearchFilter').off("click").on('click', function () {
        var dataTable = $('#orderList1').dataTable();
        dataTable.fnDestroy();
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: {
                searchTable: 'epay_wxx_account_list'
            }
        };
        $('#appID').val('');
        $('#mchID').val('');
        $('#desc').val('');
        $('#orderList1').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        $('#cancelSearchFilter').hide();
    });
    $('#searchContent').off("click").on('click', function () {
        var appID = $('#appID').val();
        var mchID = $('#mchID').val();
        var desc = $('#desc').val();

        var dataTable = $('#orderList1').dataTable();

        if (!appID && !mchID && !desc) {
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_wxx_account_list'
                }
            };
            dataTable.fnDestroy();
            $('#orderList1').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
            return true;
        }

        dataTable.fnDestroy();
        var data = {'searchTable': 'epay_wxx_account_list', 'search': {}, 'args': {}};
        if (appID)
            data['args']['appID'] = appID;
        if (mchID)
            data['args']['mchID'] = mchID;
        if (desc)
            data['args']['desc'] = desc;
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: data
        };
        $('#cancelSearchFilter').show();
        $('#orderList1').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
    });

    $('#saveInfo').off("click").on('click', function () {
        if (isRequest)
            return;
        var appID = $('#accountInfo [data-name="appID"]').val();
        var mchID = $('#accountInfo [data-name="mchID"]').val();
        var appKey = $('#accountInfo [data-name="appKey"]').val();
        var appSecret = $('#accountInfo [data-name="appSecret"]').val();
        var desc = $('#accountInfo [data-name="desc"]').val();

        var apiCert = $('#accountInfo [data-name="apiCert"]').val();
        var apiKey = $('#accountInfo [data-name="apiKey"]').val();

        if (appID.length === 0) {
            swal('请求失败', 'appID不能为空', 'error');
            return true;
        }
        if (mchID.length === 0) {
            swal('请求失败', 'mchID不能为空', 'error');
            return true;
        }
        if (appKey.length === 0) {
            swal('请求失败', 'appKey不能为空', 'error');
            return true;
        }
        if (appSecret.length === 0) {
            swal('请求失败', 'appSecret不能为空', 'error');
            return true;
        }
        if (apiCert.length === 0) {
            swal('请求失败', '公钥证书不能为空', 'error');
            return true;
        }
        if (apiKey.length === 0) {
            swal('请求失败', '私钥证书不能为空', 'error');
            return true;
        }
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        isRequest = true;
        var requestData = {
            apiKey: apiKey,
            apiCert: apiCert,
            appID: appID,
            mchID: mchID,
            appKey: appKey,
            appSecret: appSecret,
            desc: desc
        };
        if ($('#accountInfo #saveInfo').attr('data-type') !== 'add') {
            requestData['id'] = $('#accountInfo').attr('data-account-id');
            requestData['act'] = 'update';
        } else {
            requestData['act'] = 'add';
        }
        var requestUrl = '/cy2018/api/Wxx/Account';
        $.post(requestUrl, requestData, function (data) {
            isRequest = false;
            if (data['status'] !== 1) {
                swal('请求失败', data['msg'], 'error');
                return true;
            }
            swal('请求成功', data['msg'], 'success');
            $('#orderList1').dataTable().fnDraw(false);
            $('#accountInfo').modal('hide');
        });
    });
    $('button[data-type="delete"]').off("click").on('click', function () {
        var id = $('#accountInfo').attr('data-account-id');
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
                $.post('/cy2018/api/Wxx/DeleteAccount', {id: id}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    swal('请求成功', '服务号已经删除', 'success');
                    $('#orderList1').dataTable().fnDraw(false);
                    $('#accountInfo').modal('hide');
                }, 'json');
            });
    });

    $('#addAccount').off("click").on('click', function () {
        var dom = $('#accountInfo').modal('show');
        dom.find('#saveInfo').attr('data-type', 'add');
        dom.find('button[data-type="delete"]').hide();
        dom.find('[data-name]').val('');
    });
    $('#orderList1>tbody').on('click', 'td>div.btn-group [data-type]', function () {
        var clickDom = $(this);
        var clickType = $(this).attr('data-type');
        var id = $(this).parent().parent().parent().find('td:nth-child(1)').text();
        var setDataNameInfo = function (dataName, info) {
            if (info === '0' || info === null)
                info = '暂无记录';
            $('#accountInfo [data-name="' + dataName + '"]').val(info);
        };
        if (clickType === 'more') {
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.getJSON(baseUrl + 'cy2018/api/Wxx/Account', {
                id: id
            }, function (data) {
                if (data['status'] !== 1) {
                    swal('获取信息失败', data['msg'], 'error');
                    return;
                }
                swal.close();
                data = data['data'];
                $.each(data, function (key, value) {
                    setDataNameInfo(key, value);
                });
                //基础信息置入
                $('#accountInfo').modal('show').attr('data-account-id', id).find('#saveInfo').attr('data-type', 'update');
            });
        }
    });
});
