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
                searchTable: 'epay_wxx_apply_info'
            }
        },
        destroy: true,
        retrieve: true,
        "bRetrieve": true,
        'columns': [
            {}, {}, {}, {
                'render': function (data) {
                    if (data === 1) {
                        return '集体号'
                    } else if (data === 2) {
                        return '独立号'
                    } else {
                        return '未知'
                    }
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
                'targets': 5
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#orderList1').DataTable(dataTableConfig);

    $('#applyInfo select[data-name="type"]').change(function () {
        var selectValue = $(this).val();
        if (selectValue === '1') {
            $('#applyInfo input[data-name="uid"]').attr("disabled", '');
        } else {
            $('#applyInfo input[data-name="uid"]').removeAttr("disabled");
        }
    });

    $('#cancelSearchFilter').off("click").on('click', function () {
        var dataTable = $('#orderList1').dataTable();
        dataTable.fnDestroy();
        dataTableConfig['ajax'] = {
            url: baseUrl + 'cy2018/api/searchTable',
            type: 'post',
            data: {
                searchTable: 'epay_wxx_apply_info'
            }
        };
        $('#idCardName').val('');
        $('#idCardNumber').val('');
        $('#applyInfoType').val('');
        $('#orderList1').dataTable(dataTableConfig);
        $('#searchFilter').modal('hide');
        $('#cancelSearchFilter').hide();
    });
    $('#searchContent').off("click").on('click', function () {
        var idCardName = $('#idCardName').val();
        var idCardNumber = $('#idCardNumber').val();
        var type = $('#applyInfoType').val();

        var dataTable = $('#orderList1').dataTable();

        if (!idCardName && !idCardNumber && !type) {
            dataTableConfig['ajax'] = {
                url: baseUrl + 'cy2018/api/searchTable',
                type: 'post',
                data: {
                    searchTable: 'epay_wxx_apply_info'
                }
            };
            dataTable.fnDestroy();
            $('#orderList1').dataTable(dataTableConfig);
            $('#searchFilter').modal('hide');
            $('#cancelSearchFilter').hide();
            return true;
        }

        dataTable.fnDestroy();
        var data = {'searchTable': 'epay_wxx_apply_info', 'search': {}, 'args': {}};
        if (idCardName)
            data['args']['idCardName'] = idCardName;
        if (idCardNumber)
            data['args']['idCardNumber'] = idCardNumber;
        if (type)
            data['args']['type'] = type;
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
        var type = $('#applyInfo [data-name="type"]').val();
        var uid = $('#applyInfo [data-name="uid"]').val();
        var idCardName = $('#applyInfo [data-name="idCardName"]').val();
        var idCardNumber = $('#applyInfo [data-name="idCardNumber"]').val();

        var idCardCopy = $('#applyInfo [data-name="idCardCopy"]').attr('data-file-id');
        var idCardNational = $('#applyInfo [data-name="idCardNational"]').attr('data-file-id');

        var idCardValidTime1 = $('#applyInfo [data-name="idCardValidTime1"]').val();
        var idCardValidTime2 = $('#applyInfo [data-name="idCardValidTime2"]').val();

        var accountName = $('#applyInfo [data-name="accountName"]').val();
        var accountNumber = $('#applyInfo [data-name="accountNumber"]').val();
        var accountBank = $('#applyInfo [data-name="accountBank"]').val();

        var bankName = $('#applyInfo [data-name="bankName"]').val();

        var merchantShortName = $('#applyInfo [data-name="merchantShortName"]').val();
        var servicePhone = $('#applyInfo [data-name="servicePhone"]').val();
        var productDesc = $('#applyInfo [data-name="productDesc"]').val();
        var rate = $('#applyInfo [data-name="rate"]').val();
        var contact = $('#applyInfo [data-name="contact"]').val();
        var contactPhone = $('#applyInfo [data-name="contactPhone"]').val();

        if (merchantShortName.length === 0) {
            swal('请求失败', '商户简称必须填写', 'error');
            return true;
        }
        if (servicePhone.length === 0) {
            swal('请求失败', '客服电话必须填写', 'error');
            return true;
        }
        if (productDesc === null) {
            swal('请求失败', '商品服务描述必须选择', 'error');
            return true;
        }
        if (rate === null) {
            swal('请求失败', '费率必须选择', 'error');
            return true;
        }
        if (contact.length === null) {
            swal('请求失败', '联系人姓名必须填写', 'error');
            return true;
        }
        if (contactPhone.length === null) {
            swal('请求失败', '联系人电话必须填写', 'error');
            return true;
        }
        if (accountName.length === 0) {
            swal('请求失败', '开户名称必须填写', 'error');
            return true;
        }
        if (accountNumber.length === 0) {
            swal('请求失败', '开户卡号必须填写', 'error');
            return true;
        }
        if (idCardValidTime1.length === 0) {
            swal('请求失败', '身份证有效期开始时间必须填写', 'error');
            return true;
        }
        if (idCardValidTime2.length === 0) {
            swal('请求失败', '身份证有效期结束时间必须填写', 'error');
            return true;
        }

        var idCardValidTime = JSON.stringify([idCardValidTime1, idCardValidTime2]);
        //身份证有效时间
        var city = $('#applyInfo select[data-area-name="city"]').val();
        var area = $('#applyInfo select[data-area-name="area"]').val();

        if (city === null) {
            swal('请求失败', '开户银行地区必须选择正常', 'error');
            return true;
        }

        var bankAddressCode = city;
        if (area != null)
            bankAddressCode = area;
        //银行地区编码

        if (idCardNumber.length === 0) {
            swal('请求失败', '身份证号码必须填写', 'error');
            return true;
        }
        if (idCardName.length === 0) {
            swal('请求失败', '身份证名称必须填写', 'error');
            return true;
        }
        if (idCardCopy === undefined) {
            swal('请求失败', '身份证正面必须上传', 'error');
            return true;
        }
        if (idCardNational === undefined) {
            swal('请求失败', '身份证反面必须上传', 'error');
            return true;
        }
        if (type === null) {
            swal('请求失败', '账号类型必须选择', 'error');
            return true;
        }
        if (type === '2' && uid.length === 0) {
            swal('请求失败', '商户号必须填写', 'error');
            return true;
        }
        if (accountBank === null) {
            swal('请求失败', '开户银行必须选择', 'error');
            return true;
        }
        if (accountBank === '其他银行' && bankName === null) {
            swal('请求失败', '必须选择开户支行全称', 'error');
            return true;
        }


        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        isRequest = true;
        var requestData = {
            uid: uid,
            type: type,
            idCardCopy: idCardCopy,
            idCardNational: idCardNational,
            idCardName: idCardName,
            idCardNumber: idCardNumber,
            idCardValidTime: idCardValidTime,
            accountName: accountName,
            accountBank: accountBank,
            bankAddressCode: bankAddressCode,
            bankName: bankName,
            accountNumber: accountNumber,
            merchantShortName: merchantShortName,
            servicePhone: servicePhone,
            productDesc: productDesc,
            rate: rate,
            contact: contact,
            contactPhone: contactPhone
        };
        if ($('#applyInfo #saveInfo').attr('data-type') !== 'add') {
            requestData['id'] = $('#applyInfo').attr('data-apply-id');
            requestData['act'] = 'update';
        } else {
            requestData['act'] = 'add';
        }
        var requestUrl = '/cy2018/api/Wxx/ApplyInfo';
        $.post(requestUrl, requestData, function (data) {
            isRequest = false;
            if (data['status'] !== 1) {
                swal('请求失败', data['msg'], 'error');
                return true;
            }
            swal('请求成功', data['msg'], 'success');
            $('#orderList1').dataTable().fnDraw(false);
            $('#applyInfo').modal('hide');
        });
    });
    $('button[data-type="delete"]').off("click").on('click', function () {
        var id = $('#applyInfo').attr('data-apply-id');
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
                $.post('/cy2018/api/Wxx/DeleteApplyInfo', {id: id}, function (data) {
                    if (data['status'] !== 1) {
                        swal('请求失败', data['msg'], 'error');
                        return true;
                    }
                    swal('请求成功', '服务号已经删除', 'success');
                    $('#orderList1').dataTable().fnDraw(false);
                    $('#applyInfo').modal('hide');
                }, 'json');
            });
    });

    $('#applyInfo div[data-area-select] select[data-area-name]').change(function () {
        var changeAreaName = $(this).attr('data-area-name');
        var selectAreaID = $(this).val();
        var nextSelectDom = null;
        if (changeAreaName === 'area')
            return;
        if (changeAreaName === 'province') {
            nextSelectDom = $(this).parent().parent().find('select[data-area-name="city"]');
        } else if (changeAreaName === 'city') {
            nextSelectDom = $(this).parent().parent().find('select[data-area-name="area"]');
        }

        nextSelectDom.removeAttr("disabled").val(0).select2({
            language: 'zh-CN',
            ajax: {
                url: '/cy2018/api/Wxx/SearchArea',
                method: 'post',
                dataType: 'json',
                delay: 250,
                cache: true,
                data: function (params) {
                    return {
                        title: params.term, // search term
                        page: params.page,
                        parentID: selectAreaID
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
        })
    });

    $('#addAccount').off("click").on('click', function () {
        var dom = $('#applyInfo').modal('show');
        dom.find('#saveInfo').attr('data-type', 'add');
        dom.find('button[data-type="delete"]').hide();

        $('#applyInfo input[type!="file"]').val('');
        $('#applyInfo input[type="file"]').removeAttr('data-file-id');
        $('#applyInfo img.imgPreview').removeAttr('src').hide();
        $('#applyInfo span.imgPreview').show();

        $.each($('#applyInfo select'), function (key, value) {
            $(value).find('option[selected]')[0].selected = true;
        });
        $('#applyInfo select[data-area-name="city"]').attr('disabled','');
        $('#applyInfo select[data-area-name="area"]').attr('disabled','');
        initSelect();
        // dom.find('[data-name]').val('');
    });
    $('.imgPreview').off("click").on('click', function () {
        $(this).parent().find('input[data-name]').click();
    });
    $('#applyInfo input[type="file"]').bind('change', function () {
        var fileInfo = $(this)[0].files;
        if (fileInfo === undefined)
            return false;

        var isSuccess = true;
        var dom = $(this);
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
            readFileHash(value, readHashEvent, {'inputDom': dom});
            //为减少图片缓存做准备
        });
    });
    $('#orderList1>tbody').on('click', 'td>div.btn-group [data-type]', function () {
        var clickDom = $(this);
        var clickType = $(this).attr('data-type');
        var id = $(this).parent().parent().parent().find('td:nth-child(1)').text();
        var setDataNameInfo = function (dataName, info) {
            if (info === null)
                info = '暂无记录';
            var dom1 = $('#applyInfo input[data-name="' + dataName + '"]');
            var dom2 = $('#applyInfo select[data-name="' + dataName + '"]');
            if (dom1.length !== 0)
                dom1.val(info).trigger('change');
            if (dom2.length !== 0)
                dom2.val(info).trigger('change');
        };
        if (clickType === 'more') {
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.getJSON(baseUrl + 'cy2018/api/Wxx/ApplyInfo', {
                id: id
            }, function (data) {
                if (data['status'] !== 1) {
                    swal('获取信息失败', data['msg'], 'error');
                    return;
                }
                swal.close();
                data = data['data'];
                //基础信息置入
                $('#applyInfo').modal('show').attr('data-apply-id', id).find('#saveInfo').attr('data-type', 'update');
                $('#applyInfo button[data-type="delete"]').show();
                $.each(data, function (key, value) {
                    if (key === 'idCardCopyFilePath') {
                        var dom = $('#applyInfo input[data-name="idCardCopy"]').parent();
                        dom.find('span.imgPreview').hide();
                        dom.find('img.imgPreview').attr('src', value).css('border', 'none').show();
                    } else if (key === 'idCardNationalFilePath') {
                        var dom = $('#applyInfo input[data-name="idCardNational"]').parent();
                        dom.find('span.imgPreview').hide();
                        dom.find('img.imgPreview').attr('src', value).css('border', 'none').show();
                    } else if (key === 'idCardValidTime') {
                        var temp = JSON.parse(value);
                        setDataNameInfo('idCardValidTime1', temp[0]);
                        setDataNameInfo('idCardValidTime2', temp[1]);
                    } else if (key === 'idCardNational' || key === 'idCardCopy') {
                        if (key === 'idCardNational') {
                            $('#applyInfo input[data-name="idCardNational"]').attr('data-file-id', value);
                        } else if (key === 'idCardCopy') {
                            $('#applyInfo input[data-name="idCardCopy"]').attr('data-file-id', value);
                        }
                    } else if (key === 'bankAddressAreaData') {
                        if (value.length === 0)
                            return;
                        setTimeout(function () {
                            var provinceData = value[value.length - 1];
                            $('#applyInfo select[data-area-name="province"]').append('<option value="' + provinceData['areaID'] + '">' + provinceData['areaName'] + '</option>').val(provinceData['areaID']).trigger('change');
                            var cityData = value[value.length - 2];
                            $('#applyInfo select[data-area-name="city"]').append('<option value="' + cityData['areaID'] + '">' + cityData['areaName'] + '</option>').val(cityData['areaID']).trigger('change');
                            if (value.length === 3) {
                                var areaData = value[value.length - 3];
                                $('#applyInfo select[data-area-name="area"]').append('<option value="' + areaData['areaID'] + '">' + areaData['areaName'] + '</option>').val(areaData['areaID']).trigger('change');
                            }
                        }, 600);
                    } else {
                        if (key === 'uid') {
                            if (value === 0)
                                value = '';
                        } else if (key === 'bankName') {
                            if (value.length !== 0)
                                $('#applyInfo select[data-name="bankName"]').append('<option value="' + value + '">' + value + '</option>');
                            return;
                        }
                        setDataNameInfo(key, value);
                    }
                });
                initSelect();
            });
        }
    });
});

function readHashEvent(hash, args) {
    var fileID = getServerFileID(hash);
    //第一步 判断服务器是否有存档
    if (fileID === 0) {
        fileID = uploadFileCloud(args['fileInfo'], 'productImg');
        //第二步 上传文件
    }
    var inputDom = args['inputDom'].parent();
    inputDom.find('span.imgPreview').hide();
    inputDom.find('img.imgPreview').attr('src', '/static/uploads/' + getFilePath(fileID)).css({'border': 'none'}).show();
    inputDom.find('input[type="file"]').attr('data-file-id', fileID);
}

function initSelect() {
    setTimeout(function () {
        $('#applyInfo select:not([data-name="bankName"]):not([data-area-name])').select2({
            language: 'zh-CN'
        });
        $("#applyInfo select[data-name='bankName']").select2({
            language: 'zh-CN',
            ajax: {
                url: '/cy2018/api/Wxx/SearchBankName',
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
            }
        });
        $('#applyInfo select[data-area-name="province"]').select2({
            language: 'zh-CN',
            ajax: {
                url: '/cy2018/api/Wxx/SearchArea',
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
            }
        });
    }, 300);
}