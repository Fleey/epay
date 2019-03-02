<div class="card">
    <div class="card-body">
        <h5 class="card-title">基本信息</h5>
        <div class="form-group">
            <label for="partner">网站名称</label>
            <input type="text" class="form-control" data-name="webName" placeholder="请输入网站名称">
        </div>
        <div class="form-group">
            <label for="partner">客服QQ</label>
            <input type="text" class="form-control" data-name="webQQ" placeholder="请输入客服QQ">
        </div>
        <div class="form-group">
            <label for="partner">默认结算费率</label>
            <input type="text" class="form-control" data-name="defaultMoneyRate" placeholder="请输入默认结算费率">
            <small class="form-text text-muted">默认支付分成比例（百分数） 例如：97 = 收取3%的费率</small>
        </div>
        <div class="form-group">
            <label for="partner">默认每笔最大支付金额</label>
            <input type="text" class="form-control" data-name="defaultMaxPayMoney" placeholder="请输入默认每笔订单最大支付金额">
        </div>
        <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
    </div>
</div>
<div class="card" style="margin-top: 2rem;">
    <div class="card-body">
        <h5 class="card-title">支付回调域名配置</h5>
        <div class="form-group">
            <label for="notifyDomain">回调域名</label>
            <input type="text" class="form-control" data-name="notifyDomain" placeholder="请输入回调域名">
            <small class="form-text text-muted">例如 http://www.baidu.com</small>
        </div>
        <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
    </div>
</div>
<div class="card" style="margin-top: 2rem;">
    <div class="card-body">
        <h5 class="card-title">管理员账号</h5>
        <div class="form-group">
            <label for="username"></label>
            <input type="text" class="form-control" id="username" placeholder="请输入管理员账号">
        </div>
        <div class="form-group">
            <label for="password">管理员密码</label>
            <input type="password" class="form-control" id="password" placeholder="请输入管理员密码">
        </div>
        <button type="button" class="btn btn-outline-primary float-right" id="setAdmin">保存</button>
    </div>
</div>
<div class="card" style="margin-top: 2rem;">
    <div class="card-body">
        <h5 class="card-title">极验证配置</h5>
        <div class="form-group">
            <label for="geetestCaptchaID">CAPTCHA_ID</label>
            <input type="text" class="form-control" data-name="geetestCaptchaID" placeholder="CAPTCHA_ID">
        </div>
        <div class="form-group">
            <label for="geetestPrivateKey">PRIVATE_KEY</label>
            <input type="text" class="form-control" data-name="geetestPrivateKey" placeholder="PRIVATE_KEY">
        </div>
        <button type="button" class="btn btn-outline-primary float-right" data-save>保存</button>
    </div>
</div>
<script>
    $(function () {
        $('[data-name]').each(function (key, value) {
            var dataDom = $(value);
            var configName = dataDom.attr('data-name');
            $.getJSON('/admin/api/Config', {keyName: configName}, function (data) {
                if (data['status'] === 1) {
                    if (configName === 'defaultMaxPayMoney' || configName === 'defaultMoneyRate') {
                        data['data'] = data['data'] / 100;
                    }
                    dataDom.val(data['data']);
                }
            });
        });
        $('#setAdmin').click(function () {
            var username = $('#username').val();
            var password = $('#password').val();
            if (username.length === 0) {
                swal({
                    title: '',
                    text: '管理员用户名不能为空',
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
                return true;
            }
            if (password.length === 0) {
                swal({
                    title: '',
                    text: '管理员密码不能为空',
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
                return true;
            }
            $.post('/admin/api/SetAdmin', {
                username: username,
                password: password
            }, function (data) {
                if (data['status'] === 0) {
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'error'
                    });
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
        $('button[data-save]').click(function () {
            var buttonDom = $(this);
            buttonDom.parent().find('[data-name]').each(function (key, value) {
                var dom = $(value);
                var keyName = dom.attr('data-name');
                var configValue = dom.val();
                if (configValue === '0' || configValue === '1') {
                    configValue = configValue !== '0';
                }
                $.post('/admin/api/Config', {
                    keyName: keyName,
                    data: configValue
                });
                swal({
                    title: '',
                    text: '保存数据成功',
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'success'
                });
            });
        });
    });
</script>