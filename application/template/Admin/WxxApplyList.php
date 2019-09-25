<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">服务商号管理</h5>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            data-target="#searchFilter">
                        高级搜索
                    </button>
                    <button class="btn w96 mr15 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            id="openApplyModal">
                        批量提交申请
                    </button>
                    <div class="table-responsive">
                        <table id="orderList1" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>服务商</th>
                                <th>当日交易金额</th>
                                <th>subMchID</th>
                                <th>用户名称</th>
                                <th>状态</th>
                                <th>备注</th>
                                <th>创建日期</th>
                                <th>操作</th>
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
<div class="modal fade" role="dialog" id="applyInfoResult">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">申请结果</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="accountName">服务商号</label>
                                <input data-name="accountName" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="idCardName">身份证名称</label>
                                <input data-name="idCardName" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subMchID">小微商户ID</label>
                                <input data-name="subMchID" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">申请状态</label>
                                <input data-name="status" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="desc">描述</label>
                                <input data-name="desc" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="createTime">创建时间</label>
                                <input data-name="createTime" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-12 param-tips-div">
                            <div class="alert alert-danger">
                                <h3 class="text-danger">
                                    <i class="fa fa-exclamation-circle"></i>
                                    参数错误提示
                                </h3>
                                <div class="row">
                                    <table class="table table-bordered" id="error-param-tips">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>参数名称</th>
                                            <th>提示</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 sign-tips-div">
                            <div class="alert alert-info">
                                <h3 class="text-info">
                                    <i class="fa fa-exclamation-circle"></i>
                                    温馨提示
                                </h3>
                                <div class="row">
                                    <div class="col-md-8">
                                        请尽快让<h2 data-name="idCardName" style="display: inline"></h2>使用微信扫码签署协议<br>
                                        <a href="" data-name="signUrl" target="_blank"></a>
                                    </div>
                                    <div class="col-md-4">
                                        <iframe id="signQrCode"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 trade-statistics">
                            <div id="chartMap" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="delete">删除</button>
                <button type="button" class="btn btn-facebook" data-type="refresh">刷新结果</button>
                <button type="button" class="btn btn-primary" data-type="replay">重新申请</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" role="dialog" id="applyInfoModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">批量提交申请</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="form-group">
                        <label for="idCardName">身份证名称</label>
                        <select class="form-control" data-name="applyInfoID">
                            <option selected disabled>请选择想要批量申请的账号名称</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="accountID">服务商号</label>
                        <select class="form-control" data-name="accountID" multiple="multiple" disabled></select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addApply">提交申请</button>
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
                            <label for="applyInfoID" class="col-md-3 control-label">身份证名称</label>
                            <div class="col-md-8">
                                <select class="form-control" data-name="applyInfoID">
                                    <option selected disabled>请选择要查询的账号名称</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="desc" class="col-md-3 control-label">subMchID</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="subMchID" placeholder="小微商户ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="desc" class="col-md-3 control-label">描述</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="desc" placeholder="描述">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="desc" class="col-md-3 control-label">申请状态</label>
                            <div class="col-md-8">
                                <select class="form-control" id="type">
                                    <option value="2">已通过</option>
                                    <option value="1">待签约</option>
                                    <option value="0">待审核</option>
                                    <option value="-1">已驳回</option>
                                    <option value="-2">已冻结</option>
                                </select>
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
<script src="/static/js/resource/echarts.min.js"></script>
<script src="/static/js/admin/WxxApplyList.js"></script>