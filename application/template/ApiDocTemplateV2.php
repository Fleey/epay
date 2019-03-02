<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>开发文档V2 | <?php echo $webName; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="keywords" content="<?php echo $webName; ?>">
    <meta name="description" content="<?php echo $webName; ?>"/>
    <meta content="Fleey" name="author"/>

    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/animate.css/3.7.0/animate.min.css" rel="stylesheet">
    <link href="/static/css/doc/style.min.css" rel="stylesheet"/>
    <link href="/static/css/doc/style-responsive.min.css" rel="stylesheet"/>
    <link href="/static/css/doc/blue.css" rel="stylesheet">
</head>
<body data-spy="scroll" data-target="#header-navbar" data-offset="51">
<div id="page-container" class="fade">
    <div id="header" class="header navbar navbar-inverse" style="margin-bottom: 0!important;border-radius:0!important;">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#header-navbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="/" class="navbar-brand">
                    <span class="brand-logo"></span>
                    <span class="brand-text">
                            <span class="text-theme"><?php echo $webName; ?></span>
                    </span>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="header-navbar">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/">首页</a></li>
                    <li><a href="./#service">优势</a></li>
                    <li><a href="./#team">团队</a></li>
                    <li><a href="./SDK">在线测试</a></li>
                    <li class="active"><a href="./doc.php">开发文档</a></li>
                    <li><a href="./user/reg.php">接入申请</a></li>
                    <li><a href="./user/">商户登录</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content has-bg home" style="height:160px;">
        <div class="content-bg">
            <img src="/static/images/client-bg.jpg" alt="Home"/>
        </div>
        <div class="container home-content" style="margin-top: -40px!important;">
            <h2>开发文档</h2>
            <h4>参考下面文档，快速接入<?php echo $webName; ?>吧！</h4>
        </div>
    </div>
    <div class="container" style="padding-top:30px;">
        <div class="row">
            <div class="col-md-3 ">
                <div id="toc" class="bc-sidebar">
                    <ul class="nav">
                        <li class="toc-h2 toc-active"><a href="#toc0">支付接口介绍</a></li>
                        <li class="toc-h2"><a href="#toc2">协议规则</a></li>
                        <hr>
                        <li class="toc-h2"><a href="#api0">[API]查询订单</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <article class="post page">
                    <section class="post-content">
                        <h2 id="toc0">支付接口介绍</h2>
                        <blockquote><p>使用此接口可以实现支付宝、QQ钱包、微信支付与财付通的即时到账，免签约，无需企业认证。</p></blockquote>
                        <p>本文阅读对象：商户系统（在线购物平台、人工收银系统、自动化智能收银系统或其他）集成<?php echo $webName; ?>
                            涉及的技术架构师，研发工程师，测试工程师，系统运维工程师。</p>
                        <h2 id="toc2">协议规则</h2>
                        <p>传输方式：HTTP 或 HTTPS</p>
                        <p>数据格式：JSON</p>
                        <p>签名算法：MD5</p>
                        <p>字符编码：UTF-8</p>
                        <hr/>
                        <h2 id="requestInfo">签名与公共请求参数</h2>
                        <p>请求参数说明：</p>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>字段名</th>
                                <th>变量名</th>
                                <th>必填</th>
                                <th>类型</th>
                                <th>示例值</th>
                                <th>描述</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>商户号</td>
                                <td>uid</td>
                                <td>是</td>
                                <td>String</td>
                                <td>1024</td>
                                <td>商户id</td>
                            </tr>
                            <tr>
                                <td>一堆其他参数</td>
                                <td>*</td>
                                <td>*</td>
                                <td>*</td>
                                <td>*</td>
                                <td>一堆其他参数</td>
                            </tr>
                            <tr>
                                <td>签名类型</td>
                                <td>sign_type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>固定参数</td>
                            </tr>
                            <tr>
                                <td>参数签名</td>
                                <td>sign</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>
                                    签名算法与<a href="https://doc.open.alipay.com/docs/doc.htm?treeId=62&amp;articleId=104741&amp;docType=1"
                                            target="_blank">支付宝签名算法</a>相同
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <hr>
                        <h2 id="requestInfo">签名与公共返回参数</h2>
                        <p>返回参数说明：</p>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>字段名</th>
                                <th>变量名</th>
                                <th>必填</th>
                                <th>类型</th>
                                <th>示例值</th>
                                <th>描述</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>请求状态码</td>
                                <td>status</td>
                                <td>是</td>
                                <td>int</td>
                                <td>1</td>
                                <td>返回状态 0 失败 1成功</td>
                            </tr>
                            <tr>
                                <td>返回数据</td>
                                <td>data</td>
                                <td>可选</td>
                                <td>Json</td>
                                <td>{"用户名":"河伯人"}</td>
                                <td>返回的一些数据 不可描述 <span style="color: red">当请求状态码返回0时将会不显示data参数</span></td>
                            </tr>
                            <tr>
                                <td>签名类型</td>
                                <td>sign_type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>固定参数</td>
                            </tr>
                            <tr>
                                <td>参数签名</td>
                                <td>sign</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>
                                    签名算法与<a href="https://doc.open.alipay.com/docs/doc.htm?treeId=62&amp;articleId=104741&amp;docType=1"
                                            target="_blank">支付宝签名算法</a>相同
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <hr>
                        <h2 id="api0">[API]查询订单</h2>
                        <p>API权限：该API只能合作支付商户调用</p>
                        <p>
                            URL地址：<?php echo url('/api/v2/Order', '', false, true); ?></p>
                        <p>请求方式：<span style="color: red;">POST</span></p>
                        <p>请求参数说明：</p>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>字段名</th>
                                <th>变量名</th>
                                <th>必填</th>
                                <th>类型</th>
                                <th>示例值</th>
                                <th>描述</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>平台订单号</td>
                                <td>tradeNo</td>
                                <td>可选</td>
                                <td>String</td>
                                <td>123456</td>
                                <td>不可描述</td>
                            </tr>
                            <tr>
                                <td>商户单号</td>
                                <td>tradeNoOut</td>
                                <td>可选</td>
                                <td>String</td>
                                <td>123456</td>
                                <td>不可描述</td>
                            </tr>
                            </tbody>
                        </table>
                        <p>返回结果：</p>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>字段名</th>
                                <th>变量名</th>
                                <th>类型</th>
                                <th>示例值</th>
                                <th>描述</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>易支付订单号</td>
                                <td>trade_no</td>
                                <td>String</td>
                                <td>2016080622555342651</td>
                                <td>订单号</td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td>商户系统内部的订单号</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>uid</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td>发起支付的商户ID</td>
                            </tr>
                            <tr>
                                <td>订单金额</td>
                                <td>money</td>
                                <td>Big Int</td>
                                <td>1000</td>
                                <td>以分为单位 譬如返回 1 则余额等于 1/100 = 0.01</td>
                            </tr>
                            <tr>
                                <td>创建订单时间</td>
                                <td>createTime</td>
                                <td>String</td>
                                <td>2016-08-06 22:55:52</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>完成交易时间</td>
                                <td>endTime</td>
                                <td>String</td>
                                <td>2016-08-06 22:55:52</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商品名称</td>
                                <td>name</td>
                                <td>String</td>
                                <td>VIP会员</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>支付方式</td>
                                <td>type</td>
                                <td>String</td>
                                <td>1</td>
                                <td>支付类型 0 未知支付方式 1 微信支付 2 腾讯财付通支付 3 支付宝支付</td>
                            </tr>
                            </tbody>
                        </table>
                        <hr>
                        <h2 id="sdk0">SDK下载</h2>
                        <blockquote>
                            <a href="#" style="color:blue">PhpSDK.zip</a><br/>
                            PHP 版本：V2.0 目前暂未撰写
                        </blockquote>
                        <blockquote>
                            <a href="#" style="color:blue">JavaSDK.zip</a><br/>
                            Java 版本：V2.0 目前暂未撰写
                        </blockquote>
                    </section>
                </article>
            </div>
        </div>
    </div>
    <div id="footer" class="footer">
        <div class="container">
            <div class="footer-brand">
                <div class="footer-brand-logo"></div>
                <?php echo $webName; ?>
            </div>
            <p>
                Copyright &copy; 2016-2018 <a href="<?php echo url('/', '', false, true); ?>"
                                              target="_blank"><?php echo $webName; ?></a> 版权所有. <br/>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.staticfile.org/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.js"></script>
<script src="https://cdn.staticfile.org/scrollmonitor/1.2.0/scrollMonitor.js"></script>

<script src="/static/js/doc/app.js"></script>
<script>
    $(document).ready(function () {
        App.init();
    });
</script>
</body>
</html>