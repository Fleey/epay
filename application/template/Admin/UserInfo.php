<style>
    #orderInfo .item > span[data-name] {
        display: block;
    }
</style>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">商户列表</h5>
        <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal" data-target="#searchFilter">
            高级搜索
        </button>
        <button class="btn w96 btn-outline-primary btn-sm float-right" style="margin-right: 15px;" data-toggle="modal"
                id="addUser">
            新增商户
        </button>
        <table id="orderList" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>UID</th>
                <th>密匙</th>
                <th>余额</th>
                <th>结算账号</th>
                <th>结算名称</th>
                <th>账号状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="userInfo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">用户信息</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="form-group">
                        <label for="uid">商户ID</label>
                        <input type="text" class="form-control" data-name="id" value="" disabled>
                    </div>
                    <div class="form-group">
                        <label for="userKey">商户密匙</label>
                        <input type="text" class="form-control" data-name="key" value="" disabled>
                    </div>
                    <div class="form-group">
                        <label for="userKey">商户余额</label>
                        <input type="text" class="form-control" data-name="balance" value="" placeholder="请输入商户余额">
                    </div>
                    <div class="form-group">
                        <label>结算类型</label>
                        <select class="form-control" data-name="clearType">
                            <option value="1">银行转账（手动）</option>
                            <option value="2">微信转账（手动）</option>
                            <option value="3">支付宝转账（手动）</option>
                            <option value="4">支付宝转账（自动）</option>
                        </select>
                    </div>
                    <div class="form-group" id="deposit" style="display: none;">
                        <label for="deposit">保证金金额</label>
                        <input type="text" class="form-control" data-name="deposit" value="" placeholder="保证金金额">
                        <small class="form-text text-muted">自动转账规则 保留保证金 达到结算金额则进行结算
                        </small>
                    </div>
                    <div class="form-group" id="settleMoney" style="display: none;">
                        <label for="settleMoney">结算金额</label>
                        <input type="text" class="form-control" data-name="settleMoney" value="" placeholder="结算金额">
                        <small class="form-text text-muted">自动转账规则 保留保证金 达到结算金额则进行结算
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="account1">结算账号</label>
                        <input type="text" class="form-control" data-name="account" value="" placeholder="结算账号">
                    </div>
                    <div class="form-group">
                        <label for="username1">结算名称</label>
                        <input type="text" class="form-control" data-name="username" value="" placeholder="请输入结算用户名">
                    </div>
                    <div class="form-group">
                        <label for="rate">结算费率</label>
                        <input type="text" class="form-control" data-name="rate" value="" placeholder="请输入结算费率">
                    </div>
                    <div class="form-group">
                        <label for="payMoneyMax">单笔最大支付金额</label>
                        <input type="text" class="form-control" data-name="payMoneyMax" value="" placeholder="单笔最大支付金额">
                    </div>
                    <div class="form-group">
                        <label for="payDayMoneyMax">单日最大支付金额</label>
                        <input type="text" class="form-control" data-name="payDayMoneyMax" value=""
                               placeholder="单日最大累计金额">
                        <small class="form-text text-muted">0 为不限制</small>
                    </div>
                    <div class="form-group">
                        <label for="domain">网站域名</label>
                        <input type="text" class="form-control" data-name="domain" value="" placeholder="网站域名">
                    </div>
                    <div class="form-group">
                        <label for="email1">电子邮箱</label>
                        <input type="text" class="form-control" data-name="email" value="" placeholder="电子邮箱">
                    </div>
                    <div class="form-group">
                        <label for="qq">QQ账号</label>
                        <input type="text" class="form-control" data-name="qq" value="" placeholder="QQ账号">
                    </div>
                    <div class="form-group">
                        <label for="isBan">是否结算</label>
                        <select class="form-control" data-name="isClear">
                            <option value="1">结算</option>
                            <option value="0">不结算</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="isBan">是否封禁</label>
                        <select class="form-control" data-name="isBan">
                            <option value="1">封禁</option>
                            <option value="0">正常</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="delete">删除账号</button>
                <button type="button" class="btn btn-danger" data-type="reloadKey">重置密匙</button>
                <button type="button" class="btn btn-primary" data-type="save">保存</button>
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
                            <label for="key" class="col-md-3 control-label">商户密匙</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="key" placeholder="商户密匙">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account" class="col-md-3 control-label">结算账号</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="tradeNo" placeholder="结算账号">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="username" class="col-md-3 control-label">结算姓名</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="username" placeholder="结算姓名">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-3 control-label">电子邮箱</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="email" placeholder="电子邮箱">
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
<script src="/static/js/admin/userInfo.js"></script>