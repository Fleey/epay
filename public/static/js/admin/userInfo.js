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

    $('#cancelSearchFilter').off("click").on('click', function () {
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
    $('#searchContent').off("click").on('click', function () {
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
        $('input[data-name="settleHour"]').parent().hide();
        if (selectValue === '2') {
            $('#deposit').show();
            $('#settleMoney').show();
            $('select[data-name="clearType"]').html('<option value="4">支付宝转账（自动）</option>');
        } else {
            $('#deposit').hide();
            $('#settleMoney').hide();
            $('select[data-name="clearType"]').html('' +
                '<option value="1">银行转账（手动）</option>' +
                '<option value="2">微信转账（手动）</option>' +
                '<option value="3">支付宝转账（手动）</option>' +
                '<option value="5">微信转账（二维码）</option>' +
                '<option value="6">支付宝转账（二维码）</option>'
            );
        }
        if (selectValue === '3') {
            $('input[data-name="settleHour"]').parent().show();
        }
    });
    $('select[data-name="productNameShowMode"]').change(function () {
        var selectValue = $(this).val();
        if (selectValue === '1') {
            $('input[data-name="productName"]').parent().show();
        } else {
            $('input[data-name="productName"]').parent().hide();
        }
    });
    $('select[data-name="clearType"]').change(function () {
        var selectValue = parseInt($(this).val());
        if (selectValue === 5 || selectValue === 6) {
            $('.clearModeQr').show();
            $('.clearModeAccount').hide();
            $('span.QrCodeImgPreview').show();
            $('img.QrCodeImgPreview').hide();
        } else {
            $('.clearModeAccount').show();
            $('.clearModeQr').hide();
            $('img.QrCodeImgPreview').removeAttr('src').removeAttr('data-file-id').hide();
        }
    });
    $('button[data-type="save"]').off("click").on('click', function () {
        if (isRequest)
            return;
        var requestData = {};
        var payConfigData = {};
        $.each($('#setPayConfig div[data-name]'), function (key, value) {
            var dom = $(value);
            var tempData = {};
            $.each(dom.find('[data-value]'), function (key1, value1) {
                tempData[$(value1).attr('data-value')] = parseInt($(value1).val());
            });
            payConfigData[dom.attr('data-name')] = tempData;
        });
        requestData['payConfig'] = JSON.stringify(payConfigData);

        var discountsData = {};
        discountsData['isOpen'] = parseInt($('select[data-name="isOrderDiscountsOpen"]').val());
        discountsData['type'] = parseInt($('select[data-name="orderDiscountsType"]').val());
        discountsData['minMoney'] = $('input[data-name="orderDiscountsMinMoney"]').val();
        if (discountsData['minMoney'].length === 0)
            discountsData['minMoney'] = 0;
        else
            discountsData['minMoney'] = parseFloat(discountsData['minMoney']);
        var tempData = [];
        $('.orderDiscountsMoneyList input[data-name="discountsMoney"]').each(function (key, value) {
            var inputValue = $(value).val();
            if (inputValue.length !== 0) {
                tempData.push(parseFloat(inputValue))
            }
        });
        discountsData['moneyList'] = tempData;
        if (discountsData['moneyList'].length === 0 && discountsData['isOpen'] === 1) {
            swal('请求失败', '减免金额不能为空', 'error');
            return true;
        }
        requestData['orderDiscounts'] = JSON.stringify(discountsData);
        var requestStatus = $('#userInfo').attr('data-status');
        var clearMode = parseInt($('select[data-name="clearType"]').val());
        if (clearMode === 5 || clearMode === 6) {
            if (!$('img.QrCodeImgPreview').is('[data-file-id]')) {
                swal('请求失败', '结算二维码不能为空', 'error');
                return true;
            }
        }
        $('#userInfo [data-name]').each(function (key, value) {
            var inputDom = $(value);
            var keyName = inputDom.attr('data-name');
            if (keyName === 'id')
                keyName = 'uid';
            requestData[keyName] = inputDom.val();
        });
        if (clearMode === 5 || clearMode === 6) {
            requestData['qrFileID'] = $('img.QrCodeImgPreview').attr('data-file-id')
        }
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        isRequest = true;
        var requestUrl = requestStatus === 'add' ? '/cy2018/api/AddUser' : '/cy2018/api/UserInfo';
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
    $('button[data-type="delete"]').off("click").on('click', function () {
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
    $('button[data-type="reloadKey"]').off("click").on('click', function () {
        var uid = $('input[data-name="id"]').val();
        swal({
                title: '操作提示',
                text: '确定要重置该账户密匙？',
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
    });
    $('#addUser').off("click").on('click', function () {
        $('input[data-name="id"]').parent().hide();
        $('input[data-name="key"]').parent().hide();
        $('button[data-type="delete"]').hide();
        $('button[data-type="reloadKey"]').hide();
        $('button[data-type="save"]').text('新增用户');
        $('input[data-name]').val('');
        $('#userInfo').modal('show').attr('data-status', 'add');
        $('select[data-name="clearMode"]').val(0).change();
        $('select[data-name="clearType"]').val(1).change();
        $('select[data-name="productNameShowMode"]').val('0').change();
        $('input[data-name="balance"]').val(0);

        $('#setPayConfig [data-name="alipay"] [data-value="apiType"]').val(0);
        $('#setPayConfig [data-name="wxpay"] [data-value="apiType"]').val(0);
        $('#setPayConfig [data-name="qqpay"] [data-value="apiType"]').val(0);
        $('#setPayConfig [data-name="bankpay"] [data-value="apiType"]').val(0);
        $('#setPayConfig [data-name="alipay"] [data-value="isOpen"]').val(1);
        $('#setPayConfig [data-name="wxpay"] [data-value="isOpen"]').val(1);
        $('#setPayConfig [data-name="qqpay"] [data-value="isOpen"]').val(1);
        $('#setPayConfig [data-name="bankpay"] [data-value="isOpen"]').val(1);

        $('#setPayConfig [data-value="payAisle"]').html('<option value="0" selected = "selected">没有更多选项</option>').attr('disabled', 'disabled');
    });
    $('#setPayConfig select[data-value="apiType"]').change(function () {
        var configDom = $(this).parent().parent().parent();
        var configPayName = configDom.attr('data-name');
        var selectApiType = $(this).val();

        if (selectApiType !== '1') {
            configDom.find('select[data-value="payAisle"]').html('<option value="0" selected = "selected">没有更多选项</option>').attr('disabled', 'disabled');
        } else {
            var html = '';
            var payApiList = getCenterPayApiList(configPayName);
            if (payApiList['status'] !== 1 || payApiList['data'].length === 0) {
                configDom.find('select[data-value="payAisle"]').html('<option value="0" selected = "selected">没有更多选项</option>').attr('disabled', 'disabled');
            } else {
                payApiList = payApiList['data'];
                $.each(payApiList, function (key, value) {
                    html += '<option value="' + value['aisle'] + '">' + value['name'] + '</option>';
                });
                configDom.find('select[data-value="payAisle"]').html(html).removeAttr('disabled');
            }

        }
    });
    $('.QrCodeImgPreview').off("click").on('click', function () {
        $('#QrCodeImg').click();
    });
    $('#QrCodeImg').bind('change', function () {
        var fileInfo = $(this)[0].files;
        if (fileInfo === undefined)
            return false;

        var isSuccess = true;
        $.each(fileInfo, function (key, value) {
            if (!isSuccess)
                return true;

            if (value['type'] !== 'image/jpeg' && value['type'] !== 'image/png' && value['type'] !== 'image/gif') {
                swal('该图片类型错误', '只能上传PNG或JPG或GIF类型', 'error');
                isSuccess = false;
                return true
            }
            if (value['size'] > (2 * 1024 * 1024)) {
                swal('文件大小错误', '上传文件不能超过2MB', 'error');
                isSuccess = false;
                return true;
            }
            readFileHash(value, readHashEvent);
            //为减少图片缓存做准备
        });
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
                $('input[data-name="productName"]').parent().hide();
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
                    } else if (key === 'productNameShowMode') {
                        if (value === 1)
                            $('input[data-name="productName"]').parent().show();
                    } else if (key === 'orderDiscounts') {
                        if (value === '') {
                            setDataNameInfo('isOrderDiscountsOpen', 0);
                            setDataNameInfo('orderDiscountsType', 0);
                            setDataNameInfo('orderDiscountsMinMoney', '');
                            $('.orderDiscountsMoneyList').html('');
                            addDiscountsMoneyList('');
                        } else {
                            setDataNameInfo('isOrderDiscountsOpen', value['isOpen']);
                            setDataNameInfo('orderDiscountsType', value['type']);
                            setDataNameInfo('orderDiscountsMinMoney', value['minMoney']);
                            $('.orderDiscountsMoneyList').html('');
                            if (value['moneyList'].length === 0){
                                addDiscountsMoneyList('');
                            }else {
                                $.each(value['moneyList'], function (key1, value1) {
                                    addDiscountsMoneyList(value1);
                                });
                            }
                        }
                    } else if (key === 'payConfig') {
                        if (value === '') {
                            $('#setPayConfig [data-name="alipay"] [data-value="apiType"]').val(0);
                            $('#setPayConfig [data-name="wxpay"] [data-value="apiType"]').val(0);
                            $('#setPayConfig [data-name="qqpay"] [data-value="apiType"]').val(0);
                            $('#setPayConfig [data-name="bankpay"] [data-value="apiType"]').val(0);
                            $('#setPayConfig [data-name="alipay"] [data-value="isOpen"]').val(1);
                            $('#setPayConfig [data-name="wxpay"] [data-value="isOpen"]').val(1);
                            $('#setPayConfig [data-name="qqpay"] [data-value="isOpen"]').val(1);
                            $('#setPayConfig [data-name="bankpay"] [data-value="isOpen"]').val(1);

                            $('#setPayConfig [data-value="payAisle"]').html('<option value="0" selected = "selected">没有更多选项</option>').attr('disabled', 'disabled');
                        } else {
                            $.each(value, function (key1, value1) {
                                $('#setPayConfig [data-name="' + key1 + '"] [data-value="apiType"]').val(value1['apiType']).change();
                                $('#setPayConfig [data-name="' + key1 + '"] [data-value="isOpen"]').val(value1['isOpen']);
                                $('#setPayConfig [data-name="' + key1 + '"] [data-value="payAisle"]').val(value1['payAisle']);
                            });
                        }
                    }
                    setDataNameInfo(key, value);
                });
                //基础信息置入
                var fileID = data['qrFileID'];
                setDataNameInfo('setUserBalance', '');
                $('select[data-name="clearType"]').val(data['clearType']).change();
                $('input[data-name="id"]').parent().show();
                $('input[data-name="key"]').parent().show();
                $('input[data-name="balance"]').parent().show();
                $('button[data-type="delete"]').show();
                $('button[data-type="reloadKey"]').show();
                $('button[data-type="save"]').text('保存');
                $('span.QrCodeImgPreview').hide();
                if (fileID !== undefined) {
                    $('img.QrCodeImgPreview').attr({
                        'data-file-id': fileID === 0 ? 0 : fileID,
                        'src': '/static/uploads/' + getFilePath(fileID)
                    }).css({
                        'border': 'none'
                    }).show();
                }
                $('#userInfo').modal('show').attr('data-status', 'save');

            });
        }
    });

    function addDiscountsMoneyList(value) {
        $('.orderDiscountsMoneyList').append('<div class="row item">\n' +
            '                                        <div class="col-md-8">\n' +
            '                                            <div class="form-group">\n' +
            '                                                <input type="text" class="form-control" value="' + value + '" data-name="discountsMoney" placeholder="不能为零或不能为负数">\n' +
            '                                            </div>\n' +
            '                                        </div>\n' +
            '                                        <div class="col-md-4">\n' +
            '                                            <div class="button-groups">\n' +
            '                                                <button class="btn btn-info" type="button" data-type="appendItem" style="margin-right: 10px;">插入\n' +
            '                                                </button>\n' +
            '                                                <button class="btn btn-danger" type="button" data-type="deleteItem">删除\n' +
            '                                                </button>\n' +
            '                                            </div>\n' +
            '                                        </div>\n' +
            '                                    </div>');
        $('.orderDiscountsMoneyList button[data-type="appendItem"]').off('click').on('click', function () {
            var isOpen = $('select[data-name="isOrderDiscountsOpen"]').val();
            if (isOpen === '0')
                return;
            var orderDiscountsType = $('select[data-name="orderDiscountsType"]').val();
            if (orderDiscountsType === '1')
                addDiscountsMoneyList('')
        });
        $('.orderDiscountsMoneyList button[data-type="deleteItem"]').off('click').on('click', function () {
            if ($('.orderDiscountsMoneyList>.item.row').length === 1)
                return false;
            $(this).parent().parent().parent().remove();
        });
    }

    $('select[data-name="orderDiscountsType"]').change(function () {
        var type = $(this).val();
        if (type === '0') {
            $('.orderDiscountsMoneyList>.item.row').each(function (key, value) {
                if (key !== 0)
                    $(value).remove();
            });
        }
    });

    $('.orderDiscountsMoneyList').html('');
    addDiscountsMoneyList('');
});

function getCenterPayApiList(payType) {
    var returnData = {};
    $.ajax({
        url: '/cy2018/api/CenterPayApiList',
        async: false,
        cache: false,
        data: {
            payType: payType
        },
        dataType: 'json',
        success: function (data) {
            returnData = data;
        }
    });
    return returnData;
}

function readHashEvent(hash, args) {
    var fileID = getServerFileID(hash);
    //第一步 判断服务器是否有存档
    if (fileID === 0) {
        fileID = uploadFileCloud(args['fileInfo'], 'productImg');
        //第二步 上传文件
    }
    $('span.QrCodeImgPreview').hide();
    $('img.QrCodeImgPreview').attr({
        'data-file-id': fileID === 0 ? 0 : fileID,
        'src': '/static/uploads/' + getFilePath(fileID)
    }).css({
        'border': 'none'
    }).show();
}