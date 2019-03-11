<div class="page-content container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    结算申请
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="settleType">结算方式</label>
                        <select class="form-control" id="settleType" disabled>
                            <option value="1">银行卡（手动）</option>
                            <option value="2">微信（手动）</option>
                            <option value="3">支付宝（手动）</option>
                        </select>
                        <small class="form-text text-muted">修改结算信息，请到个人信息处修改</small>
                    </div>
                    <div class="form-group">
                        <label for="account">收款账号</label>
                        <input type="text" class="form-control" id="account" value="" disabled>
                        <small class="form-text text-muted">修改结算信息，请到个人信息处修改</small>
                    </div>
                    <div class="form-group">
                        <label for="username">真实姓名</label>
                        <input type="text" class="form-control" id="username" value="" disabled>
                        <small class="form-text text-muted">修改结算信息，请到个人信息处修改</small>
                    </div>
                    <div class="form-group">
                        <label for="username">结算金额</label>
                        <input type="text" class="form-control" id="settleMoney" value="">
                        <small class="form-text text-muted">您当前可结算余额为
                            <span id="balance">0</span>
                            <a href="javascript:void(0);" id="settleAll" class="text-danger">结算所有</a>
                        </small>
                    </div>
                    <button type="button" class="btn btn-primary float-right" id="settleApply">提交申请</button>
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
                var money = (data['balance'] / 1000).toFixed(3);
                $('#balance').text(money.substring(0, money.lastIndexOf('.') + 3));
                $('#settleType').val(data['clearType']);
                $('#account').val(data['account']);
                $('#username').val(data['username'])
            }
        });
        $('#settleAll').click(function () {
            $('#settleMoney').val($('#balance').text())
        });
        $('#settleApply').click(function () {
            var settleMoney = $('#settleMoney').val();
            swal({
                title: '请稍后...',
                text: '正在积极等待服务器响应',
                showConfirmButton: false
            });
            $.post('/user/api/settleApply', {money: settleMoney}, function (data) {
                if (data['status'] === 0) {
                    swal({
                        title: '',
                        text: data['msg'],
                        showConfirmButton: false,
                        timer: 1500,
                        type: 'error'
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