$(function () {
    $.getJSON('/cy2018/api/SettleRecord', {type:'auto'}, function (data) {
        if (data['status'] === 0)
            return true;
        var tempDom = $('#orderList1>tbody');
        tempDom.html('');
        $.each(data['data'], function (key, value) {
            tempDom.append('<tr><td>' + (key + 1) + '</td><td>' + (value['totalMoney'] / 100) + '</td><td>' + value['createTime'] + '</td><td>' +
                '<div class="btn-group btn-group-sm" role="group">' +
                '  <button type="button" class="btn btn-secondary" data-type="downloadSettle">下载结算列表</button>' +
                '</div>' +
                '</td></tr>');
        });
        $('#orderList1>tbody button[data-type]').click(function () {
            var clickType = $(this).attr('data-type');
            var settleTime = $(this).parent().parent().parent().find(':nth-child(3)').text();
            if (clickType === 'downloadSettle') {
                window.open('/cy2018/api/SettleOperate?type=downloadSettleAuto&createTime=' + settleTime);
            }
        });
    });
    $.getJSON('/cy2018/api/SettleRecord', {}, function (data) {
        if (data['status'] === 0)
            return true;
        var tempDom = $('#orderList>tbody');
        tempDom.html('');
        $.each(data['data'], function (key, value) {
            tempDom.append('<tr><td>' + (key + 1) + '</td><td>' + (value['totalMoney'] / 100) + '</td><td>' + value['createTime'] + '</td><td>' +
                '<div class="btn-group btn-group-sm" role="group">' +
                '  <button type="button" class="btn btn-secondary" data-type="confirmSettle">批量完成结算</button>' +
                '  <button type="button" class="btn btn-secondary" data-type="downloadSettle">下载结算列表</button>' +
                '</div>' +
                '</td></tr>');
        });
        $('#orderList>tbody button[data-type]').click(function () {
            var clickType = $(this).attr('data-type');
            var settleTime = $(this).parent().parent().parent().find(':nth-child(3)').text();
            if (clickType === 'confirmSettle') {
                swal({
                    title: '请稍后...',
                    text: '正在积极等待服务器响应',
                    showConfirmButton: false
                });
                $.getJSON('/cy2018/api/SettleOperate', {
                    type: 'confirmSettle',
                    createTime: settleTime
                }, function (data) {
                    if (data['status'] === 1)
                        swal({
                            title: '',
                            text: data['msg'],
                            showConfirmButton: false,
                            timer: 1500,
                            type: 'success'
                        });
                    else
                        swal({
                            title: '',
                            text: '操作失败',
                            showConfirmButton: false,
                            timer: 1500,
                            type: 'error'
                        });
                });
            } else if (clickType === 'downloadSettle') {
                window.open('/cy2018/api/SettleOperate?type=downloadSettle&createTime=' + settleTime);
            }
        });
    });
});