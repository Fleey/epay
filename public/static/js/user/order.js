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
                    var tdDom2 = $(document.createElement("td")).text(value["productName"]).addClass("text-truncate");
                    var tdDom3 = $(document.createElement("td")).text(value["money"] / 100);
                    var tdDom4 = $(document.createElement("td")).text(value['discountMoney'] / 100);
                    var tdDom5;
                    switch (value["type"]) {
                        case 1:
                            tdDom5 = $(document.createElement("td")).text("微信支付");
                            break;
                        case 2:
                            tdDom5 = $(document.createElement("td")).text("财付通支付");
                            break;
                        case 3:
                            tdDom5 = $(document.createElement("td")).text("支付宝支付");
                            break;
                        case 4:
                            tdDom5 = $(document.createElement("td")).text("银联支付");
                            break;
                        default:
                            tdDom5 = $(document.createElement("td")).text("未知方式");
                            break
                    }
                    var tempHtml = value["createTime"] + "<br>" + (value["endTime"] ? value["endTime"] : "");
                    var tdDom6 = $(document.createElement("td")).html(tempHtml);
                    var tdDom7;
                    if (value["status"] === 1) {
                        tdDom7 = $(document.createElement("td")).text("已支付").addClass("text-success")
                    } else if (value['status'] === 0) {
                        tdDom7 = $(document.createElement("td")).text("未支付").addClass("text-danger")
                    } else if (value['status'] === 2) {
                        tdDom7 = $(document.createElement("td")).text("已冻结").addClass("text-danger")
                    } else if (value['status'] === 3) {
                        tdDom7 = $(document.createElement("td")).text("退款中").style({color: "#3b4abb"});
                    } else if (value['status'] === 4) {
                        tdDom7 = $(document.createElement("td")).text("已退款").addClass('text-warning');
                    }
                    trDom.append(tdDom1).append(tdDom2).append(tdDom3).append(tdDom4).append(tdDom5).append(tdDom6).append(tdDom7).append('<td><button data-type="Notified" class="btn btn-outline-primary btn-sm">重新通知</button></td>');
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

    $("#search").off("click").on('click', function () {
        var content = $("#content").val();
        var type = $("#type").val();
        if (content.length !== 0) {
            drawOrderList(1, type, content)
        } else {
            drawOrderList(1)
        }
    })
});