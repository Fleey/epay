<div class="card">
    <div class="card-header">
        订单查询
    </div>
    <div class="card-body">
        <div class="row" style="margin-bottom: 1rem;">
            <div class="col-md-2">
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
            <div class="form-group">
                <button class="btn btn-outline-primary" type="button" id="search">搜索</button>
            </div>
        </div>
        <table class="table table-hover" id="orderList">
            <thead>
            <tr>
                <th scope="col">交易号/商户订单号</th>
                <th scope="col">商品名称</th>
                <th scope="col">商品金额</th>
                <th scope="col">支付方式</th>
                <th scope="col">创建时间/完成时间</th>
                <th scope="col">状态</th>
                <th scope="col">操作</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <p class="text-center" id="tips">数据正在加载中。。。</p>
    </div>
</div>
<script>
    $(function () {
        drawOrderList(1);

        function drawOrderList(page, searchType, searchContent) {
            var searchData = {
                page: page
            };
            if (searchType)
                searchData['type'] = searchType;
            if (searchContent)
                searchData['content'] = searchContent;
            $.post('/user/api/SearchOrder', searchData, function (data) {
                $('#tips').hide();
                $('#orderList>tbody').html('');
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
                        var span1 = $(document.createElement('span')).text(value['tradeNo']);
                        var span2 = $(document.createElement('span')).text(value['tradeNoOut']);
                        var tdDom1 = $(document.createElement('td')).append(span1).append('<br>').append(span2);
                        var tdDom2 = $(document.createElement('td')).text(value['productName']).addClass('text-truncate ');
                        var tdDom3 = $(document.createElement('td')).text(value['money'] / 100);
                        var tdDom4;
                        switch (value['type']) {
                            case 1:
                                tdDom4 = $(document.createElement('td')).text('微信支付');
                                break;
                            case 2:
                                tdDom4 = $(document.createElement('td')).text('财付通支付');
                                break;
                            case 3:
                                tdDom4 = $(document.createElement('td')).text('支付宝支付');
                                break;
                            default:
                                tdDom4 = $(document.createElement('td')).text('未知方式');
                                break;
                        }
                        var tempHtml = value['createTime'] + '<br>' + (value['endTime'] ? value['endTime'] : '');
                        var tdDom5 = $(document.createElement('td')).html(tempHtml);
                        var tdDom6;
                        if (value['status']) {
                            tdDom6 = $(document.createElement('td')).text('已支付').addClass('text-success');
                        } else {
                            tdDom6 = $(document.createElement('td')).text('未支付').addClass('text-danger');
                        }
                        trDom.append(tdDom1).append(tdDom2).append(tdDom3).append(tdDom4).append(tdDom5).append(tdDom6)
                            .append('<td><button data-type="Notified" class="btn btn-outline-primary btn-sm">重新通知</button></td>');
                        $('#orderList>tbody').append(trDom);
                    });
                    $('table button[data-type]').click(function () {
                        var clickType = $(this).attr('data-type');
                        var tradeNo = $(this).parent().parent().find(':nth-child(1)>:nth-child(1)').text();
                        if (tradeNo.length === 0)
                            return true;
                        if (clickType === 'Notified') {
                            $.ajax({
                                url: '/user/api/Notified',
                                type: 'post',
                                async: false,
                                data:{
                                    tradeNo: tradeNo
                                },
                                success: function (data) {
                                    if (data['status'] === 0) {
                                        swal(data['msg'], {
                                            buttons: false,
                                            timer: 1500,
                                            icon: 'warning'
                                        });
                                        return true;
                                    }
                                    window.open(data['url']);
                                }
                            });
                        }
                    });
                    $('#orderList').after('<nav id="pagination" aria-label="Page navigation" class="pagination"></nav>');
                    $('#pagination').html('').Pagination({
                        minPage: 1,
                        maxPage: data['totalPage'],
                        nowPage: page,
                        click_event: clickEvent
                    });
                }
            }, 'json');
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
            var content = $('#content').val();
            var type = $('#type').val();
            if (content.length !== 0)
                drawOrderList(page, type, content);
            else
                drawOrderList(page);
        }

        $('#search').click(function () {
            var content = $('#content').val();
            var type = $('#type').val();
            if (content.length !== 0)
                drawOrderList(1, type, content);
            else
                drawOrderList(1);
        });
    });
</script>