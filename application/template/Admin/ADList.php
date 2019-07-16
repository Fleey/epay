<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">广告列表</h5>
                    <div class="table-responsive">
                        <button class="btn w96 btn-outline-primary btn-sm float-right ml-20" data-toggle="modal"
                                id="addAD">
                            新增广告
                        </button>
                        <table id="adList" class="table no-wrap user-table mb-0 table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>标题</th>
                                <th>是否显示</th>
                                <th>点击次数</th>
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
<div class="modal fade" tabindex="-1" role="dialog" id="ADModel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">新增广告信息</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group row">
                            <label for="uid" class="col-md-3 control-label">标题</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="title" placeholder="广告标题">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="uid" class="col-md-3 control-label">转跳链接</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="hrefUrl" placeholder="转跳链接">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="uid" class="col-md-3 control-label">图片接连</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="imgUrl" placeholder="图片接连">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-md-3 control-label">是否显示</label>
                            <div class="col-md-8">
                                <select class="form-control" id="status">
                                    <option value="0">不显示</option>
                                    <option value="1">显示</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-type="deleteRecord">删除广告</button>
                <button type="button" class="btn btn-primary" data-type="save">保存</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/admin/ADList.js"></script>
