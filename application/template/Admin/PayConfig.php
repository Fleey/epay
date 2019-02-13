<div class="card">
    <div class="card-body" data-config-name="alipay">
        <h5 class="card-title">支付宝接口配置</h5>
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
        <h4>转账支付宝配置</h4>
        <hr>
        <div class="form-group">
            <label for="transferPartner">合作身份者ID</label>
            <input type="text" class="form-control" data-name="transferPartner" placeholder="请输入合作者身份ID">
            <small class="form-text text-muted">合作身份者id，以2088开头的16位纯数字</small>
        </div>
        <div class="form-group">
            <label for="transferPrivateKey">应用私钥</label>
            <textarea type="text" class="form-control" data-name="transferPrivateKey" placeholder="应用公钥"></textarea>
            <small class="form-text text-muted">长度一般都很长，建议使用工具生成然后丢蚂蚁金服<a href="https://docs.open.alipay.com/291/106097">下载地址</a></small>
        </div>
        <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
    </div>
</div>
<div class="card" style="margin-top: 2rem;">
    <div class="card-body" data-config-name="wxpay">
        <h5 class="card-title">微信接口配置</h5>
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
        <div class="form-group">
            <label for="appSecret">AppSecret</label>
            <input type="text" class="form-control" data-name="appSecret" placeholder="公众帐号secert">
            <small class="form-text text-muted">公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
                获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
            </small>
        </div>
        <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
    </div>
</div>
<div class="card" style="margin-top: 2rem;">
    <div class="card-body" data-config-name="qqpay">
        <h5 class="card-title">QQ支付接口配置</h5>
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
        <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
    </div>
</div>
<div class="card" style="margin-top: 2rem;">
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
<script>
    $(function () {
        $('div[data-config-name]').each(function (key, value) {
            var dataDom = $(value);
            var configName = dataDom.attr('data-config-name');
            $.getJSON('/admin/api/Config', {keyName: configName}, function (data) {
                if (data['status'] === 1) {
                    $.each(data['data'], function (keyName, configValue) {
                        if (typeof configValue === "boolean") {
                            configValue = configValue ? '1' : '0';
                        }
                        dataDom.find('[data-name="' + keyName + '"]').val(configValue);
                    });
                }
            });
        });
        $('div[data-config-name] button[data-save]').click(function () {
            var buttonDom = $(this);
            var configName = buttonDom.parent().attr('data-config-name');
            var configData = {};
            buttonDom.parent().find('[data-name]').each(function (key, value) {
                var dom = $(value);
                var keyName = dom.attr('data-name');
                var configValue = dom.val();
                if (configValue === '0' || configValue === '1') {
                    configValue = configValue !== '0';
                }
                configData[keyName] = configValue;
            });
            $.post('/admin/api/Config', {
                keyName: configName,
                isArray: true,
                data: configData
            }, function (data) {
                if (data['status'] === 0) {
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'warning'
                    });
                    return true;
                }
                swal({
                    title: '',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'success'
                });
            }, 'json');
        });
    });
</script>