<div class="card">
    <div class="card-header">
        结算记录
    </div>
    <div class="card-body">
        <table class="table table-striped settle-list">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">结算账号</th>
                <th scope="col">结算金额</th>
                <th scope="col">结算时间</th>
                <th scope="col">状态</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <p class="text-center" id="tips">数据正在加载中。。。</p>
    </div>
</div>
<script type="text/javascript">
    $(function ($) {
        drawSettleData(1);

        function drawSettleData(page) {
            if (!page)
                page = 1;
            $.ajax({
                url: '/user/api/SettleList',
                type: 'get',
                dataType: 'json',
                data: {page: page},
                async: false,
                success: function (data) {
                    $('#tips').hide();
                    $('.settle-list>tbody').html('');
                    if (data['status'] === 0) {
                        swal(data['msg'], {
                            buttons: false,
                            timer: 1500,
                            icon: 'warning'
                        });
                    } else {
                        if (data['data'].length === 0) {
                            $('#tips').show().html('暂时查询不到更多数据');
                        }
                        $('#pagination').remove();
                        $.each(data['data'], function (key, value) {
                            var trDom = $(document.createElement('tr'));
                            var tdDom1 = $(document.createElement('td')).text(value['id']);
                            var tdDom2 = $(document.createElement('td')).text(value['account']);
                            var tdDom3 = $(document.createElement('td')).text(value['money'] / 100);
                            var tdDom4;
                            if (value['status']) {
                                tdDom4 = $(document.createElement('td')).text('已结算').addClass('text-success');
                            } else {
                                tdDom4 = $(document.createElement('td')).text('待结算').addClass('text-danger');
                            }
                            var tdDom5 = $(document.createElement('td')).text(value['createTime']);
                            trDom.append(tdDom1).append(tdDom2).append(tdDom3).append(tdDom5).append(tdDom4);
                            $('.settle-list>tbody').append(trDom);
                        });
                        $('.settle-list').after('<nav id="pagination" aria-label="Page navigation" class="pagination"></nav>');
                        $('#pagination').html('').Pagination({
                            minPage: 1,
                            maxPage: data['totalPage'],
                            nowPage: page,
                            click_event: clickEvent
                        });
                    }
                }
            });
        }

        function clickEvent(nowPage, ele) {
            var page = parseInt($(ele).text());
            if (ele.is('.disabled') || ele.is('.break') || ele.is('.active')) {
                return;
            }
            if (isNaN(page)) {
                if (ele.is('.previous')) {
                    page = nowPage - 1;
                }
                if (ele.is('.next')) {
                    page = nowPage + 1;
                }
            }
            drawSettleData(page);
        }
    });
</script>