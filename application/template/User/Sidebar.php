<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#"><?php echo $webName; ?> 聚合支付系统</a>

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
                    订单记录
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-href="Settles">
                    <i data-feather="inbox"></i>
                    结算记录
                </a>
            </li>
            <?php if ($isSettleApply) { ?>
                <li class="nav-item">
                    <a class="nav-link" data-href="SettleApply">
                        <i data-feather="inbox"></i>
                        申请结算
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link" data-href="UserInfo">
                    <i data-feather="user"></i>
                    个人信息
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/doc/v1" target="_blank">
                    <i data-feather="book-open"></i>
                    开发文档
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>帮助</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" data-href="Help">
                    <i data-feather="package"></i>
                    使用说明
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="http://qm.qq.com/cgi-bin/qm/qr?k=NKJzypWNotJIWbvQOO3fPxl42MuMu_Lk"
                   target="_blank">
                    <i data-feather="twitter"></i>
                    产品QQ群
                </a>
            </li>
        </ul>
    </div>
</nav>