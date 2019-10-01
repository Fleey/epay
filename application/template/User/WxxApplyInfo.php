<div class="page-content container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    开户资料列表
                </div>
                <div class="card-body">
                    <button class="btn w96 mr15 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            id="addAccount">
                        新增用户资料信息
                    </button>
                    <div class="table-responsive">
                        <table class="table no-wrap user-table mb-0 table-hover applyInfo">
                            <thead>
                            <tr>
                                <th scope="col" class="border-0 text-uppercase font-medium">ID</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">身份证名</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">身份证号</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">状态</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">创建时间</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">操作</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <nav id="pagination" aria-label="Page navigation" class="pagination"></nav>
                        <p class="text-center" id="tips" style="">暂时查询不到更多数据</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" role="dialog" id="applyInfo">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">用户资料信息</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idCardName">身份证姓名</label>
                                <input data-name="idCardName" class="form-control" placeholder="身份证姓名">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idCardNumber">身份证号码</label>
                                <input data-name="idCardNumber" class="form-control" placeholder="身份证号码">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="idCardCopy">身份证正面</label>
                            <div class="uploadFile">
                                <input type="file" data-name="idCardCopy" style="display: none;"
                                       accept=".png,.jpg,.gif">
                                <span class="imgPreview" style="">点击上传图片</span>
                                <img class="imgPreview" style="display: none;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="idCardCopy">身份证反面</label>
                            <div class="uploadFile">
                                <input type="file" data-name="idCardNational" style="display: none;"
                                       accept=".png,.jpg,.gif">
                                <span class="imgPreview" style="">点击上传图片</span>
                                <img class="imgPreview" style="display: none;">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="idCardValidTime">身份证有效期限（xxxx-xx-xx | 长期）</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input data-name="idCardValidTime1" type="date" class="form-control"
                                               placeholder="开始时间">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="date" class="form-control" data-name="idCardValidTime2"
                                                   placeholder="身份证有效期">
                                            <div class="input-group-prepend">
                                                <button type="button" id="changeCardValidType" class="btn btn-primary">
                                                    切换长期
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accountName">开户名称</label>
                                <input data-name="accountName" class="form-control" placeholder="开户名称">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accountNumber">开户卡号</label>
                                <input data-name="accountNumber" class="form-control" placeholder="银行账号">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accountBank">开户银行</label>
                                <select data-name="accountBank" class="form-control">
                                    <option selected disabled>请选择开户银行</option>
                                    <option value="工商银行">工商银行</option>
                                    <option value="交通银行">交通银行</option>
                                    <option value="招商银行">招商银行</option>
                                    <option value="民生银行">民生银行</option>
                                    <option value="中信银行">中信银行</option>
                                    <option value="浦发银行">浦发银行</option>
                                    <option value="兴业银行">兴业银行</option>
                                    <option value="光大银行">光大银行</option>
                                    <option value="广发银行">广发银行</option>
                                    <option value="平安银行">平安银行</option>
                                    <option value="北京银行">北京银行</option>
                                    <option value="华夏银行">华夏银行</option>
                                    <option value="农业银行">农业银行</option>
                                    <option value="建设银行">建设银行</option>
                                    <option value="邮政储蓄银行">邮政储蓄银行</option>
                                    <option value="中国银行">中国银行</option>
                                    <option value="宁波银行">宁波银行</option>
                                    <option value="其他银行">其他银行</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bankName">开户银行全称（选填 当为其他银行必填）</label>
                                <select data-name="bankName" class="form-control">
                                    <option selected disabled>请选择开户支行全称</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" data-area-select>
                                <label for="bankAddressCode">开户银行省市</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select data-area-name="province" class="form-control">
                                            <option selected disabled>请选择省</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select data-area-name="city" class="form-control" disabled>
                                            <option selected disabled>请选择市</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select data-area-name="area" class="form-control" disabled>
                                            <option selected disabled>请选择区</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="merchantShortName">商户简称</label>
                                <input data-name="merchantShortName" class="form-control" placeholder="商户简称">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="servicePhone">客服电话</label>
                                <input data-name="servicePhone" class="form-control" placeholder="客服电话">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="productDesc">售卖商品/提供服务描述</label>
                                <select class="form-control" data-name="productDesc">
                                    <option selected disabled>请选择对应 售卖商品/提供服务描述</option>
                                    <option value="其他">其他</option>
                                    <option value="交通出行">交通出行</option>
                                    <option value="休闲娱乐">休闲娱乐</option>
                                    <option value="居民生活服务">居民生活服务</option>
                                    <option value="线下零售">线下零售</option>
                                    <option value="餐饮">餐饮</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact">联系人姓名</label>
                                <input data-name="contact" class="form-control" placeholder="联系人姓名">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contactPhone">手机号码</label>
                                <input data-name="contactPhone" class="form-control" placeholder="手机号码">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="delete">删除账号</button>
                <button type="button" class="btn btn-github" data-type="reloadSettle">发起提现</button>
                <button type="button" class="btn btn-primary" id="saveInfo">保存</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function ($) {
        var isRequest = false;
        $('#changeCardValidType').off('click').on('click', function () {
            var idCardValidTime2 = $('#applyInfo input[data-name="idCardValidTime2"]');
            var type = idCardValidTime2.attr('type');
            if (type === 'date') {
                idCardValidTime2.attr({
                    'type': 'text',
                    'disabled': 'disabled'
                }).val('长期');
            } else {
                idCardValidTime2.attr({
                    'type': 'date'
                }).val('').removeAttr('disabled');
            }
        });
        $('#saveInfo').off("click").on('click', function () {
            if (isRequest)
                return;
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

            var bankName = $('#applyInfo [data-name="bankName"] option:selected').text();

            var merchantShortName = $('#applyInfo [data-name="merchantShortName"]').val();
            var servicePhone = $('#applyInfo [data-name="servicePhone"]').val();
            var productDesc = $('#applyInfo [data-name="productDesc"]').val();
            var contact = $('#applyInfo [data-name="contact"]').val();
            var contactPhone = $('#applyInfo [data-name="contactPhone"]').val();

            var reservedMoney = $('#applyInfo [data-name="reservedMoney"]').val();

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
                contact: contact,
                contactPhone: contactPhone,
                reservedMoney: reservedMoney
            };
            if ($('#applyInfo #saveInfo').attr('data-type') !== 'add') {
                requestData['id'] = $('#applyInfo').attr('data-apply-id');
                requestData['act'] = 'update';
            } else {
                requestData['act'] = 'add';
            }
            var requestUrl = '/user/api/Wxx/ApplyInfo';
            $.post(requestUrl, requestData, function (data) {
                isRequest = false;
                if (data['status'] !== 1) {
                    swal('请求失败', data['msg'], 'error');
                    return true;
                }
                swal('请求成功', data['msg'], 'success');
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
                    $.post('/user/api/Wxx/DeleteApplyInfo', {id: id}, function (data) {
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
                    url: '/user/api/Wxx/SearchArea',
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
            dom.find('button[data-type="reloadSettle"]').hide();

            $('#applyInfo input[type!="file"]').val('');
            $('#applyInfo input[type="file"]').removeAttr('data-file-id');
            $('#applyInfo img.imgPreview').removeAttr('src').hide();
            $('#applyInfo span.imgPreview').show();

            $.each($('#applyInfo select'), function (key, value) {
                $(value).find('option[selected]')[0].selected = true;
            });
            $('#applyInfo select[data-area-name="city"]').attr('disabled', '');
            $('#applyInfo select[data-area-name="area"]').attr('disabled', '');
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


        $('.applyInfo>tbody').on('click', 'td>div.btn-group [data-type]', function () {
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
                $.getJSON(baseUrl + 'user/api/Wxx/ApplyInfo', {
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
                    $('#applyInfo button[data-type="reloadSettle"]').show();
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
                            if (temp[1] === '长期') {
                                $('#applyInfo input[data-name="idCardValidTime2"]').attr({
                                    'type': 'text',
                                    'disabled': 'disabled'
                                }).val('长期');
                            }else{
                                $('#applyInfo input[data-name="idCardValidTime2"]').attr({
                                    'type': 'date'
                                }).val(temp[1]).removeAttr('disabled');
                            }
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
                                    $('#applyInfo select[data-name="bankName"]').append('<option value="' + value + '">' + value + '</option>').val(value).trigger('change');
                                else
                                    $('#applyInfo select[data-name="bankName"]').html('<option selected disabled>请选择开户支行全称</option>').trigger('change');
                                return;
                                // else
                                // $('#applyInfo select[data-name="bankName"]').html
                            }
                            setDataNameInfo(key, value);
                        }
                    });
                    initSelect();
                });
            }
        });


        function drawApplyInfoData(page) {
            if (!page) page = 1;
            $.ajax({
                url: '/user/api/Wxx/WxxApplyList',
                type: 'get',
                dataType: 'json',
                data: {page: page},
                success: function (data) {
                    $('#tips').hide();
                    $('.applyInfo>tbody').html('');
                    if (data['status'] === 0) {
                        swal({
                            title: '',
                            text: data['msg'],
                            showConfirmButton: false,
                            timer: 1500,
                            type: 'error'
                        });
                    } else {
                        if (data['data'].length === 0) {
                            $('#tips').show().html('暂时查询不到更多数据')
                        }
                        $('#pagination').remove();
                        $.each(data['data'], function (key, value) {
                            var trDom = $(document.createElement('tr'));
                            var tdDom1 = $(document.createElement('td')).text(value['id']);
                            var tdDom2 = $(document.createElement('td')).text(value['idCardName']);
                            var tdDom3 = $(document.createElement('td')).text(value['idCardNumber']);
                            var tdDom7 = $(document.createElement('td')).text(value['status'] ? '审核通过' : '审核中');
                            var tdDom4 = $(document.createElement('td')).text(value['createTime']);
                            var caozuo = '<td><div class="btn-group" role="group" aria-label="Button group with nested dropdown"><button type="button" class="btn btn-sm btn-secondary" data-type="more">修改</button></div></td>';
                            trDom.append(tdDom1).append(tdDom2).append(tdDom3).append(tdDom7).append(tdDom4).append(caozuo);
                            $('.applyInfo>tbody').append(trDom)
                        });
                        $('.applyInfo').after('<nav id="pagination" aria-label="Page navigation" class="pagination"></nav>');
                        $('#pagination').html('').Pagination({
                            minPage: 1,
                            maxPage: data['totalPage'],
                            nowPage: page,
                            click_event: clickEvent
                        })
                    }
                }
            })
        }

        function clickEvent(nowPage, ele) {
            var page = parseInt($(ele).text());
            if (ele.is('.disabled') || ele.is('.break') || ele.is('.active')) {
                return
            }
            if (isNaN(page)) {
                if (ele.is('.previous')) {
                    page = nowPage - 1
                }
                if (ele.is('.next')) {
                    page = nowPage + 1
                }
            }
            drawApplyInfoData(page)
        }

        drawApplyInfoData(1);
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
                    url: '/user/api/Wxx/SearchBankName',
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
                    url: '/user/api/Wxx/SearchArea',
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
</script>