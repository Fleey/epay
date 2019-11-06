<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">用户资料信息管理</h5>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            data-target="#searchFilter">
                        高级搜索
                    </button>
                    <button class="btn w96 mr15 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            id="addAccount">
                        新增用户资料信息
                    </button>
                    <div class="table-responsive">
                        <table id="orderList1" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>身份证名称</th>
                                <th>身份证号码</th>
                                <th>类型</th>
                                <th>昨日交易额</th>
                                <th>当日交易额</th>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">账号类型</label>
                                <select data-name="type" class="form-control">
                                    <option selected disabled>请选择账号类型</option>
                                    <option value="2">独立号</option>
                                    <option value="1">集体号</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="uid">商户号</label>
                                <button class="btn btn-primary form-control" id="relate">设置关联商户号</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reservedMoney">预留金额</label>
                                <input data-name="reservedMoney" class="form-control" placeholder="预留金额 留空不使用" disabled>
                            </div>
                        </div>
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
                                        <input data-name="idCardValidTime1" class="form-control" placeholder="开始时间">
                                    </div>
                                    <div class="col-md-6">
                                        <input data-name="idCardValidTime2" class="form-control" placeholder="结束时间">
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
                        <div class="col-md-6">
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
                                <label for="rate">费率</label>
                                <select class="form-control" data-name="rate">
                                    <option selected disabled>请选择对应 费率</option>
                                    <option value="0.38%">0.38%</option>
                                    <option value="0.39%">0.39%</option>
                                    <option value="0.4%">0.4%</option>
                                    <option value="0.45%">0.45%</option>
                                    <option value="0.48%">0.48%</option>
                                    <option value="0.49%">0.49%</option>
                                    <option value="0.5%">0.5%</option>
                                    <option value="0.55%">0.55%</option>
                                    <option value="0.58%">0.58%</option>
                                    <option value="0.59%">0.59%</option>
                                    <option value="0.6%">0.6%</option>
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
                        <div class="col-md-12">
                            <h4>封号检测配置</h4>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contactPhone">白天检测间隔（分钟）</label>
                                <input data-name="banCheckDay" class="form-control" placeholder="请输入整数 例如 6 为空则不启用">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contactPhone">晚上检测间隔（分钟）</label>
                                <input data-name="banCheckNight" class="form-control" placeholder="请输入整数 例如 3 为空则不启用">
                            </div>
                        </div>
                        <!--检测账户状态参数-->
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
                            <label for="idCardNumber" class="col-md-3 control-label">身份证号码</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="idCardNumber" placeholder="身份证号码">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCardName" class="col-md-3 control-label">身份证姓名</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="idCardName" placeholder="身份证姓名 支持模糊查询">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-md-3 control-label">账号类型</label>
                            <div class="col-md-8">
                                <select id="applyInfoType" class="form-control">
                                    <option selected disabled>请选择账号类型</option>
                                    <option value="2">独立号</option>
                                    <option value="1">集体号</option>
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
<div class="modal fade" id="setApplyInfoRelate" role="dialog" aria-labelledby="setApplyInfoRelate" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">设置商户号关联</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="uid" class="control-label">请输入关联商户号</label>
                            <div class="row">
                                <div class="col-md-9">
                                    <input type="text" class="form-control" data-name="uid"
                                           placeholder="请输入关联商户号">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-info btn-block addRelateUid" type="button"
                                            style="margin-right: 10px;">添加
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="uid" class=" control-label">已关联用户列表</label>
                                <div class="table-responsive">
                                    <table id="relateList" class="table no-wrap user-table mb-0 table-hover">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>商户号</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w96" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/admin/WxxApplyInfo.js"></script>