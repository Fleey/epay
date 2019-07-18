<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">商户信息</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="#UserInfo">用户中心</a></li>
                    <li class="breadcrumb-item active" aria-current="page">用户信息</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    商户信息
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="uid">商户ID</label>
                        <input type="text" class="form-control" id="uid" value="" disabled>
                    </div>
                    <div class="form-group">
                        <label for="key">商户密钥</label>
                        <input type="text" class="form-control" id="key" value="" disabled>
                    </div>
                    <div class="form-group">
                        <label for="balance">商户余额</label>
                        <input type="text" class="form-control" id="balance" value="" disabled>
                    </div>
                    <div>
                        <label for="balance">冻结金额</label>
                        <input type="text" class="form-control" id="frozenBalance" value="" disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    收款账号
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="settleMode">结算方式</label>
                        <select class="form-control" id="settleMode" disabled>
                            <option value="0">凌晨自动结算</option>
                            <option value="1">手动提交结算</option>
                            <option value="2">系统自动结算</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="settleType">结算类型</label>
                        <select class="form-control" id="settleType">
                            <option value="1">银行卡（手动）</option>
                            <option value="3">支付宝（手动）</option>
                            <option value="2">微信（手动）</option>
                            <option value="4" disabled>支付即时转账（自动）</option>
                            <option value="5" disabled>微信（二维码）</option>
                            <option value="6" disabled>支付宝（二维码）</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="account">收款账号</label>
                        <input type="text" class="form-control" id="account" value=""
                               placeholder="请输入收款账号 必须填写正确否则后果自负">
                    </div>
                    <div class="form-group">
                        <label for="username">真实姓名</label>
                        <input type="text" class="form-control" id="username" value=""
                               placeholder="请输入真实姓名 必须填写正确否则后果自负">
                    </div>
                    <button type="button" class="btn btn-primary float-right" data-type="settle" data-save>保存信息</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    联系方式
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="email">邮箱账号</label>
                        <input type="text" class="form-control" id="email" value="" placeholder="请输入邮箱账号">
                    </div>
                    <div class="form-group">
                        <label for="qq">ＱＱ账号</label>
                        <input type="text" class="form-control" id="qq" value="" placeholder="请输入QQ账号">
                    </div>
                    <div class="form-group">
                        <label for="domain">网站域名</label>
                        <input type="text" class="form-control" id="domain" value="" placeholder="请输入网站域名">
                    </div>
                    <button type="button" class="btn btn-primary float-right" data-type="connectInfo" data-save>保存信息
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function ($) {
        $.getJSON('/user/api/info', function (data) {
            if (data['status'] === 1) {
                data = data['data'];
                if (data['clearMode'] === 2) $('#settleType').attr("disabled", "disabled");
                $('#uid').val(data['id']);
                $('#key').val(data['key']);
                $('#balance').val(data['balance'] / 1000);
                $('#settleType').val(data['clearType']);
                $('#settleMode').val(data['clearMode']);
                $('#account').val(data['account']);
                $('#username').val(data['username']);
                $('#email').val(data['email']);
                $('#qq').val(data['qq']);
                $('#domain').val(data['domain']);
                $('#frozenBalance').val(data['frozenBalance']);
            }
        });
        $('button[data-save]').off("click").on('click', function () {
            var type = $(this).attr('data-type');
            var data = {};
            if (type === 'connectInfo') {
                data = {'email': $('#email').val(), 'qq': $('#qq').val(), 'domain': $('#domain').val()}
            } else if (type === 'settle') {
                data = {
                    'settleType': $('#settleType').val(),
                    'account': $('#account').val(),
                    'username': $('#username').val()
                }
            }
            data['type'] = type;
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.post('/user/api/Info', data, function (data) {
                if (data['status'] === 0) {
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'warning'
                    });
                    return true
                }
                swal({
                    title: '',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'success'
                });
            }, 'json')
        })
    });
</script>