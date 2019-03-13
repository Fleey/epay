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
<script>
    $(function () {
        drawOrderList(1);

        function drawOrderList(page, searchType, searchContent) {
            var searchData = {page: page};
            if (searchType) {
                searchData["type"] = searchType
            }
            if (searchContent) {
                searchData["content"] = searchContent
            }
            $.post("/user/api/SearchOrder", searchData, function (data) {
                $("#tips").hide();
                $("#orderList>tbody").html("");
                if (data["status"] === 0) {
                    swal(data["msg"], {buttons: false, timer: 1500, icon: "warning"})
                } else {
                    if (data["data"].length === 0) {
                        $("#tips").show().html("暂时查询不到更多数据")
                    } else {
                        swal.close()
                    }
                    $("#pagination").remove();
                    $.each(data["data"], function (key, value) {
                        var trDom = $(document.createElement("tr"));
                        var span1 = $(document.createElement("span")).text(value["tradeNo"]);
                        var span2 = $(document.createElement("span")).text(value["tradeNoOut"]);
                        var tdDom1 = $(document.createElement("td")).append(span1).append("<br>").append(span2);
                        var tdDom2 = $(document.createElement("td")).text(value["productName"]).addClass("text-truncate ");
                        var tdDom3 = $(document.createElement("td")).text(value["money"] / 100);
                        var tdDom4;
                        switch (value["type"]) {
                            case 1:
                                tdDom4 = $(document.createElement("td")).text("微信支付");
                                break;
                            case 2:
                                tdDom4 = $(document.createElement("td")).text("财付通支付");
                                break;
                            case 3:
                                tdDom4 = $(document.createElement("td")).text("支付宝支付");
                                break;
                            default:
                                tdDom4 = $(document.createElement("td")).text("未知方式");
                                break
                        }
                        var tempHtml = value["createTime"] + "<br>" + (value["endTime"] ? value["endTime"] : "");
                        var tdDom5 = $(document.createElement("td")).html(tempHtml);
                        var tdDom6;
                        if (value["status"]) {
                            tdDom6 = $(document.createElement("td")).text("已支付").addClass("text-success")
                        } else {
                            tdDom6 = $(document.createElement("td")).text("未支付").addClass("text-danger")
                        }
                        trDom.append(tdDom1).append(tdDom2).append(tdDom3).append(tdDom4).append(tdDom5).append(tdDom6).append('<td><button data-type="Notified" class="btn btn-outline-primary btn-sm">重新通知</button></td>');
                        $("#orderList>tbody").append(trDom)
                    });
                    $("table button[data-type]").click(function () {
                        var clickType = $(this).attr("data-type");
                        var tradeNo = $(this).parent().parent().find(":nth-child(1)>:nth-child(1)").text();
                        if (tradeNo.length === 0) {
                            return true
                        }
                        if (clickType === "Notified") {
                            swal({
                                title: '请稍后...',
                                text: '正在积极等待服务器响应',
                                showConfirmButton: false
                            });
                            $.ajax({
                                url: "/user/api/Notified",
                                type: "post",
                                data: {tradeNo: tradeNo},
                                success: function (data) {
                                    if (data["status"] === 0) {
                                        swal({
                                            title: '',
                                            text: data['msg'],
                                            showConfirmButton: false,
                                            timer: 1500,
                                            type: 'warning'
                                        });
                                        return true
                                    } else {
                                        swal.close();
                                    }
                                    var windows = window.open('_blank');
                                    windows.location = data["url"];
                                }
                            })
                        }
                    });
                    $("#orderList").after('<nav id="pagination" aria-label="Page navigation" class="pagination"></nav>');
                    $("#pagination").html("").Pagination({
                        minPage: 1,
                        maxPage: data["totalPage"],
                        nowPage: page,
                        click_event: clickEvent
                    })
                }
            }, "json")
        }

        function clickEvent(nowPage, ele) {
            var page = parseInt($(ele).text());
            if (ele.is(".disabled") || ele.is(".break") || ele.is(".active")) {
                return
            }
            if (isNaN(page)) {
                if (ele.is(".previous")) {
                    page = nowPage - 1
                }
                if (ele.is(".next")) {
                    page = nowPage + 1
                }
            }
            var content = $("#content").val();
            var type = $("#type").val();
            if (content.length !== 0) {
                drawOrderList(page, type, content)
            } else {
                drawOrderList(page)
            }
        }

        $("#search").click(function () {
            var content = $("#content").val();
            var type = $("#type").val();
            if (content.length !== 0) {
                drawOrderList(1, type, content)
            } else {
                drawOrderList(1)
            }
        })
    });
</script>