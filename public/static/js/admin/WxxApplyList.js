$(function () {
        var setDataNameInfo = function (dataName, info) {
            if (info === '0' || info === null)
                info = '暂无记录';
            $('#applyInfoResult [data-name="' + dataName + '"]').val(info);
        };
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
                    searchTable: 'epay_wxx_apply_list'
                }
            },
            destroy: true,
            retrieve: true,
            "bRetrieve": true,
            'columns': [
                {}, {}, {
                    'render': function (data) {
                        return data / 100;
                    }
                }, {}, {}, {
                    'render': function (data) {
                        if (data === 1) {
                            return '待签约';
                        } else if (data === -1) {
                            return '已驳回';
                        } else if (data === 0) {
                            return '审核中';
                        } else if (data === -2) {
                            return '已冻结';
                        } else if (data === 2) {
                            return '已通过';
                        } else if (data === -3) {
                            return '封禁中';
                        }
                        return '未知状态';
                    }
                }
            ],
            'columnDefs': [
                {
                    'orderable': false,
                    'render': function (data, type, row) {
                        var html = '<div class="btn-group" role="group" aria-label="Button group with nested dropdown">';
                        if (row[5] === 2) {
                            html += '<button type="button" class="btn btn-sm btn-secondary" data-type="changeStatus" data-status="freeze">冻结账号</button>';
                        } else if (row[5] === -3) {
                            html += '<button type="button" class="btn btn-sm btn-secondary" data-type="changeStatus" data-status="unfreeze">解冻账号</button>';
                        }
                        html += '<button type="button" class="btn btn-sm btn-secondary" data-type="remark">设置备注</button>';
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
        $('#orderList1').DataTable(dataTableConfig);

        $('#cancelSearchFilter').off("click").on('click', function () {
            var dataTable = $('#orderList1').dataTable();
            dataTable.fnDestroy();
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_wxx_apply_list'
                }
            };
            $('#searchFilter select[data-name="applyInfoID"]').val('');
            $('#mchID').val('');
            $('#searchFilter #desc').val('');
            $('#searchFilter #type').val('');
            $('#searchFilter #subMchID').val('');
            $('#orderList1').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
        });
        $('#searchContent').off("click").on('click', function () {
            var applyInfoID = $('#searchFilter select[data-name="applyInfoID"]').val();
            var desc = $('#searchFilter #desc').val();
            var type = $('#searchFilter #type').val();
            var subMchID = $('#searchFilter #subMchID').val();

            var dataTable = $('#orderList1').dataTable();

            if (!applyInfoID && !type && !desc && !subMchID) {
                dataTableConfig['ajax'] = {
                    url: baseUrl + 'cy2018/api/searchTable',
                    type: 'post',
                    data: {
                        searchTable: 'epay_wxx_apply_list'
                    }
                };
                dataTable.fnDestroy();
                $('#orderList1').dataTable(dataTableConfig);
                $('#searchFilter').modal('hide');
                $('#cancelSearchFilter').hide();
                return true;
            }
            dataTable.fnDestroy();
            var data = {'searchTable': 'epay_wxx_apply_list', 'search': {}, 'args': {}};
            if (applyInfoID)
                data['args']['applyInfoID'] = applyInfoID;
            if (type)
                data['args']['type'] = type;
            if (desc)
                data['args']['desc'] = desc;
            if (subMchID)
                data['args']['subMchID'] = subMchID;
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: data
            };
            $('#cancelSearchFilter').show();
            $('#orderList1').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
        });

        $('button[data-target="#searchFilter"]').click(function () {
            setTimeout(function () {
                $('#searchFilter select:not([data-name="applyInfoID"])').val(null).select2({
                    language: 'zh-CN',
                    placeholder: '请选择申请状态'
                });
                $('#searchFilter select[data-name="applyInfoID"]').val(null).select2({
                    language: 'zh-CN',
                    ajax: {
                        url: '/cy2018/api/Wxx/SearchIDCardName',
                        method: 'post',
                        dataType: 'json',
                        delay: 250,
                        cache: true,
                        data: function (params) {
                            return {
                                title: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: params.page < data.totalCount
                                }
                            };
                        }
                    },
                    placeholder: '请选择需要查询的用户信息'
                });
            }, 300);
        });

        $('#addApply').off("click").on('click', function () {
            if (isRequest)
                return;
            var applyInfoID = $('#applyInfoModal [data-name="applyInfoID"]').val();
            var accountID = $('#applyInfoModal [data-name="accountID"]').val();

            if (applyInfoID === null) {
                swal('请求失败', '必须选择提交申请人信息', 'error');
                return true;
            }
            if (accountID.length === 0) {
                swal('请求失败', '必须选择服务商号信息', 'error');
                return true;
            }
            swal({
                title: '请稍后...',
                text: '请耐心等候，可能会比较长时间。',
                showConfirmButton: false
            });
            isRequest = true;
            var requestData = {
                applyInfoID: applyInfoID,
                accountIDs: JSON.stringify(accountID)
            };
            var requestUrl = '/cy2018/api/Wxx/ApplyList';
            $.post(requestUrl, requestData, function (data) {
                isRequest = false;
                if (data['status'] !== 1) {
                    swal('请求失败', data['msg'], 'error');
                    return true;
                }
                swal('请求成功', data['msg'], 'success');
                $('#orderList1').dataTable().fnDraw(false);
                $('#applyInfoModal').modal('hide');
            });
        });
        $('button[data-type="delete"]').off("click").on('click', function () {
            var id = $('#applyInfoResult').attr('data-apply-id');
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
                    $.post('/cy2018/api/Wxx/DeleteApplyList', {id: id}, function (data) {
                        if (data['status'] !== 1) {
                            swal('请求失败', data['msg'], 'error');
                            return true;
                        }
                        swal('请求成功', '服务号已经删除', 'success');
                        $('#orderList1').dataTable().fnDraw(false);
                        $('#applyInfoResult').modal('hide');
                    }, 'json');
                });
        });
        $('#applyInfoModal select[data-name="applyInfoID"]').change(function () {
            $('#applyInfoModal select[data-name="accountID"]').removeAttr('disabled');
        });
        $('#applyInfoResult button[data-type="refresh"]').click(function () {
            var dom = $('#applyInfoResult');

            var applyInfo = dom.attr('data-apply-id');
            $.getJSON(baseUrl + 'cy2018/api/Wxx/ApplyStatus', {
                id: applyInfo
            }, function (data) {
                if (data['status'] !== 1) {
                    swal('获取信息失败', data['msg'], 'error');
                    return;
                }
                $('.sign-tips-div').hide();
                $('.param-tips-div').hide();
                swal.close();
                data = data['data'];
                $.each(data, function (key, value) {
                    if (key === 'status') {
                        if (value === 1) {
                            value = '待签约';
                        } else if (value === -1) {
                            value = '已驳回';
                        } else if (value === 0) {
                            value = '审核中';
                        } else if (value === -2) {
                            value = '已冻结';
                        } else if (value === 2) {
                            value = '已通过';
                        } else if (value === -3) {
                            value = '已封禁';
                        } else {
                            value = '未知状态';
                        }
                    } else if (key === 'applyData') {
                        if (value === null)
                            return;
                        value = JSON.parse(value);
                        if (data['status'] === 1) {
                            $('#applyInfoResult h2[data-name="idCardName"]').text(data['idCardName']);
                            $('#applyInfoResult a[data-name="signUrl"]').attr('href', value['signUrl']).text(value['signUrl']);
                            $('#signQrCode').attr('src', value['signUrl']);
                            $('.sign-tips-div').show();
                        } else if (data['status'] === -1 && value['audit_detail'] !== undefined) {
                            $('.param-tips-div').show();
                            var html = '';
                            $.each(value['audit_detail'], function (id, content) {
                                html += '<tr><th scope="row">' + id + '</th><td>' + content['param_name'] + '</td><td>' + content['reject_reason'] + '</td></tr>';
                            });
                            $('#error-param-tips>tbody').html(html);
                        }
                    }
                    if (key !== 'applyData')
                        setDataNameInfo(key, value);
                });
                //基础信息置入
                if (data['status'] !== -1 && data['status'] !== 0 && data['status'] !== 1 && data['status'] !== -2) {
                    // console.log(data);
                    loadMchIDStatistics(data['subMchID']);
                } else {
                    $('.trade-statistics').hide();
                }
                $('#applyInfoResult').modal('show').attr({
                    'data-apply-id': applyInfo,
                    'data-account-id': data['accountID'],
                    'data-apply-info-id': data['applyInfoID']
                });
            });
        });
        $('#applyInfoResult button[data-type="replay"]').click(function () {
            var dom = $('#applyInfoResult');

            var applyInfo = dom.attr('data-apply-info-id');
            var accountID = dom.attr('data-account-id');

            swal({
                title: '请稍后...',
                text: '请耐心等候，可能会比较长时间。',
                showConfirmButton: false
            });
            isRequest = true;
            var requestData = {
                applyInfoID: applyInfo,
                accountIDs: JSON.stringify([accountID])
            };
            var requestUrl = '/cy2018/api/Wxx/ApplyList';
            $.post(requestUrl, requestData, function (data) {
                isRequest = false;
                if (data['status'] !== 1) {
                    swal('请求失败', data['msg'], 'error');
                    return true;
                }
                swal('请求成功', data['msg'], 'success');
                $('#orderList1').dataTable().fnDraw(false);
                $('#applyInfoResult').modal('hide');
            });
        });
        $('#openApplyModal').on('click', function () {
            $('#applyInfoModal').modal('show');
            setTimeout(function () {
                $.each($('#applyInfoModal select:nth-child(1)'), function (key, value) {
                    $(value).find('option[selected]')[0].selected = true;
                });
                $('#applyInfoModal select[data-name="applyInfoID"]').val(null).select2({
                    language: 'zh-CN',
                    ajax: {
                        url: '/cy2018/api/Wxx/SearchIDCardName',
                        method: 'post',
                        dataType: 'json',
                        delay: 250,
                        cache: true,
                        data: function (params) {
                            return {
                                title: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: data.results,
                                pagination: {
                                    more: params.page < data.totalCount
                                }
                            };
                        },
                    },
                    placeholder: '请选择需要批量的用户信息'
                });
                $('#applyInfoModal select[data-name="accountID"]').attr('disabled', '').val(null).select2({
                    language: 'zh-CN',
                    placeholder: '请选择服务商号信息',
                    ajax: {
                        url: '/cy2018/api/Wxx/SearchAccountID',
                        method: 'post',
                        dataType: 'json',
                        delay: 250,
                        cache: true,
                        data: function (params) {
                            return {
                                title: params.term, // search term
                                page: params.page,
                                applyID: $('#applyInfoModal select[data-name="applyInfoID"]').val()
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: data.results,
                                pagination: {
                                    more: params.page < data.totalCount
                                }
                            };
                        },
                    }
                });
            }, 300);
        });
        $('#orderList1>tbody').on('click', 'td>div.btn-group [data-type]', function () {
            var clickDom = $(this);
            var clickType = $(this).attr('data-type');
            var id = $(this).parent().parent().parent().find('td:nth-child(1)').text();
            if (clickType === 'more') {
                swal({
                    title: '请稍后...',
                    text: '正在积极等待服务器响应',
                    showConfirmButton: false
                });
                $.getJSON(baseUrl + 'cy2018/api/Wxx/ApplyList', {
                    id: id
                }, function (data) {
                    if (data['status'] !== 1) {
                        swal('获取信息失败', data['msg'], 'error');
                        return;
                    }
                    $('.sign-tips-div').hide();
                    $('.param-tips-div').hide();
                    swal.close();
                    data = data['data'];
                    $.each(data, function (key, value) {
                        if (key === 'status') {
                            if (value === 1) {
                                value = '待签约';
                            } else if (value === -1) {
                                value = '已驳回';
                            } else if (value === 0) {
                                value = '审核中';
                            } else if (value === -2) {
                                value = '已冻结';
                            } else if (value === 2) {
                                value = '已通过';
                            } else if (value === -3) {
                                value = '已封禁';
                            } else {
                                value = '未知状态';
                            }
                        } else if (key === 'applyData') {
                            if (value === null)
                                return;
                            value = JSON.parse(value);
                            if (data['status'] === 1) {
                                $('#applyInfoResult h2[data-name="idCardName"]').text(data['idCardName']);
                                $('#applyInfoResult a[data-name="signUrl"]').attr('href', value['signUrl']).text(value['signUrl']);
                                $('#signQrCode').attr('src', value['signUrl']);
                                $('.sign-tips-div').show();
                            } else if (data['status'] === -1 && value['audit_detail'] !== undefined) {
                                $('.param-tips-div').show();
                                var html = '';
                                $.each(value['audit_detail'], function (id, content) {
                                    html += '<tr><th scope="row">' + id + '</th><td>' + content['param_name'] + '</td><td>' + content['reject_reason'] + '</td></tr>';
                                });
                                $('#error-param-tips>tbody').html(html);
                            }
                        }
                        if (key !== 'applyData')
                            setDataNameInfo(key, value);
                    });
                    //基础信息置入
                    if (data['status'] !== -1 && data['status'] !== 0 && data['status'] !== 1 && data['status'] !== -2) {
                        // console.log(data);
                        loadMchIDStatistics(data['subMchID']);
                    } else {
                        $('.trade-statistics').hide();
                    }
                    $('#applyInfoResult').modal('show').attr({
                        'data-apply-id': id,
                        'data-account-id': data['accountID'],
                        'data-apply-info-id': data['applyInfoID']
                    });
                });
            } else if (clickType === 'changeStatus') {
                var status = $(this).attr('data-status');
                if (status === 'freeze')
                    status = -3;
                else
                    status = 2;
                swal({
                    title: '请稍后...',
                    text: '正在积极等待服务器响应',
                    showConfirmButton: false
                });

                $.post('/cy2018/api/Wxx/ApplyListStatus', {id: id, status: status}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    swal('请求成功', data['msg'], 'success');
                    $('#orderList1').dataTable().fnDraw(false);
                }, 'json');
            } else if (clickType === 'remark') {
                swal({
                    title: "设置小微商户号备注",
                    text: "这里可以输入并确认:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "请填写备注"
                }, function(inputValue){
                    if (inputValue === false) return false;
                    $.post('/cy2018/api/Wxx/ApplyListRemark', {id: id, remark: inputValue}, function (data) {
                        if (data['status'] !== 1) {
                            swal('请求失败', data['msg'], 'error');
                            return true;
                        }
                        swal('请求成功', data['msg'], 'success');
                        $('#orderList1').dataTable().fnDraw(false);
                    }, 'json');
                });
            }
        });

        function loadMchIDStatistics(id) {
            $.getJSON('/cy2018/api/Wxx/WxxSubMchTradeStatistics?id=' + id, function (data) {
                if (data['status'] !== 1)
                    return;
                data = data['data']['record'];

                var timeList = [];
                var moneyList = [];
                $.each(data, function (key, value) {
                    timeList.push(value['time']);
                    moneyList.push(value['money'].toString())
                });
                var option = {
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
                            data: timeList,
                        }
                    ],
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: '近七日交易金额',
                            data: moneyList,
                            type: 'line',
                            areaStyle: {}
                        }
                    ]
                };

                var chartMap = echarts.init(document.getElementById('chartMap'));
                $(window).resize(function () {
                    setTimeout(function () {
                        chartMap.resize();
                    }, 200)
                });
                $('.trade-statistics').show();
                chartMap.setOption(option);
                $(window).resize();
            });

        }
    }
);
