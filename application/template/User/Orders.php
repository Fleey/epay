<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">订单记录</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="#Orders">订单信息</a></li>
                    <li class="breadcrumb-item active" aria-current="page">订单记录</li>
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
                    <h5 class="card-title text-uppercase mb-0">订单记录</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 m1">
                            <select class="input-sm form-control" id="type">
                                <option value="1">交易号</option>
                                <option value="2">商户订单号</option>
                                <option value="3">商品名称</option>
                                <option value="4">商品金额</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" class="input-sm form-control" id="content" placeholder="搜索内容">
                            </div>
                        </div>
                        <div class="col-md-4 m1">
                            <button class="btn btn-outline-primary" type="button" id="search">搜索</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table no-wrap user-table mb-0 table-hover" id="orderList">
                            <thead>
                            <tr>
                                <th scope="col" class="border-0 text-uppercase font-medium">交易号/商户订单号</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">商品名称</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">商品金额</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">商品减免金额</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">创建时间/完成时间</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">状态</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">操作</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <p class="text-center" id="tips">数据正在加载中。。。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/user/order.js"></script>