<style>
    #orderInfo .item > span[data-name], #orderInfo .item > img[data-name] {
        display: block;
    }

    #orderInfo .item > span.title {
        font-weight: 600;
    }

    #orderInfo {
        margin-top: 6rem;
    }

    #orderInfo p.header {
        font-weight: 600;
        font-size: 16px;
    }
</style>
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">系统日志</h5>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            data-target="#searchFilter">
                        高级搜索
                    </button>
                    <div class="table-responsive">
                        <table id="systemLogTable" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>UID</th>
                                <th>类型</th>
                                <th>IPv4</th>
                                <th>操作时间</th>
                                <th>日志信息</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="orderInfo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">结算信息</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="header">结算信息</p>
                <div class="row">
                    <div class="col-md-3 item">
                        <span class="title">商户编号</span>
                        <span data-name="uid"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">结算类型</span>
                        <span data-name="clearType"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">结算方式</span>
                        <span data-name="addType"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">结算状态</span>
                        <span data-name="status"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">结算金额</span>
                        <span data-name="money"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">手续费</span>
                        <span data-name="fee"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">结算账号</span>
                        <span data-name="account"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">结算名称</span>
                        <span data-name="username"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">创建时间</span>
                        <span data-name="createTime"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">操作时间</span>
                        <span data-name="updateTime"></span>
                    </div>
                </div>
                <div class="row" id="settleQr" style="display: block;">
                    <div class="col-md-4 item">
                        <span class="title">转账二维码</span>
                        <img style="cursor: pointer;" class="data-img" data-name="settleQrCode" src="#" alt=""
                             width="128" height="128">
                    </div>
                </div>
                <div class="row" id="settleRemark">
                    <div class="col-md-12 item">
                        <span class="title">转账备注</span>
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" class="form-control" data-name="settleRemark"
                                       placeholder="请填写转账转账备注 可为空">
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        快速备注
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="javascript:$('input[data-name=\'settleRemark\']').val('结算转账')">结算转账</a>
                                        <a class="dropdown-item"
                                           href="javascript:$('input[data-name=\'settleRemark\']').val('无')">无</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="deleteRecord">删除申请</button>
                <button type="button" class="btn btn-primary" data-type="confirmPay">确认付款</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="searchFilter" role="dialog" aria-labelledby="searchFilter">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">高级搜索</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group row">
                            <label for="uid" class="col-md-3 control-label">商户ID</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="uid" placeholder="商家ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="infoType" class="col-md-3 control-label">信息类型</label>
                            <div class="col-md-8">
                                <select class="form-control" id="infoType" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">登录系统</option>
                                    <option value="2">订单风控</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ipv4" class="col-md-3 control-label">IPv4</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="ipv4" placeholder="IPv4">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="data" class="col-md-3 control-label">日志信息</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="data" placeholder="支持模糊搜索">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button class="btn btn-default w96" style="display: none;" id="cancelSearchFilter">取消过滤</button>
                <button type="button" class="btn btn-primary w96" id="searchContent">搜索</button>
            </div>
        </div>
    </div>
</div>
<script>
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
                        console.log(data);
                        if (data == 1) {
                            return '登录系统';
                        } else if (data == 2) {
                            return '订单风控';
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
                    searchTable: 'epay_settle'
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

            if (!uid && !log && !ipv4 && infoType !== 'all') {
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
</script>