$(function () {

    var dataTableConfig = {
        'language': {url: '/static/zh_CN.txt'},
        'serverSide': true,
        'info': true,
        'autoWidth': false,
        'searching': true,
        'aLengthMenu': [15, 25, 50],
        'deferRender': true,
        'order': [[0, 'desc']],
        'ajax': {
            url: baseUrl + 'cy2018/api/SearchTable',
            type: 'post',
            data: {
                searchTable: 'epay_ad_content'
            }
        },
        destroy: true,
        retrieve: true,
        "bRetrieve": true,
        'columns': [
            {}, {}, {
                'render': function (data) {
                    return data === 0 ? '不显示' : '显示';
                }
            }, {}
        ],
        'columnDefs': [
            {
                'orderable': false,
                'render': function (data, type, row) {
                    return '<div class="btn-group" role="group" aria-label="Button group with nested dropdown"><button type="button" class="btn btn-sm btn-secondary" data-type="more">查看更多</button></div>';
                },
                'targets': 5
            }
        ],
        'fnDrawCallback': function (obj) {
            //渲染完成事件
        }
    };
    $('#adList').DataTable(dataTableConfig);

    $('#addAD').click(function () {
        $('#title').val('');
        $('#hrefUrl').val('');
        $('#imgUrl').val('');
        $('#status').val(0);
        $('#ADModel').modal('show').attr('data-type', 'add').find('.modal-title').text('新增广告信息');
        $('#ADModel button[data-type="deleteRecord"]').hide();
    });
    $('#adList').on('click', 'button[data-type="more"]', function () {
        var trDom = $(this).parent().parent().parent();
        var id = parseInt($(trDom).find('td:nth-child(1)').text());

        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        $.getJSON('/cy2018/api/AD/Info?id=' + id, function (data) {
            if(data['status'] === 0){
                swal({
                    title: '操作提示',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
                return;
            }
            $('#title').val(data['data']['title']);
            $('#hrefUrl').val(data['data']['hrefUrl']);
            $('#imgUrl').val(data['data']['imgUrl']);
            $('#status').val(data['data']['status']);
            swal.close();
            $('#ADModel').attr({
                'data-type':'update',
                'data-id':id
            }).modal('show').find('.modal-title').text('更新广告信息');
            $('#ADModel button[data-type="deleteRecord"]').show();
        });
    });
    $('#ADModel button[data-type="deleteRecord"]').click(function () {
        var id = $('#ADModel').attr('data-id');
        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        $.post('/cy2018/api/AD/Delete', {
            id: id
        }, function (data) {
            if (data['status'] === 1) {
                swal({
                    title: '操作提示',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'success'
                });
                $('#ADModel').modal('hide');
                $('#adList').dataTable().fnDraw(false);
            } else {
                swal({
                    title: '操作提示',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
            }
        }, 'json');
    });
    $('#ADModel button[data-type="save"]').click(function () {
        var title = $('#title').val();
        var hrefUrl = $('#hrefUrl').val();
        var imgUrl = $('#imgUrl').val();
        var status = $('#status').val();
        var id = $('#ADModel').attr('data-id');
        if (title.length === 0 || hrefUrl.length === 0 || imgUrl.length === 0) {
            swal({
                title: '温馨提示',
                text: '参数不能为空',
                showConfirmButton: false,
                timer: 1500,
                type: 'error'
            });
            return;
        }

        swal({
            title: '请稍后...',
            text: '正在积极等待服务器响应',
            showConfirmButton: false
        });
        var requestUrl = '/cy2018/api/AD/';
        var requestType = $('#ADModel').attr('data-type');
        if (requestType === 'add') {
            requestUrl += 'Add';
        } else if (requestType === 'update') {
            requestUrl += 'Update';
        }
        $.post(requestUrl, {
            title: title,
            href: hrefUrl,
            imgUrl: imgUrl,
            status:status,
            id: id
        }, function (data) {
            if (data['status'] === 1) {
                swal({
                    title: '操作提示',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'success'
                });
                $('#ADModel').modal('hide');
                $('#adList').dataTable().fnDraw(false);
            } else {
                swal({
                    title: '操作提示',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'error'
                });
            }
        }, 'json');
    });
});