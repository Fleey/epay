<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
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
        </div>
        <div class="col-md-12">
            <div class="card">
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
        </div>
        <div class="col-md-12">
            <div class="card">
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
        </div>
        <div class="col-md-12">
            <div class="card">
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
        </div>
    </div>
</div>
<script>
    $(function(){$("[data-name]").each(function(a,b){var c=$(b),d=c.attr("data-name");$.getJSON("/cy2018/api/Config",{keyName:d},function(a){1===a["status"]&&(("defaultMaxPayMoney"===d||"defaultMoneyRate"===d)&&(a["data"]=a["data"]/100),c.val(a["data"]))})}),$("#setAdmin").click(function(){var a=$("#username").val(),b=$("#password").val();return 0===a.length?(swal({title:"",text:"管理员用户名不能为空",showConfirmButton:!1,timer:1500,type:"error"}),!0):0===b.length?(swal({title:"",text:"管理员密码不能为空",showConfirmButton:!1,timer:1500,type:"error"}),!0):($.post("/cy2018/api/SetAdmin",{username:a,password:b},function(a){0===a["status"]&&swal({title:"",text:a["msg"],showConfirmButton:!1,timer:1500,type:"error"}),swal({title:"",text:a["msg"],showConfirmButton:!1,timer:1500,type:"success"})},"json"),void 0)}),$("button[data-save]").click(function(){var a=$(this);a.parent().find("[data-name]").each(function(a,b){var c=$(b),d=c.attr("data-name"),e=c.val();("0"===e||"1"===e)&&(e="0"!==e),$.post("/cy2018/api/Config",{keyName:d,data:e}),swal({title:"",text:"保存数据成功",showConfirmButton:!1,timer:1500,type:"success"})})})});
</script>