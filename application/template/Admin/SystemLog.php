<?php
$filterList = [
        'all',
        '登录系统',
        '订单风控',
        '结算记录',
        '手动回调记录',
        '屏蔽订单记录',
        '配置文件修改'
];
?>
<style>
    #orderInfo .item>span[data-name],#orderInfo .item>img[data-name]{display:block}#orderInfo .item>span.title{font-weight:600}#orderInfo{margin-top:6rem}#orderInfo p.header{font-weight:600;font-size:16px}
</style>
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">系统日志</h5>
                    <button class="btn w96 btn-outline-primary btn-sm float-right" data-toggle="modal"
                            data-target="#searchFilter">
                        高级搜索
                    </button>
                    <div class="table-responsive">
                        <table id="systemLogTable" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>UID</th>
                                <th>类型</th>
                                <th>IPv4</th>
                                <th>操作时间</th>
                                <th>日志信息</th>
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
                            <label for="infoType" class="col-md-3 control-label">信息类型</label>
                            <div class="col-md-8">
                                <select class="form-control" id="infoType" style="width: 100%;">
                                    <option value="all">所有</option>
                                    <?php
                                    foreach ($filterList as $key=>$value){
                                        if($value == 'all')
                                            continue;
                                        echo ' <option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ipv4" class="col-md-3 control-label">IPv4</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="ipv4" placeholder="IPv4">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="data" class="col-md-3 control-label">日志信息</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="data" placeholder="支持模糊搜索">
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
<script>
    <?php
    $temp = '';
    foreach ($filterList as $value){
        $temp.= '"'.$value.'",';
    }
    $temp = substr($temp,0,strlen($temp)-1);
    ?>
    var filterList = [<?php echo $temp; ?>];
</script>
<script src="/static/js/admin/systemLog.js"></script>