<style>
    #orderInfo .item > span[data-name] {
        display: block;
    }

    .QrCodeImgPreview {
        width: 128px;
        height: 128px;
        text-align: center;
        display: block;
        margin: 0 auto;
        border: dashed 2px;
        line-height: 7.5rem;
        cursor: pointer;
    }
</style>
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">商户列表</h5>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            data-target="#searchFilter">
                        高级搜索
                    </button>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" style="margin-right: 15px;"
                            data-toggle="modal"
                            id="addUser">
                        新增商户
                    </button>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" style="margin-right: 15px;"
                            id="batchSetFee">
                        批量调整费率
                    </button>
                    <div class="table-responsive">
                        <table id="orderList1" class="table no-wrap user-table mb-0 table-hover">
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
            </div>
        </div>
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
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="uid">商户ID</label>
                                <input type="text" class="form-control" data-name="id" value="" disabled>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="userKey">商户密匙</label>
                                <input type="text" class="form-control" data-name="key" value="" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>商户余额</label>
                                <input type="text" class="form-control" data-name="balance" value="" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>设置金额</label>
                                <input type="text" class="form-control" data-name="setUserBalance" value=""
                                       placeholder="设置用户余额 例如+50 -100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="isBan">是否封禁</label>
                                <select class="form-control" data-name="isBan">
                                    <option value="0">正常</option>
                                    <option value="1">封禁</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clearMode">结算方式</label>
                                <select class="form-control" data-name="clearMode">
                                    <option value="0">凌晨自动结算</option>
                                    <option value="1">手动提交结算</option>
                                    <option value="2">系统自动结算</option>
                                    <option value="3">自定义时间自动结算</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>结算类型</label>
                                <select class="form-control" data-name="clearType"></select>
                            </div>
                        </div>
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
                    <div class="clearModeQr" style="display: none;">
                        <input type="file" id="QrCodeImg" style="display: none;" accept=".png,.jpg,.gif">
                        <span class="QrCodeImgPreview">点击上传二维码</span>
                        <img class="QrCodeImgPreview" style="display: none;">
                    </div>
                    <div class="clearModeAccount" style="display: none;">
                        <div class="form-group">
                            <label for="account1">结算账号</label>
                            <input type="text" class="form-control" data-name="account" value="" placeholder="结算账号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username1">结算名称</label>
                        <input type="text" class="form-control" data-name="username" value=""
                               placeholder="请输入结算用户名">
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="settleHour">每N小时执行自动结算（从上一单结算完成开始计算时间）</label>
                        <input type="text" class="form-control" data-name="settleHour" value=""
                               placeholder="每N小时执行自动结算 不支持超过24小时 不支持小数">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rate">结算费率（百分比%）</label>
                                <input type="text" class="form-control" data-name="rate" value="" placeholder="请输入结算费率">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="settleFee">每笔结算手续费（单位RMB）</label>
                                <input type="text" class="form-control" data-name="settleFee" value=""
                                       placeholder="结算手续费 支持两位小数 0 则不收手续费">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payMoneyMax">单笔最大支付金额</label>
                                <input type="text" class="form-control" data-name="payMoneyMax" value=""
                                       placeholder="单笔最大支付金额 0 为不限制">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payDayMoneyMax">单日最大支付金额</label>
                                <input type="text" class="form-control" data-name="payDayMoneyMax" value=""
                                       placeholder="单日最大累计金额 0 为不限制">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="domain">网站域名</label>
                                <input type="text" class="form-control" data-name="domain" value="" placeholder="网站域名">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email1">电子邮箱</label>
                                <input type="text" class="form-control" data-name="email" value="" placeholder="电子邮箱">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="qq">QQ账号</label>
                                <input type="text" class="form-control" data-name="qq" value="" placeholder="QQ账号">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="productNameShowMode">商品名显示模式</label>
                        <select class="form-control" data-name="productNameShowMode">
                            <option value="0">默认系统商品名称</option>
                            <option value="1">商户自行指定商品名称</option>
                            <option value="2">按接口请求商品名字</option>
                        </select>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="productName">商户自行指定商品名称</label>
                        <input type="text" class="form-control" data-name="productName" value=""
                               placeholder="商户自行指定商品名称">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="delete">删除账号</button>
                <button type="button" class="btn btn-danger" data-type="reloadKey">重置密匙</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#setPayConfig">支付配置
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#userMoneyLog">金额操作记录
                </button>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#setOrderDiscounts">
                    设置下单减免
                </button>
                <button type="button" class="btn btn-primary" data-type="save">保存</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="userMoneyLog" role="dialog" aria-labelledby="userMoneyLog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">用户金额操作记录</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="userMoneyLogTable" class="table no-wrap user-table mb-0 table-hover">
                        <thead>
                        <tr>
                            <th>金额</th>
                            <th>备注</th>
                            <th>操作时间</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="setPayConfig" role="dialog" aria-labelledby="setPayConfig">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">支付配置</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-md-12" data-name="alipay">
                                <label class="control-label">支付宝支付</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="apiType">
                                            <option value="0">原生支付接口</option>
                                            <option value="1">易支付中央系统</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="payAisle" disabled>
                                            <option value="0">没有更多选项</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="isOpen">
                                            <option value="1">开启</option>
                                            <option value="0">关闭</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12" data-name="qqpay">
                                <label class="control-label">QQ钱包支付</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="apiType">
                                            <option value="0">原生支付接口</option>
                                            <option value="1">易支付中央系统</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="payAisle" disabled>
                                            <option value="0">没有更多选项</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="isOpen">
                                            <option value="1">开启</option>
                                            <option value="0">关闭</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12" data-name="wxpay">
                                <label class="control-label">微信支付</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="apiType">
                                            <option value="0">原生支付接口</option>
                                            <option value="1">易支付中央系统</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="payAisle" disabled>
                                            <option value="0">没有更多选项</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="isOpen">
                                            <option value="1">开启</option>
                                            <option value="0">关闭</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12" data-name="bankpay">
                                <label class="control-label">银联支付</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="apiType">
                                            <option value="0">原生支付接口</option>
                                            <option value="1">易支付中央系统</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="payAisle" disabled>
                                            <option value="0">没有更多选项</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" data-value="isOpen">
                                            <option value="1">开启</option>
                                            <option value="0">关闭</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="control-label">取消支付是否返回原页面（仅手机访问有效）</label>
                            <select class="form-control" data-name="isCancelReturn">
                                <option value="false">关闭</option>
                                <option value="true">开启</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w96" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="setOrderDiscounts" role="dialog" aria-labelledby="setOrderDiscounts">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">设置下单减免</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="uid" class="control-label">是否开启本功能</label>
                                <select class="form-control" data-name="isOrderDiscountsOpen">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                                <small class="form-text text-muted">注意，订单金额减免后为零或者负数则会下单失败</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="uid" class="control-label">最低减免金额</label>
                                <input type="text" class="form-control" data-name="orderDiscountsMinMoney"
                                       placeholder="低于或等于这个金额，则不进行减免">
                                <small class="form-text text-muted">0则不限制 如果减免后金额为负数或0就会触发免单机制</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="uid" class=" control-label">减免类型</label>
                                <select class="form-control" data-name="orderDiscountsType">
                                    <option value="0">固定减免</option>
                                    <option value="1">随机减免</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="uid" class=" control-label">减免金额列表</label>
                                <div class="orderDiscountsMoneyList">
                                    <div class="row item">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <input type="text" class="form-control" data-name="discountsMoney"
                                                       placeholder="不能为零或不能为负数">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="button-groups">
                                                <button class="btn btn-info" type="button" data-type="appendItem"
                                                        style="margin-right: 10px;">插入
                                                </button>
                                                <button class="btn btn-danger" type="button" data-type="deleteItem">删除
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w96" data-dismiss="modal">确定</button>
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