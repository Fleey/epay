(function ($) {

    $.extend({
        /**
         * @return {string}
         */
        getCookie: function (name) {
            var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
            if (arr = document.cookie.match(reg))
                return unescape(arr[2]);
            else
                return '';
        },
        /**
         * @return {string}
         */
        getRandomStr: function (length) {
            var Chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
            var Ret = '';
            for (var i = 0; i < length; i++) {
                Ret += Chars.charAt(Math.floor(Math.random() * Chars.length));
            }
            return Ret;
        },
        /**
         * @return {number}
         */
        getTime: function () {
            var d = new Date();
            return d.getTime()
        },
        callBack: function (Fun, Args) {
            if (typeof (Fun) === 'function')
                Fun.apply(this, Args);
        },
        isPhone: function (text) {
            return /^1(3|4|5|7|8)\d{9}$/.test(text);
        },
        isEmail: function (text) {
            return /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/.test(text);
        },
        /**
         * @return {string}
         */
        formNow: function (time) {
            // time必须是毫秒
            if (time.length === 10) {
                time *= 1000;
            }
            //如果不是毫秒则补三个0
            var curTime, diff;
            curTime = $.getTime();
            diff = curTime - time;

            if (0 > diff) {
                return '出错了';
            } else if (1000 * 60 > diff) {
                return "刚刚";
            } else if (1000 * 60 <= diff && 1000 * 60 * 60 > diff) {
                return parseInt(diff / (1000 * 60)) + "分钟前";
            } else if (1000 * 60 * 60 <= diff && 1000 * 60 * 60 * 24 > diff) {
                return parseInt(diff / (1000 * 60 * 60)) + "小时前";
            } else if (1000 * 60 * 60 * 24 <= diff && 1000 * 60 * 60 * 24 * 30 > diff) {
                return parseInt(diff / (1000 * 60 * 60 * 24)) + "天前";
            } else if (1000 * 60 * 60 * 24 * 30 <= diff && 1000 * 60 * 60 * 24 * 30 * 12 > diff) {
                return parseInt(diff / (1000 * 60 * 60 * 24 * 30)) + "月前";
            } else {
                return parseInt(diff / (1000 * 60 * 60 * 24 * 30 * 12)) + "年前";
            }
        },
        getUrlParam: function (name) {
            /*?videoId=identification  */
            var params = decodeURI(window.location.search);
            /* 截取？号后面的部分    index.html?act=doctor,截取后的字符串就是?act=doctor  */
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = params.substr(1).match(reg);
            if (r != null) return unescape(r[2]);
            return null;
        }
    });

    var Pagination = function (ele, args) {
        this.$element = ele;
        this.random_id = $.getRandomStr(8);
        this.container = '';
        this.defaults = {
            'minPage': 1,
            'maxPage': 10,
            'nowPage': 3,
            'click_event': function (nowPage, ele) {
            }
        };
        this.options = $.extend({}, this.defaults, args);
    };
    Pagination.prototype = {
        Init: function () {
            if ((this.options.minPage > this.options.nowPage) || (this.options.maxPage < this.options.nowPage)) return;
            this.$element.append('<ul class="pagination pagination-' + this.random_id + ' clearfix"></ul>');
            this.container = this.$element.find('.pagination-' + this.random_id);

            this.__proto__.WriteInfo.call(this, this.options.minPage, this.options.maxPage, this.options.nowPage);

        },
        WriteInfo: function (minPage, maxPage, nowPage) {
            this.container.html('<li class="page-item previous"><a class="page-link"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li>');
            if (minPage === nowPage) {
                this.container.find('.previous').addClass('disabled');
            }
            //后退按钮
            var ForBase, ForMax;
            ForMax = ForBase = 0;
            for (var p = (nowPage - 2); p < nowPage; p++) {
                if (p > minPage)
                    ForBase++;
            }
            for (var o = nowPage; o < (nowPage + 3); o++) {
                if (o <= maxPage)
                    ForMax++;
            }
            ForBase = nowPage - ForBase;
            ForMax += nowPage;
            //计算基础位置

            if (ForBase > minPage) {
                this.container.find('li.previous').parent().append('<li class="page-item"><a class="page-link">' + minPage + '</a>');
                if ((ForBase - 1) !== minPage)
                    this.container.find('li.previous').parent().append('<li class="break page-item"><a class="page-link">...</a></li>');
            }

            for (var i = ForBase; i < ForMax; i++) {
                this.container.append('<li class="' + ((nowPage === i) ? 'active' : '') + ' page-item"><a class="page-link">' + i + '</a></li>');
            }

            this.container.append('<li class="page-item next"><a class="page-link"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>');
            if (maxPage === nowPage) {
                this.container.find('.next').addClass('disabled');
            }
            //前进按钮
            if (ForMax <= maxPage) {
                var NextBtn = this.container.find('li.next');
                if (ForMax !== maxPage)
                    NextBtn.before('<li class="break page-item"><a class="page-link">...</a></li>');

                NextBtn.before('<li class="page-item"><a class="page-link">' + maxPage + '</a>');
            }

            var _this = this;
            this.container.find('li').bind('click', function () {
                var li = $(this).parent();
                if (li.hasClass('active')) {
                    return true;
                }
                // _this.container.find('li').unbind('click');
                $.callBack(_this.options.click_event, [nowPage, $(this)]);
            });
        }
    };
    $.fn.Pagination = function (options) {
        var PaginationA = new Pagination(this, options);
        PaginationA.Init();
        return PaginationA;
    };

    var pageBar = function (ele, args) {
        this.$element = ele;
        this.random_id = $.getRandomStr(8);

        this.defaults = {};
        this.options = $.extend({}, this.defaults, args);
    };

    $.fn.pageBar = function (options) {
        var pageBar;

    };

})(jQuery);
document.write("<style>.data-img{cursor:pointer} .pic_looper{z-index:9999;width:100%; height:100%; position: fixed; left: 0; top:0; opacity: 0.5; background: #000; display: none; } .pic_show{z-index:9999;width:100%; max-width: 1020px; height:520px; position:absolute; left:0; top:0; right:0; bottom:0; margin:auto; text-align: center; display: none; } .pic_box{width:90%; height:450px; margin:40px auto; text-align: center; overflow: hidden; } .pic_box img{height:100%; } .pic_close{width:100%; height:16px; float: right; } .pic_close span{display: block; width:16px; height:16px; float: right; margin:2px 5px; text-align: center; line-height: 16px; cursor: pointer; color:#fff; font-size: 22px; } </style><div class='pic_looper'></div> <div class='pic_show'> <p class='pic_close'><span class='gb' title='关闭'>x</span></p> <div class='pic_box'><img src=''/></div> </div>");
function bindClickImg() {
    $('.data-img').click(function () {
        var img = this.getAttribute('src');
        $('.pic_show img').attr('src', img);
        $('.pic_looper').fadeIn(500);
        $('.pic_show').fadeIn(500);
    });
    $('.gb').click(function () {
        $('.pic_looper').fadeOut(300);
        $('.pic_show').fadeOut(300);
    });
}

function toDecimal2(x) {
    var f = parseFloat(x);
    if (isNaN(f)) {
        return false
    }
    f = Math.round(x * 100) / 100;
    var s = f.toString();
    var rs = s.indexOf(".");
    if (rs < 0) {
        rs = s.length;
        s += "."
    }
    while (s.length <= rs + 2) {
        s += "0"
    }
    return s
}
