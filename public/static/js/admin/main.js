$(function ($) {
    var hashPath = location.hash.substring(1);
    if (hashPath.length === 0) {
        route('Dashboard', true);
    } else {
        route(hashPath, true);
    }
    $('a[data-href]').off("click").on('click', function () {
        var url = $(this).attr('data-href');
        route(url, false);
    });
    $('.exit').off("click").on('click', function () {
        $.getJSON('/auth/admin/exit', function (data) {
            swal(data['msg'], {
                buttons: false,
                timer: 1500,
                icon: 'success'
            });
            setTimeout(function () {
                window.location.href = baseUrl + 'cy2018/Login';
            }, 1500);
        });
    });
    if (('onhashchange' in window) && ((typeof document.documentMode === 'undefined') || document.documentMode === 8)) {
        window.onhashchange = function () {
            var hashPath = location.hash.substring(1);
            route(hashPath, true);
        }
    }
});
var isAwaitRoute = false;

function route(url, isFirst, args, isGetPageData) {
    isGetPageData = isGetPageData !== undefined;
    var html = '';
    if (!isGetPageData) {
        var sidebarDom = $('.sidebar-nav');
        var clickDom = sidebarDom.find('a[data-href="' + url + '"]');
        if (clickDom.parent().is('.selected') && !isFirst) {
            return;
        }
        if (isAwaitRoute)
            return;
        isAwaitRoute = true;

        location.hash = url;
        sidebarDom.find('li.selected').removeClass('selected');

        clickDom.parent().addClass('selected');
    }
    $.ajax({
        url: baseUrl + 'cy2018/' + url,
        async: !isGetPageData,
        type: 'get',
        cache: true,
        success: function (data) {
            if (isGetPageData) {
                html = data;
                return true;
            }

            window.history.pushState(null, null, baseUrl + 'cy2018/Index#' + url);
            //增加历史地址
            $('.page-wrapper').html(data);
            isAwaitRoute = false;
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            isAwaitRoute = false;
            if (isGetPageData)
                return undefined;
            switch (XMLHttpRequest['status']) {
                case 404:
                    if (isFirst) {
                        route('Dashboard');
                    } else {
                        swal('页面未找到', '页面正在抓紧施工中。。。', 'error');
                    }
                    break;
                case 500:
                    if (isFirst) {
                        route('Dashboard');
                    } else {
                        swal('服务器异常', '当您看到这个提示时请截图相关信息给管理员', 'error');
                    }
                    break;
                default:
                    if (isFirst) {
                        route('Dashboard');
                    } else {
                        swal('请求页面错误', '很抱歉页面请求错误了...', 'error');
                    }
                    break;
            }
        }
    });
    if (isGetPageData)
        return html;
}

function getObjectURL(file) {
    var url = null;
    if (window.createObjectURL !== undefined) { // basic
        url = window.createObjectURL(file);
    } else if (window.URL !== undefined) {
        // mozilla(firefox)
        url = window.URL.createObjectURL(file);
    } else if (window.webkitURL !== undefined) {
        // webkit or chrome
        url = window.webkitURL.createObjectURL(file);
    }
    return url;
}

//上传文件转连接 本地
function readFileHash(file, callBack, args) {
    if (args === undefined)
        args = [];
    args['fileInfo'] = file;
    var fileReader = new FileReader();
    fileReader.readAsArrayBuffer(file);
    fileReader.onload = function () {
        var wordArray = CryptoJS.lib.WordArray.create(fileReader.result);
        var hash = CryptoJS.SHA256(wordArray).toString();
        callBack(hash, args); // Compute hash
    };

    fileReader.onerror = function () {
        swal('程序异常', '读取文件异常，请重试', 'error');
    };
}

//读取文件SHA256
function getServerFileID(hash) {
    if (hash === undefined || hash.length === 0)
        return false;
    var fileID = 0;
    $.ajax({
        url: '/cy2018/file/FileID',
        type: 'get',
        dataType: 'json',
        async: false,
        data: {
            hash: hash
        },
        success: function (data) {
            if (data['status'])
                fileID = data['fileID'];
        }
    });
    return fileID;
}

//获取文件路径 意在减少重复上传文件数量
function uploadFileCloud(file, folder) {
    var fileID, data = new FormData();
    data.append('folderName', folder);
    data.append('file', file);
    swal({
        title: '正在上传文件',
        text: '请耐心等候，服务器正在拼命干活...',
        showConfirmButton: false
    });
    $.ajax({
        url: '/cy2018/file/UploadFile',
        type: 'post',
        data: data,
        contentType: false,
        processData: false,
        dataType: 'json',
        async: false,
        success: function (data) {
            if (data['status'] === 0) {
                swal('上传失败', data['msg'], 'error');
                return true;
            }
            swal.close();
            fileID = data['fileID'];
        },
        error: function () {
            fileID = 0;
            swal('遇到错误', '请与技术人员联系解决问题', 'error');
        }
    });
    return fileID;
}

//利用文件ID获取文件路径
function getFilePath(fileID) {
    var path = '';
    $.ajax({
        url: '/cy2018/file/filePath/' + fileID + '.json',
        type: 'get',
        dataType: 'json',
        async: false,
        success: function (data) {
            if (data['status'])
                path = data['path'];
        }
    });
    return path;
}