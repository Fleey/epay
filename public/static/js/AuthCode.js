(function($) {
    var ModuleName = 'DorpHelp';
    var Style = '.DorpHelp-AuthCode{padding-bottom: 15px;position: relative;user-select:none;}' +
        '.DorpHelp-AuthCode-Img{vertical-align: top;border-radius: 2px;}' +
        '.DorpHelp-Control{border-radius: 2px;height: 40px;position: relative;border: 1px solid #e4e7eb;background-color: #f7f9fa;}' +
        '.DorpHelp-Tips{text-align: center;color: #45494c;line-height: 40px;}' +
        '.DorpHelp-AuthCode-Refresh{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAWCAYAAAAvg9c4AAAB4klEQVQ4ja3Uy2+MURzG8Y93ZlSYjhK3iRBhgRATl9QlYdN0J4hbhFg28Qf4YyS2rFwWVjZ2EkQkKhGbihSjhJRQjJJh8TvTjs5MTFtP8mYm5z3v95zfOc/vyZmflmI7vuJHYzCbBWBBm7F1OI1dzYO5LiBrsRFF7EAf8viS3h9FDY8aH+Y7AHtRxkmswDKsxqu02Et8QBW/xTEUMdEJWkE/jmEMz/EAr7GkaecbUtn9CbaoE/QQTmANbuIeXuC7OP8Mv9Lc3TiPheKixmeWnyXQBYziUtrdt6YF6+mBAnamsq/idtO7Ka3HRdzApjZH0qwc9uEahtL8FhcVcBaXceYfQKLcrRgUF9qiPErCwGO43wV0Es8wgp/tJmRYmcBVvO0C2lBbYAO6XBxBLT3zVoYe4b/q/wA2oB8TdFz7/u5WvaLqLMMbsduyaLm5aDEOC0eUMuG7EewVrTYXVTCAVUT5Y3iSXmyeA7CEg6J9H+JToxPu4LNIpeIsgD3Yjy14jGGm83RC2OlU+u1o7BkawBGRrbfE/UxB6yIr6wlcSP8n/e3dnLjMMo7jnIjE63iavmmxUDFNPID3aaHRtPNSWqyCbSJX7+KK6bDWDkrkwSD2iLTvE5Z5J+KxJnp/OJXcYsM/FKtkwd+UaZMAAAAASUVORK5CYII=);width:21px;height:22px;position: absolute;right: 3px;top:3px;cursor:pointer;z-index:11;}' +
        '.DorpHelp-Loading{position: absolute;width: 100%;height: 80px;z-index: 10;background-color: #f7f9fa;text-align: center;line-height: 80px;}' +
        '.DorpHelp-Point{width: 26px;height: 26px;border-radius: 50%;position: absolute;background-color: rgba(31,138,255,0.7);text-align: center;color: white;font-weight: 600;line-height: 22px;border: 2px solid rgba(255,255,255,0.8);cursor:move;}';
    var AuthContent,RefreshCodeTime = 0,ClickCount = 1 , MaxClickCount = 0;

    var PostAuthCode = function (options,ClickList){
        console.log('已经要开始Ajax 事实上需要你自己Post 2333');
    };

    var AuthCode = function (ele,args) {
        this.$element = ele;
        this.defaults = {
            'ApiServerUrl': '',
            'ResourcesUrl': '',
            'Key': '',
            'PostInit':PostAuthCode
        };
        this.options = $.extend({},this.defaults,args);
    };
    var GetClickInfo = function () {
        var List = {};
        var ListData = AuthContent.find('[data-click-number]');
        for(var i = 0;i<ListData.length;i++){
            var Div = $(ListData[i]);
            List[i] = {};
            List[i]['y'] = parseInt(Div.css('top'));
            List[i]['x'] = parseInt(Div.css('left'));
        }
        return List;
    };

    var ClickAuthCode = function (event) {
        event = event || window.event;
        var Img = $(this);
        var offsetX = event.pageX - Img.offset().left - 10;
        var offsetY = event.pageY - Img.offset().top - 10;
        if(offsetX <= 0 || offsetY <=0 || ClickCount === (MaxClickCount + 1) || (offsetX+24) >= Img.width() || (offsetY+24) >= Img.height()){
            return;
        }
        var AuthImgPanel = AuthContent.find( '.' + ModuleName+'-AuthCode');
        AuthImgPanel.append('<div class="'+ModuleName+'-Point" data-click-number="'+ClickCount+'">' + ClickCount + '</div>');
        var ClickPointDiv = AuthImgPanel.find('[data-click-number="'+ClickCount+'"]').css({'top' : offsetY, 'left': offsetX});
        var UnMousemove = function () {
            ClickPointDiv.unbind("mousemove");
        };

        ClickPointDiv.mousedown(function (DownEvent) {
            var Left = parseInt($(this).css("left"));
            var Top  = parseInt($(this).css("top"));
            //获取div的初始位置，要注意的是需要转整型，因为获取到值带px

            var DownX= DownEvent.pageX;
            var DownY= DownEvent.pageY;
            //获取鼠标按下时的坐标，区别于下面的MouseMoveEvent.pageX,MouseMoveEvent.pageY

            $(this).bind("mousemove",function(MouseMoveEvent){
                var EndX= MouseMoveEvent.pageX - DownX + Left;
                var EndY= MouseMoveEvent.pageY- DownY +Top;
                //计算div的最终位置
                if(EndX <= 0  || (EndX+24) >= Img.width() || (EndY+24) >= Img.height() || EndY <= 0){
                    UnMousemove();
                    return;
                }
                //限制位置，免得飞出去了
                $(this).css("left",EndX+"px").css("top",EndY+"px")
            });
            //绑定鼠标移动事件
        }).mouseup(UnMousemove);
        AuthImgPanel.mouseup(UnMousemove);
        //删除移动事件
        ClickCount++;
        if(ClickCount === (MaxClickCount+1)){
            AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').hide();
            AuthContent.find('.' + ModuleName + '-Loading').text('请稍后，正在验证您的真实性').show();
            $.callBack(event.data.PostInit,[event.data,GetClickInfo()]);
        }
        //Post数据到目标地址
    };
    AuthCode.prototype = {
        Init:function () {
            this.$element.append('<div class="'+ModuleName+'-AuthCode"></div>');
            AuthContent = this.$element.find('.DorpHelp-AuthCode');
            AuthContent.append('<div class="DorHelp-Panel">' +
                '<div class="'+ModuleName+'-AuthCode">' +
                '<div class="'+ModuleName+'-Loading">正在火速加载中...</div>' +
                '<div class="'+ModuleName+'-AuthCode-Refresh" title="换一下"></div>' +
                '<img class="'+ModuleName+'-AuthCode-Img"></div>' +
                '</div>' +
                '<div class="'+ModuleName+'-Control">' +
                '<div class="'+ModuleName+'-Tips"><span class="'+ModuleName+'-Tips-Text"></span></div>' +
                '</div>');

            if($('#'+ModuleName+'-Style').val() === undefined){
                $('body').append('<style id="'+ModuleName+'-Style">'+Style+'</style>');
            }
            AuthContent.find('.' + ModuleName + '-Tips').text('数据加载中...').show();
            AuthContent.find('.' + ModuleName + '-AuthCode-Img')
                .attr('src',this.options.ResourcesUrl +'?page='+ this.options.Key)
                .bind({
                    load:this.__proto__.CompleteAuthCode,
                    click:ClickAuthCode
                },this.options);
            AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').bind('click',this.options,this.__proto__.RefreshAuthCode);
        },
        RefreshAuthCode:function (event) {
            var options = (typeof(event) === 'object' && typeof(event.data) === 'object') ? event.data:this.options;
            var DifferTime =  Date.parse(new Date()) - RefreshCodeTime;
            AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').show();
            AuthContent.find('.' + ModuleName + '-Loading').hide();
            if(DifferTime<=3000){
                AuthContent.find('.' + ModuleName + '-Tips').text('刷新速度过快...');
                AuthContent.find('.' + ModuleName + '-Loading').text('AvA 请稍后再刷新验证码吧').show();
                AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').show();
                return;
            }
            AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').hide();
            AuthContent.find('.' + ModuleName + '-Loading').text('正在火速加载中...').show();
            AuthContent.find('.' + ModuleName + '-AuthCode-Img').attr('src',options.ResourcesUrl +'?page='+ options.Key + '&' + Math.random());
            AuthContent.find('.' + ModuleName + '-Tips').text('数据加载中...');

            AuthContent.find('[data-click-number]').remove();
            ClickCount = 1;
            //复位点击

            RefreshCodeTime = Date.parse(new Date());
        },
        CompleteAuthCode:function (event) {
            var options = (typeof(event) === 'object' && typeof(event.data) === 'object') ? event.data:this.options;
            AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').show();
            var TipsCode = $.getCookie('AuthCode_' + options.Key);
            var TipsText = '';
            MaxClickCount = TipsCode.length;
            for(var i = 0;i<MaxClickCount;i++){
                if(i === MaxClickCount-1){
                    TipsText += TipsCode[i];
                    break;
                }
                TipsText += TipsCode[i] + ',';
            }
            AuthContent.find('.' + ModuleName + '-Tips').text('请按顺序点击 ' + TipsText);
            AuthContent.find('.' + ModuleName + '-Loading').hide();
        },
        GetClickInfo:function () {
            return GetClickInfo();
        },
        SuccessVerify:function () {
            AuthContent.find('.'+ModuleName+'-AuthCode-Refresh').hide();
            AuthContent.find('.' + ModuleName + '-Loading').text('已经成功通过验证').show();
            AuthContent.find('.' + ModuleName + '-Tips').text('您已经通过人机验证');
        },
        isPassVerify:function () {
            return AuthContent.find('.' + ModuleName + '-Tips').text() === '您已经通过人机验证'
        }
    };
    $.fn.DorpHelpAuthCode = function (options) {
        var AuthCodeFun = new AuthCode(this,options);
        AuthCodeFun.Init();
        return AuthCodeFun;
    };


})( jQuery );