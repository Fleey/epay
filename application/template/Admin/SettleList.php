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
                    <h5 class="card-title">结算列表</h5>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            data-target="#searchFilter">
                        高级搜索
                    </button>
                    <div class="table-responsive">
                        <table id="orderList2" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>商户号</th>
                                <th>结算方式</th>
                                <th>结算账号</th>
                                <th>结算名称</th>
                                <th>金额（单位元）</th>
                                <th>状态</th>
                                <th>申请时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="display: none;" id="userSettleInfo">
            <div class="card">
                <div class="card-body">
                    <div id="chartMap" style="height: 300px;"></div>
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
                        <img style="cursor: pointer;" class="gp" data-name="settleQrCode" src="#" alt="" width="128" height="128">
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
                            <label for="account" class="col-md-3 control-label">结算账号</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="account" placeholder="结算账号">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="username" class="col-md-3 control-label">结算名称</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="username" placeholder="结算名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-3 control-label">结算状态</label>
                            <div class="col-md-8">
                                <select class="form-control" id="status">
                                    <option value="all">所有</option>
                                    <option value="1">已完成</option>
                                    <option value="0">未操作</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="clearMode" class="col-md-3 control-label">结算类型</label>
                            <div class="col-md-8">
                                <select class="form-control" id="clearMode" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">凌晨自动结算</option>
                                    <option value="3">手动提交结算</option>
                                    <option value="2">系统自动结算</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="clearType" class="col-md-3 control-label">结算方式</label>
                            <div class="col-md-8">
                                <select class="form-control" id="clearType" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">银行转账（手动）</option>
                                    <option value="2">微信转账（手动）</option>
                                    <option value="3">支付宝转账（手动）</option>
                                    <option value="4">支付宝转账（自动）</option>
                                    <option value="5">微信转账（二维码）</option>
                                    <option value="6">支付宝转账（二维码）</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="productMinPrice" class="col-md-3 control-label">结算金额</label>
                            <div class="col-md-4">
                                <input type="float" class="form-control" id="minMoney" placeholder="最低价格">
                            </div>
                            <label class="spent">~</label>
                            <div class="col-md-4">
                                <input type="float" class="form-control" id="maxMoney" placeholder="最高价格">
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
<script src="/static/js/admin/settleList.js"></script>