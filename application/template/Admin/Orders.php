<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">订单列表</h5>
                    <div class="buttons float-right" style="margin-top: -36px;">
                        <button class="btn w96 btn-outline-primary btn-sm" style="margin-right: 10px;"
                                data-toggle="modal"
                                data-target="#batchCallbackContent">
                            订单批量回调
                        </button>
                        <button class="btn w96 btn-outline-primary btn-sm" data-toggle="modal"
                                data-target="#searchFilter">
                            高级搜索
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="orderList" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>订单号</th>
                                <th>商户订单号</th>
                                <th>商品名称</th>
                                <th>金额</th>
                                <th>支付方式</th>
                                <th>支付状态</th>
                                <th>创建时间</th>
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
<div class="modal fade" tabindex="-1" role="dialog" id="orderInfo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">订单信息</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="header">订单信息</p>
                <div class="row">
                    <div class="col-md-3 item">
                        <span class="title">平台订单号码</span>
                        <span data-name="tradeNo"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">商户订单号</span>
                        <span data-name="tradeNoOut"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">商户号</span>
                        <span data-name="uid"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">商品名称</span>
                        <span data-name="productName"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">金额（已经扣除减免金额）</span>
                        <span data-name="money"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">减免金额</span>
                        <span data-name="discountMoney"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">支付方式</span>
                        <span data-name="type"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">创建时间</span>
                        <span data-name="createTime"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">完成时间</span>
                        <span data-name="endTime"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">支付状态</span>
                        <span data-name="status"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">屏蔽状态</span>
                        <span data-name="isShield"></span>
                    </div>
                    <div class="col-md-3 item">
                        <span class="title">用户IP地址</span>
                        <span data-name="ipv4"></span>
                    </div>
                    <div class="col-md-6 item">
                        <span class="title">回调域名</span>
                        <span data-name="notify_url"></span>
                    </div>
                    <div class="col-md-6 item">
                        <span class="title">小微商户号</span>
                        <span data-name="sub_mch_id"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-facebook" data-order-id="" data-type="setRefund">退款订单</button>
                <button type="button" class="btn btn-danger" data-order-id="" data-type="setShield">屏蔽订单</button>
                <button type="button" class="btn btn-danger" data-order-id="" data-type="setFrozen">冻结订单</button>
                <button type="button" class="btn btn-primary" data-order-id="" data-type="reloadNotify">重新回调</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="batchCallbackContent" role="dialog" aria-labelledby="batchCallbackContent">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">批量回调（注意只能回调已成功订单）</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group row">
                            <label for="uid" class="col-md-3 control-label">商户ID</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="uid1" placeholder="商家ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="payTypeCallback" class="col-md-3 control-label">支付类型</label>
                            <div class="col-md-8">
                                <select class="form-control" id="payTypeCallback" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">微信支付</option>
                                    <option value="2">财付通支付</option>
                                    <option value="3">支付宝支付</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="payTypeCallback" class="col-md-3 control-label">开始时间</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="startCallbackTime"
                                       placeholder="开始回调时间 2019-2-29 00:00:00">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="payTypeCallback" class="col-md-3 control-label">结束时间</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="endCallbackTime"
                                       placeholder="结束回调时间 2019-2-30 23:59:59">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary w96" id="batchCallback">批量回调</button>
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
                            <label for="productName" class="col-md-3 control-label">商品名称</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="productName" placeholder="商品名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tradeNo" class="col-md-3 control-label">平台订单编号</label>
                            <div class="col-md-8">
                                <input type="int" class="form-control" id="tradeNo" placeholder="平台订单编号">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tradeNoOut" class="col-md-3 control-label">商家订单编号</label>
                            <div class="col-md-8">
                                <input type="int" class="form-control" id="tradeNoOut" placeholder="商家订单编号">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="orderIsShield" class="col-md-3 control-label">订单屏蔽状态</label>
                            <div class="col-md-8">
                                <select class="form-control" id="orderIsShield" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">已屏蔽</option>
                                    <option value="0">未屏蔽</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="orderStatus" class="col-md-3 control-label">订单状态</label>
                            <div class="col-md-8">
                                <select class="form-control" id="orderStatus" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">已支付</option>
                                    <option value="0">未支付</option>
                                    <option value="2">冻结中</option>
                                    <option value="3">退款中</option>
                                    <option value="4">已退款</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="payType" class="col-md-3 control-label">支付类型</label>
                            <div class="col-md-8">
                                <select class="form-control" id="payType" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <option value="1">微信支付</option>
                                    <option value="2">财付通支付</option>
                                    <option value="3">支付宝支付</option>
                                    <option value="4">银联支付</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="productMinPrice" class="col-md-3 control-label">订单金额</label>
                            <div class="col-md-4">
                                <input type="float" class="form-control" id="productMinPrice" placeholder="最低价格">
                            </div>
                            <label class="spent">~</label>
                            <div class="col-md-4">
                                <input type="float" class="form-control" id="productMaxPrice" placeholder="最高价格">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="productMinPrice" class="col-md-3 control-label">订单创建时间</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="productStartTime" placeholder="开始时间">
                            </div>
                            <label class="spent">~</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="productEndTime" placeholder="结束时间">
                            </div>
                            <small class="form-text text-muted" style="position: relative;left: 140px;">时间格式 2019-2-30
                                23:59:59
                            </small>
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
<script src="/static/js/admin/orders.js"></script>