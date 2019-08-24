<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" data-config-name="alipay">
                    <h5 class="card-title">支付宝接口配置</h5>
                    <div class="form-group">
                        <label for="partner">接口类型</label>
                        <select class="form-control" data-name="apiType">
                            <option value="0">原生接口</option>
                            <option value="1">易支付中央系统</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="partner">是否开启接口</label>
                        <select class="form-control" data-name="isOpen">
                            <option value="1">开启</option>
                            <option value="0">关闭</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="partner">关闭接口提示信息</label>
                        <input type="text" class="form-control" data-name="tips" placeholder="关闭接口提示信息">
                    </div>
                    <div data-api-type="0" style="display: none;">
                        <div class="form-group">
                            <label for="partner">合作身份者ID</label>
                            <input type="text" class="form-control" data-name="partner" placeholder="请输入合作者身份ID">
                            <small class="form-text text-muted">合作身份者id，以2088开头的16位纯数字</small>
                        </div>
                        <div class="form-group">
                            <label for="sellerEmail">收款支付宝账号</label>
                            <input type="text" class="form-control" data-name="sellerEmail" placeholder="收款支付宝账号">
                            <small class="form-text text-muted">收款支付宝账号</small>
                        </div>
                        <div class="form-group">
                            <label for="key">安全检验码</label>
                            <input type="text" class="form-control" data-name="key" placeholder="安全检验码">
                            <small class="form-text text-muted">安全检验码，以数字和字母组成的32位字符</small>
                        </div>
                    </div>
                    <div data-api-type="1" style="display: none;">
                        <div class="form-group">
                            <label for="sellerEmail">商户号（UID）</label>
                            <input type="text" class="form-control" data-name="epayCenterUid" placeholder="请输入商户号">
                            <small class="form-text text-muted">聚合支付中央系统商户号</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户密匙</label>
                            <input type="text" class="form-control" data-name="epayCenterKey" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">聚合支付中央系统商户号密匙 具体请联系相关人员</small>
                        </div>
                    </div>
                    <h4>转账支付宝配置</h4>
                    <hr>
                    <div class="form-group">
                        <label for="transferPartner">合作身份者ID</label>
                        <input type="text" class="form-control" data-name="transferPartner" placeholder="请输入合作者身份ID">
                        <small class="form-text text-muted">合作身份者id，以2088开头的16位纯数字</small>
                    </div>
                    <div class="form-group">
                        <label for="transferPrivateKey">应用私钥</label>
                        <textarea type="text" class="form-control" data-name="transferPrivateKey"
                                  placeholder="应用私钥"></textarea>
                        <small class="form-text text-muted">长度一般都很长，建议使用工具生成然后丢蚂蚁金服<a
                                    href="https://docs.open.alipay.com/291/106097">下载地址</a></small>
                    </div>
                    <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" data-config-name="wxpay">
                    <h5 class="card-title">微信接口配置</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner">接口类型</label>
                                <select class="form-control" data-name="apiType">
                                    <option value="0">原生接口（H5支付）</option>
                                    <option value="2">原生接口（JsAPI支付）</option>
                                    <option value="3">原生接口（小微商户）</option>
                                    <option value="1">易支付中央系统</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner">支付模式</label>
                                <select class="form-control" data-name="apiMode">
                                    <option value="0">H5支付与JsAPI支付共存</option>
                                    <option value="1">仅H5支付</option>
                                    <option value="2">仅JsAPI支付</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner">是否开启接口</label>
                                <select class="form-control" data-name="isOpen">
                                    <option value="1">开启</option>
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner">是否开启高级回调模式（建议被防火墙拦截情况下启动）</label>
                                <select class="form-control" data-name="isOpenAdvNotify">
                                    <option value="1">开启</option>
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="partner">关闭接口提示信息</label>
                        <input type="text" class="form-control" data-name="tips" placeholder="关闭接口提示信息">
                    </div>
                    <div data-api-type="0" style="display: none;">
                        <div class="form-group">
                            <label for="appid">APPID</label>
                            <input type="text" class="form-control" data-name="appid" placeholder="请输入应用ID">
                            <small class="form-text text-muted">绑定支付的APPID（必须配置，开户邮件中可查看）</small>
                        </div>
                        <div class="form-group">
                            <label for="MCHID">MCHID</label>
                            <input type="text" class="form-control" data-name="mchid" placeholder="请输入商户号">
                            <small class="form-text text-muted">商户号（必须配置，开户邮件中可查看）</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户支付密钥</label>
                            <input type="text" class="form-control" data-name="key" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
                                设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
                            </small>
                        </div>
                    </div>
                    <div data-api-type="2" style="display: none;">
                        <div class="form-group">
                            <label for="appid">APPID</label>
                            <input type="text" class="form-control" data-name="jsApiAppid" placeholder="请输入应用ID">
                            <small class="form-text text-muted">绑定支付的APPID（必须配置，开户邮件中可查看）</small>
                        </div>
                        <div class="form-group">
                            <label for="MCHID">MCHID</label>
                            <input type="text" class="form-control" data-name="jsApiMchid" placeholder="请输入商户号">
                            <small class="form-text text-muted">商户号（必须配置，开户邮件中可查看）</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户支付密钥</label>
                            <input type="text" class="form-control" data-name="jsApiKey" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
                                设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="appSecret">AppSecret</label>
                            <input type="text" class="form-control" data-name="jsApiAppSecret" placeholder="公众帐号secert">
                            <small class="form-text text-muted">公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
                                获取地址：<a href="https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN"
                                        target="_blank">点击进入</a>
                            </small>
                        </div>
                    </div>
                    <div data-api-type="1" style="display: none;">
                        <div class="form-group">
                            <label for="sellerEmail">商户号（UID）</label>
                            <input type="text" class="form-control" data-name="epayCenterUid" placeholder="请输入商户号">
                            <small class="form-text text-muted">聚合支付中央系统商户号</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户密匙</label>
                            <input type="text" class="form-control" data-name="epayCenterKey" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">聚合支付中央系统商户号密匙 具体请联系相关人员</small>
                        </div>
                    </div>
                    <div data-api-type="3" style="display: none">
                        <div class="form-group">
                            <label for="wxxMeanMoney">集体号满额均分金额</label>
                            <input type="text" class="form-control" data-name="wxxMeanMoney"
                                   placeholder="请输入满额均分金额，注意不能带小数 单位为元">
                            <small class="form-text text-muted">小微商户专属参数</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" data-config-name="qqpay">
                    <h5 class="card-title">QQ支付接口配置</h5>
                    <div class="form-group">
                        <label for="partner">接口类型</label>
                        <select class="form-control" data-name="apiType">
                            <option value="0">原生接口</option>
                            <option value="1">易支付中央系统</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner">是否开启接口</label>
                                <select class="form-control" data-name="isOpen">
                                    <option value="1">开启</option>
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner">是否开启高级回调模式（建议被防火墙拦截情况下启动）</label>
                                <select class="form-control" data-name="isOpenAdvNotify">
                                    <option value="1">开启</option>
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="partner">关闭接口提示信息</label>
                        <input type="text" class="form-control" data-name="tips" placeholder="关闭接口提示信息">
                    </div>
                    <div data-api-type="0" style="display: none;">
                        <div class="form-group">
                            <label for="sellerEmail">MCHID</label>
                            <input type="text" class="form-control" data-name="mchid" placeholder="请输入商户号">
                            <small class="form-text text-muted">QQ钱包商户号</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户支付密钥</label>
                            <input type="text" class="form-control" data-name="mchkey" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">QQ钱包商户平台(http://qpay.qq.com/)获取</small>
                        </div>
                        <h4>退款配置（不配置则无法使用QQ钱包退款功能）</h4>
                        <hr>
                        <div class="form-group">
                            <label for="opUserID">操作员账户（登录账户）</label>
                            <input type="text" class="form-control" data-name="opUserID"
                                   placeholder="操作员账户（登录账户）">
                            <small class="form-text text-muted">操作员帐号, 默认为商户号 一大串数字</small>
                        </div>
                        <div class="form-group">
                            <label for="opUserPassword">操作员密码（登录密码）</label>
                            <input type="text" class="form-control" data-name="opUserPassword" placeholder="应用私钥"/>
                            <small class="form-text text-muted">操作员密码，默认为商户号登录密码 </small>
                        </div>
                        <div class="form-group">
                            <label for="certPublic">证书公钥</label>
                            <textarea type="text" class="form-control" data-name="certPublic"
                                      placeholder="证书公钥"></textarea>
                            <small class="form-text text-muted">文件名为 apiclient_cert.pem</small>
                        </div>
                        <div class="form-group">
                            <label for="certPrivate">应用私钥</label>
                            <textarea type="text" class="form-control" data-name="certPrivate"
                                      placeholder="应用私钥"></textarea>
                            <small class="form-text text-muted">文件名为 apiclient_key.pem</small>
                        </div>
                    </div>
                    <div data-api-type="1" style="display: none;">
                        <div class="form-group">
                            <label for="sellerEmail">商户号（UID）</label>
                            <input type="text" class="form-control" data-name="epayCenterUid" placeholder="请输入商户号">
                            <small class="form-text text-muted">聚合支付中央系统商户号</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户密匙</label>
                            <input type="text" class="form-control" data-name="epayCenterKey" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">聚合支付中央系统商户号密匙 具体请联系相关人员</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" data-config-name="bankpay">
                    <h5 class="card-title">银联支付</h5>
                    <div class="form-group">
                        <label for="partner">接口类型</label>
                        <select class="form-control" data-name="apiType">
                            <option value="1">易支付中央系统</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="partner">是否开启接口</label>
                        <select class="form-control" data-name="isOpen">
                            <option value="1">开启</option>
                            <option value="0">关闭</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="partner">关闭接口提示信息</label>
                        <input type="text" class="form-control" data-name="tips" placeholder="关闭接口提示信息">
                    </div>
                    <div data-api-type="1" style="display: none;">
                        <div class="form-group">
                            <label for="sellerEmail">商户号（UID）</label>
                            <input type="text" class="form-control" data-name="epayCenterUid" placeholder="请输入商户号">
                            <small class="form-text text-muted">聚合支付中央系统商户号</small>
                        </div>
                        <div class="form-group">
                            <label for="key">商户密匙</label>
                            <input type="text" class="form-control" data-name="epayCenterKey" placeholder="请输入商户支付密钥">
                            <small class="form-text text-muted">聚合支付中央系统商户号密匙 具体请联系相关人员</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" data-config-name="goodsFilter">
                    <h5 class="card-title">支付订单风控</h5>
                    <div class="form-group">
                        <label for="keyWord">拦截关键字</label>
                        <textarea type="text" class="form-control" data-name="keyWord" placeholder="拦截关键字"></textarea>
                        <small class="form-text text-muted">多个关键字用,分割 如：刷钻,黑号,AV</small>
                    </div>
                    <div class="form-group">
                        <label for="tips">拦截提示</label>
                        <input type="text" class="form-control" data-name="tips" placeholder="请输入订单拦截提示">
                    </div>
                    <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/admin/payConfig.js"></script>