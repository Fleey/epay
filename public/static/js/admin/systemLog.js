$(function () {
    function decodeUnicode(str) {
        str = str.replace(/\\/g, "%");
        return unescape(str);
    }

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
                searchTable: 'epay_log'
            }
        },
        destroy: true,
        retrieve: true,
        "bRetrieve": true,
        'columns': [
            {}, {}, {
                'render': function (data) {
                    if (data == 1) {
                        return '登录系统';
                    } else if (data == 2) {
                        return '订单风控';
                    } else if (data === 3) {
                        return '结算记录';
                    }
                    return '未知类型';
                }
            }, {}, {}, {
                'render': function (data) {
                    return decodeUnicode(data);
                }
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#systemLogTable').DataTable(dataTableConfig);


    $('#cancelSearchFilter').bind('click', function () {
        var dataTable = $('#systemLogTable').dataTable();
        dataTable.fnDestroy();
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: {
                searchTable: 'epay_log'
            }
        };
        $('#uid').val('');
        $('#data').val('');
        $('#ipv4').val('');
        $('#infoType').val('all');
        $('#systemLogTable').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        $('#cancelSearchFilter').hide();
    });
    $('#searchContent').click(function () {
        var uid = $('#uid').val();
        var log = $('#data').val();
        var ipv4 = $('#ipv4').val();
        var infoType = $('#infoType').val();

        var dataTable = $('#systemLogTable').dataTable();
        dataTable.fnDestroy();

        if (!uid && !log && !ipv4 && infoType === 'all') {
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_log'
                },
                destroy: true
            };
            $('#systemLogTable').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
            return true;
        }

        var data = {'searchTable': 'epay_log', 'search': {}, 'args': {}};
        if (uid)
            data['args']['uid'] = uid;
        if (log)
            data['args']['data'] = log;
        if (ipv4)
            data['args']['ipv4'] = ipv4;
        if (infoType !== 'all')
            data['args']['type'] = infoType;
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: data
        };
        $('#cancelSearchFilter').show();
        $('#systemLogTable').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
    });
});