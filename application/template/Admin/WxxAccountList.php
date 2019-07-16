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
                            id="addAccount">
                        新增服务商号
                    </button>
                    <div class="table-responsive">
                        <table id="orderList1" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>AppID</th>
                                <th>MchID</th>
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
<div class="modal fade" tabindex="-1" role="dialog" id="accountInfo">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">微信服务商号配置</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="alert alert-info">
                        <h3 class="text-info">
                            <i class="fa fa-exclamation-circle"></i>
                            温馨提示
                        </h3>
                        请记得把Js支付安全目录塞进去公众号授权<code style="margin-left: 10px;"><?php echo url('/Pay/WxPay/','','',true); ?></code>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appID">AppID（服务商号可查看）</label>
                                <input type="text" class="form-control" data-name="appID" placeholder="应用ID appID"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mchID">MchID（服务商号可查看）</label>
                                <input type="text" class="form-control" data-name="mchID" placeholder="mchID"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appKey">AppKey（服务商号需要设置）</label>
                                <input type="text" class="form-control" data-name="appKey" placeholder="appKey"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appSecret">AppSecret（公众号可查看）</label>
                                <input type="text" class="form-control" data-name="appSecret" placeholder="appSecret"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apiCert">证书公钥（apiclient_cert.pem）</label>
                        <textarea type="text" class="form-control" data-name="apiCert"
                                  placeholder="证书公钥内容 请打开文件复制内容置入"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="apiKey">证书密钥（apiclient_key.pem）</label>
                        <textarea type="text" class="form-control" data-name="apiKey"
                                  placeholder="证书密钥内容 请打开文件复制内容置入"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="desc">备注信息</label>
                        <input type="text" class="form-control" data-name="desc" placeholder="备注内容 可进行备注服务号名称等 可空"/>
                    </div>
                    <span class="text-danger">注意：服务商号必须升级证书到v3否则将会导致一系列的错误！！！</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="delete">删除账号</button>
                <button type="button" class="btn btn-primary" id="saveInfo">保存</button>
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
                            <label for="uid" class="col-md-3 control-label">AppID</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="appID" placeholder="应用 appID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="key" class="col-md-3 control-label">MchID</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="mchID" placeholder="应用MchID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account" class="col-md-3 control-label">备注</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="desc" placeholder="备注信息 支持模糊搜索">
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
<script src="/static/js/admin/WxxAccountList.js"></script>