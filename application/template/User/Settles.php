<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">结算记录</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="#Settles">结算信息</a></li>
                    <li class="breadcrumb-item active" aria-current="page">结算记录</li>
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
                    <h5 class="card-title text-uppercase mb-0">结算记录</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table no-wrap user-table mb-0 table-hover settle-list">
                            <thead>
                            <tr>
                                <th scope="col" class="border-0 text-uppercase font-medium">ID</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">结算账号</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">结算金额</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">结算时间</th>
                                <th scope="col" class="border-0 text-uppercase font-medium">状态</th>
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
<script type="text/javascript">
    $(function ($) {
        drawSettleData(1);

        function drawSettleData(page) {
            if (!page) page = 1;
            $.ajax({
                url: '/user/api/SettleList',
                type: 'get',
                dataType: 'json',
                data: {page: page},
                success: function (data) {
                    $('#tips').hide();
                    $('.settle-list>tbody').html('');
                    if (data['status'] === 0) {
                        swal({
                            title: '',
                            text: data['msg'],
                            showConfirmButton: false,
                            timer: 1500,
                            type: 'error'
                        });
                    } else {
                        if (data['data'].length === 0) {
                            $('#tips').show().html('暂时查询不到更多数据')
                        }
                        $('#pagination').remove();
                        $.each(data['data'], function (key, value) {
                            var trDom = $(document.createElement('tr'));
                            var tdDom1 = $(document.createElement('td')).text(value['id']);
                            var tdDom2 = $(document.createElement('td')).text(value['account']);
                            var tdDom3 = $(document.createElement('td')).text(value['money'] / 100);
                            var tdDom4;
                            if (value['status']) {
                                tdDom4 = $(document.createElement('td')).text('已结算').addClass('text-success')
                            } else {
                                tdDom4 = $(document.createElement('td')).text('待结算').addClass('text-danger')
                            }
                            var tdDom5 = $(document.createElement('td')).text(value['createTime']);
                            trDom.append(tdDom1).append(tdDom2).append(tdDom3).append(tdDom5).append(tdDom4);
                            $('.settle-list>tbody').append(trDom)
                        });
                        $('.settle-list').after('<nav id="pagination" aria-label="Page navigation" class="pagination"></nav>');
                        $('#pagination').html('').Pagination({
                            minPage: 1,
                            maxPage: data['totalPage'],
                            nowPage: page,
                            click_event: clickEvent
                        })
                    }
                }
            })
        }

        function clickEvent(nowPage, ele) {
            var page = parseInt($(ele).text());
            if (ele.is('.disabled') || ele.is('.break') || ele.is('.active')) {
                return
            }
            if (isNaN(page)) {
                if (ele.is('.previous')) {
                    page = nowPage - 1
                }
                if (ele.is('.next')) {
                    page = nowPage + 1
                }
            }
            drawSettleData(page)
        }
    });
</script>