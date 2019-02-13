<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#"><?php echo $webName; ?> 聚合支付后台</a>

    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link exit" href="javascript:void();">注销账号</a>
        </li>
    </ul>
</nav>
<!--header-->
<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" data-href="Dashboard">
                    <i data-feather="home"></i>
                    仪表盘 <span class="sr-only">(current)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="Orders">
                    <i data-feather="file-text"></i>
                    订单管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="UserInfo">
                    <i data-feather="users"></i>
                    商户管理
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="SettleList">
                    <i data-feather="inbox"></i>
                    结算记录
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="SettleInfo">
                    <i data-feather="inbox"></i>
                    结算操作
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="PayConfig">
                    <i data-feather="settings"></i>
                    支付接口配置
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="ServerConfig">
                    <i data-feather="settings"></i>
                    网站配置
                </a>
            </li>
        </ul>

    </div>
</nav>