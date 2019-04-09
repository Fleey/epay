<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>开发文档V1 | <?php echo $webName; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
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
                        <li class="toc-h2"><a href="#toc1">接口申请方式</a></li>
                        <li class="toc-h2"><a href="#toc2">协议规则</a></li>
                        <hr>
                        <li class="toc-h2"><a href="#api0">[API]创建商户</a></li>
                        <li class="toc-h2"><a href="#api1">[API]查询商户信息</a></li>
                        <li class="toc-h2"><a href="#api2">[API]修改结算账号</a></li>
                        <li class="toc-h2"><a href="#api3">[API]查询结算记录</a></li>
                        <li class="toc-h2"><a href="#api4">[API]查询单个订单</a></li>
                        <li class="toc-h2"><a href="#api5">[API]批量查询订单</a></li>
                        <li class="toc-h2"><a href="#api6">[API]二维码下单接口</a></li>
                        <hr>
                        <li class="toc-h2"><a href="#pay0">发起支付请求</a></li>
                        <li class="toc-h2"><a href="#pay1">支付结果通知</a></li>
                        <hr>
                        <li class="toc-h2"><a href="#sdk0">SDK下载</a></li>
                        <hr>
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
                        <h2 id="toc1">接口申请方式</h2>
                        <p>共有两种接口模式：</p>
                        <p>（一）普通支付商户<br/>可以获得一个支付商户，目前认证费是5元。可在线申请：<a target="_blank"
                                                                      href="http://<?php echo 'test' ?>/user/reg.php">接入申请</a>，在线申请之后即可登录使用！
                        </p>
                        <p>（二）合作支付商户<br/>获得一个合作者身份TOKEN，可以集成到你开发的程序里面，通过接口无限申请普通支付商户，并且每个普通支付商户单独结算，相对独立。申请需要进行企业或开发者资质认证，请联系人工申请QQ<a
                                    target="_blank"
                                    href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $webQQ; ?>&site=qq&menu=yes"><?php echo $webQQ; ?></a>，申请之后会将合作者身份TOKEN给你！（内测暂不开放）
                        </p>
                        <h2 id="toc2">协议规则</h2>
                        <p>传输方式：HTTP</p>
                        <p>数据格式：JSON</p>
                        <p>签名算法：MD5</p>
                        <p>字符编码：UTF-8</p>
                        <hr/>
                        <h2 id="api0">[API]创建商户</h2>
                        <p>API权限：该API只能合作支付商户调用</p>
                        <p>
                            URL地址：<?php echo url('/api.php?act=apply&token={合作者身份TOKEN}&url={商户域名}', '', false, true); ?></p>
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
                                <td>操作类型</td>
                                <td>act</td>
                                <td>是</td>
                                <td>String</td>
                                <td>apply</td>
                                <td>此API固定值</td>
                            </tr>
                            <tr>
                                <td>合作者TOKEN</td>
                                <td>token</td>
                                <td>是</td>
                                <td>String</td>
                                <td>9ddab6c4f2c87ce442de371b04f36d68</td>
                                <td>需要事先申请</td>
                            </tr>
                            <tr>
                                <td>商户域名</td>
                                <td>url</td>
                                <td>是</td>
                                <td>String</td>
                                <td><?php echo 'test' ?></td>
                                <td></td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>返回信息</td>
                                <td>msg</td>
                                <td>String</td>
                                <td>添加支付商户成功！</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td>所创建的商户ID</td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>String(32)</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td>所创建的商户密钥</td>
                            </tr>
                            <tr>
                                <td>商户类型</td>
                                <td>type</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>此值暂无用</td>
                            </tr>
                            </tbody>
                        </table>

                        <h2 id="api1">[API]查询商户信息与结算规则</h2>
                        <p>
                            URL地址：<?php echo url('/api.php?act=query&pid={商户ID}&key={商户密钥}', '', false, true); ?></p>
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
                                <td>操作类型</td>
                                <td>act</td>
                                <td>是</td>
                                <td>String</td>
                                <td>query</td>
                                <td>此API固定值</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>是</td>
                                <td>String</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td></td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td>所创建的商户ID</td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>String(32)</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td>所创建的商户密钥</td>
                            </tr>
                            <tr>
                                <td>商户类型</td>
                                <td>type</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>此值暂无用</td>
                            </tr>
                            <tr>
                                <td>商户状态</td>
                                <td>active</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为正常，0为封禁</td>
                            </tr>
                            <tr>
                                <td>商户余额</td>
                                <td>money</td>
                                <td>String</td>
                                <td>0.00</td>
                                <td>商户所拥有的余额</td>
                            </tr>
                            <tr>
                                <td>结算账号</td>
                                <td>account</td>
                                <td>String</td>
                                <td>pay@v8jisu.cn</td>
                                <td>结算的支付宝账号</td>
                            </tr>
                            <tr>
                                <td>结算姓名</td>
                                <td>username</td>
                                <td>String</td>
                                <td>张三</td>
                                <td>结算的支付宝姓名</td>
                            </tr>
                            <tr>
                                <td>满多少自动结算</td>
                                <td>settle_money</td>
                                <td>String</td>
                                <td>30</td>
                                <td>此值为系统预定义</td>
                            </tr>
                            <tr>
                                <td>手动结算手续费</td>
                                <td>settle_fee</td>
                                <td>String</td>
                                <td>1</td>
                                <td>此值为系统预定义</td>
                            </tr>
                            <tr>
                                <td>每笔订单分成比例</td>
                                <td>money_rate</td>
                                <td>String</td>
                                <td>98</td>
                                <td>此值为系统预定义</td>
                            </tr>
                            </tbody>
                        </table>

                        <h2 id="api2">[API]修改结算账号</h2>
                        <p>
                            URL地址：<?php echo url('/api.php?act=change&pid={商户ID}&key={商户密钥}&account={结算账号}&username={结算姓名}', '', false, true); ?></p>
                        <p>注：为了保障资金安全，已经设置结算账号的无法再进行修改，如需修改请联系客服</p>
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
                                <td>操作类型</td>
                                <td>act</td>
                                <td>是</td>
                                <td>String</td>
                                <td>change</td>
                                <td>此API固定值</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>是</td>
                                <td>String</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>结算账号</td>
                                <td>account</td>
                                <td>是</td>
                                <td>String</td>
                                <td>pay@v8jisu.cn</td>
                                <td>结算的支付宝账号</td>
                            </tr>
                            <tr>
                                <td>结算姓名</td>
                                <td>username</td>
                                <td>是</td>
                                <td>String</td>
                                <td>张三</td>
                                <td>结算的支付宝姓名</td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>返回信息</td>
                                <td>msg</td>
                                <td>String</td>
                                <td>修改收款账号成功！</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>

                        <h2 id="api3">[API]查询结算记录</h2>
                        <p>
                            URL地址：<?php echo url('/api.php?act=settle&pid={商户ID}&key={商户密钥}', '', false, true); ?></p>
                        <p>注意！！！接口只显示最近十条结算记录</p>
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
                                <td>操作类型</td>
                                <td>act</td>
                                <td>是</td>
                                <td>String</td>
                                <td>settle</td>
                                <td>此API固定值</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>是</td>
                                <td>String</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td></td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>返回信息</td>
                                <td>msg</td>
                                <td>String</td>
                                <td>查询结算记录成功！</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>结算记录</td>
                                <td>data</td>
                                <td>Array</td>
                                <td>结算记录列表</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <p>结算数据结构：</p>
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
                                <td>结算类型</td>
                                <td>clearType</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>结算类型 0 未知类型 1 银行转账 2 微信转账 3 支付宝转账</td>
                            </tr>
                            <tr>
                                <td>结算账号</td>
                                <td>account</td>
                                <td>String</td>
                                <td>test@test.cn</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>结算名称</td>
                                <td>username</td>
                                <td>String</td>
                                <td>龙女士</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>结算金额</td>
                                <td>money</td>
                                <td>Big Int</td>
                                <td>1000</td>
                                <td>以分为单位 譬如返回 1 则余额等于 1/100 = 0.01</td>
                            </tr>
                            <tr>
                                <td>结算状态</td>
                                <td>status</td>
                                <td>Bit</td>
                                <td>1</td>
                                <td>1 为已经结算 0 为未结算</td>
                            </tr>
                            <tr>
                                <td>结算数据生成时间</td>
                                <td>createTime</td>
                                <td>Datetime</td>
                                <td>2019-1-29 12:00:00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>结算请求操作时间</td>
                                <td>createTime</td>
                                <td>Datetime</td>
                                <td>2019-1-29 13:00:00</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <h2 id="api4">[API]查询单个订单</h2>
                        <p>
                            URL地址：<?php echo url('/api.php?act=order&pid={商户ID}&key={商户密钥}&out_trade_no={商户订单号}', '', false, true); ?></p>
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
                                <td>操作类型</td>
                                <td>act</td>
                                <td>是</td>
                                <td>String</td>
                                <td>order</td>
                                <td>此API固定值</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>是</td>
                                <td>String</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>平台订单号</td>
                                <td>trade_no</td>
                                <td>可选</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td>trade_no 与 out_trade_no 任选其一</td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>可选</td>
                                <td>String</td>
                                <td>254545454545</td>
                                <td>trade_no 与 out_trade_no 任选其一</td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>返回信息</td>
                                <td>msg</td>
                                <td>String</td>
                                <td>查询订单号成功！</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>易支付订单号</td>
                                <td>trade_no</td>
                                <td>String</td>
                                <td>2016080622555342651</td>
                                <td><?php echo $webName; ?>订单号</td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td>商户系统内部的订单号</td>
                            </tr>
                            <tr>
                                <td>支付方式</td>
                                <td>type</td>
                                <td>String</td>
                                <td>alipay</td>
                                <td>alipay:支付宝,tenpay:财付通,<br/>qqpay:QQ钱包,wxpay:微信支付 bankpay：银联支付</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td>发起支付的商户ID</td>
                            </tr>
                            <tr>
                                <td>创建订单时间</td>
                                <td>addtime</td>
                                <td>String</td>
                                <td>2016-08-06 22:55:52</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>完成交易时间</td>
                                <td>endtime</td>
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
                                <td>商品金额</td>
                                <td>money</td>
                                <td>String</td>
                                <td>1.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>支付状态</td>
                                <td>status</td>
                                <td>Int</td>
                                <td>0</td>
                                <td>1为支付成功，0为未支付</td>
                            </tr>
                            </tbody>
                        </table>

                        <h2 id="api5">[API]批量查询订单</h2>
                        <p>
                            URL地址：<?php echo url('/api.php?act=orders&pid={商户ID}&key={商户密钥}', '', false, true); ?></p>
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
                                <td>操作类型</td>
                                <td>act</td>
                                <td>是</td>
                                <td>String</td>
                                <td>orders</td>
                                <td>此API固定值</td>
                            </tr>
                            <tr>
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户密钥</td>
                                <td>key</td>
                                <td>是</td>
                                <td>String</td>
                                <td>89unJUB8HZ54Hj7x4nUj56HN4nUzUJ8i</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>查询订单数量</td>
                                <td>limit</td>
                                <td>否</td>
                                <td>Int</td>
                                <td>20</td>
                                <td>返回的订单数量，最大50</td>
                            </tr>
                            <tr>
                                <td>页码</td>
                                <td>page</td>
                                <td>否</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>当前查询的页码</td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>返回信息</td>
                                <td>msg</td>
                                <td>String</td>
                                <td>查询结算记录成功！</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>订单列表</td>
                                <td>data</td>
                                <td>Array</td>
                                <td></td>
                                <td>订单列表</td>
                            </tr>
                            </tbody>
                        </table>
                        <p>返回订单列表数据结构：</p>
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
                                <td>pid</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td>发起支付的商户ID</td>
                            </tr>
                            <tr>
                                <td>订单金额</td>
                                <td>money</td>
                                <td>String</td>
                                <td>0.01</td>
                                <td>以分为单位 0.01</td>
                            </tr>
                            <tr>
                                <td>创建订单时间</td>
                                <td>addtime</td>
                                <td>String</td>
                                <td>2016-08-06 22:55:52</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>完成交易时间</td>
                                <td>endtime</td>
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
                        <h2 id="api6">[API]二维码下单接口</h2>
                        <p>此接口可用于服务器后端发起支付请求，会返回支付二维码链接</p>
                        <p style="color: red;">注意！！！如果需更换支付状态，需要重新发起新的商户订单号。</p>
                        <p>
                            URL地址：<?php echo url('/qrcode.php?pid={商户ID}&type={支付方式}&out_trade_no={商户订单号}& notify_url={服务器异步通知地址}&name={商品名称}&money={金额}&sign={签名字符串}&sign_type=MD5', '', false, true); ?></p>
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
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>支付方式</td>
                                <td>type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>alipay</td>
                                <td>qqpay:QQ钱包,wxpay:微信支付</td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>是</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>异步通知地址</td>
                                <td>notify_url</td>
                                <td>是</td>
                                <td>String</td>
                                <td><?php echo url('/notify_url.php', '', false, true); ?></td>
                                <td>服务器异步通知地址</td>
                            </tr>
                            <tr>
                                <td>商品名称</td>
                                <td>name</td>
                                <td>是</td>
                                <td>String</td>
                                <td>VIP会员</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商品金额</td>
                                <td>money</td>
                                <td>是</td>
                                <td>String</td>
                                <td>1.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>签名字符串</td>
                                <td>sign</td>
                                <td>是</td>
                                <td>String</td>
                                <td>202cb962ac59075b964b07152d234b70</td>
                                <td>
                                    签名算法与<a href="https://doc.open.alipay.com/docs/doc.htm?treeId=62&articleId=104741&docType=1"
                                            target="_blank">支付宝签名算法</a>相同
                                </td>
                            </tr>
                            <tr>
                                <td>签名类型</td>
                                <td>sign_type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>默认为MD5</td>
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
                                <td>返回状态码</td>
                                <td>code</td>
                                <td>Int</td>
                                <td>1</td>
                                <td>1为成功，其它值为失败</td>
                            </tr>
                            <tr>
                                <td>返回信息</td>
                                <td>msg</td>
                                <td>String</td>
                                <td>下单成功！</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>订单号</td>
                                <td>trade_no</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>二维码链接</td>
                                <td>code_url</td>
                                <td>String</td>
                                <td>weixin://wxpay/bizpayurl?pr=04IPMKM</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <hr/>

                        <h2 id="pay0">发起支付请求</h2>
                        <p style="color: red;">注意！！！如果需更换支付状态，需要重新发起新的商户订单号。</p>
                        <p>URL地址：<?php echo url('/submit.php', '', false, true); ?></p>
                        <p>
                            POST数据：pid={商户ID}&type={支付方式}&out_trade_no={商户订单号}&notify_url={服务器异步通知地址}&return_url={页面跳转通知地址}&name={商品名称}&money={金额}&sitename={网站名称}&sign={签名字符串}&sign_type=MD5</p>
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
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>支付方式</td>
                                <td>type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>alipay</td>
                                <td>alipay:支付宝,tenpay:财付通,<br/>qqpay:QQ钱包,wxpay:微信支付</td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>是</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>异步通知地址</td>
                                <td>notify_url</td>
                                <td>是</td>
                                <td>String</td>
                                <td><?php echo url('/notify_url.php', '', false, true); ?></td>
                                <td>服务器异步通知地址</td>
                            </tr>
                            <tr>
                                <td>跳转通知地址</td>
                                <td>return_url</td>
                                <td>是</td>
                                <td>String</td>
                                <td><?php echo url('/return_url.php', '', false, true); ?></td>
                                <td>页面跳转通知地址</td>
                            </tr>
                            <tr>
                                <td>商品名称</td>
                                <td>name</td>
                                <td>是</td>
                                <td>String</td>
                                <td>VIP会员</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商品金额</td>
                                <td>money</td>
                                <td>是</td>
                                <td>String</td>
                                <td>1.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>网站名称</td>
                                <td>sitename</td>
                                <td>否</td>
                                <td>String</td>
                                <td><?php echo $webName; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>签名字符串</td>
                                <td>sign</td>
                                <td>是</td>
                                <td>String</td>
                                <td>202cb962ac59075b964b07152d234b70</td>
                                <td>
                                    签名算法与<a href="https://doc.open.alipay.com/docs/doc.htm?treeId=62&articleId=104741&docType=1"
                                            target="_blank">支付宝签名算法</a>相同
                                </td>
                            </tr>
                            <tr>
                                <td>签名类型</td>
                                <td>sign_type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>默认为MD5</td>
                            </tr>
                            </tbody>
                        </table>

                        <h2 id="pay1">支付结果通知</h2>
                        <p>通知类型：服务器异步通知（notify_url）、页面跳转通知（return_url）</p>
                        <p>请求方式：GET</p>
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
                                <td>商户ID</td>
                                <td>pid</td>
                                <td>是</td>
                                <td>Int</td>
                                <td>1001</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>易支付订单号</td>
                                <td>trade_no</td>
                                <td>是</td>
                                <td>String</td>
                                <td>20160806151343349021</td>
                                <td><?php echo $webName; ?>订单号</td>
                            </tr>
                            <tr>
                                <td>商户订单号</td>
                                <td>out_trade_no</td>
                                <td>是</td>
                                <td>String</td>
                                <td>20160806151343349</td>
                                <td>商户系统内部的订单号</td>
                            </tr>
                            <tr>
                                <td>支付方式</td>
                                <td>type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>alipay</td>
                                <td>alipay:支付宝,tenpay:财付通,<br/>qqpay:QQ钱包,wxpay:微信支付,<br/>alipaycode:支付宝扫码,jdpay:京东支付 bankpay：银联支付
                                </td>
                            </tr>
                            <tr>
                                <td>商品名称</td>
                                <td>name</td>
                                <td>是</td>
                                <td>String</td>
                                <td>VIP会员</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>商品金额</td>
                                <td>money</td>
                                <td>是</td>
                                <td>String</td>
                                <td>1.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>支付状态</td>
                                <td>trade_status</td>
                                <td>是</td>
                                <td>String</td>
                                <td>TRADE_SUCCESS</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>签名字符串</td>
                                <td>sign</td>
                                <td>是</td>
                                <td>String</td>
                                <td>202cb962ac59075b964b07152d234b70</td>
                                <td>
                                    签名算法与<a href="https://doc.open.alipay.com/docs/doc.htm?treeId=62&articleId=104741&docType=1"
                                            target="_blank">支付宝签名算法</a>相同
                                </td>
                            </tr>
                            <tr>
                                <td>签名类型</td>
                                <td>sign_type</td>
                                <td>是</td>
                                <td>String</td>
                                <td>MD5</td>
                                <td>默认为MD5</td>
                            </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <h2 id="sdk0">SDK下载</h2>
                        <blockquote>
                            <a href="/static/sdk/PhpSDK-1.0.zip" style="color:blue">PhpSDK.zip</a><br/>
                            PHP 版本：V1.0
                        </blockquote>
                        <blockquote>
                            <a href="./JavaSDK-1.0.zip" style="color:blue">JavaSDK.zip</a><br/>
                            Java 版本：V1.0
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