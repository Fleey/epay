<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" data-href="Dashboard"
                       aria-expanded="false">
                        <i class="mdi mdi-av-timer"></i>
                        <span class="hide-menu">仪表盘</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" data-href="Orders"
                       aria-expanded="false">
                        <i class="mdi mdi-file-document-box"></i>
                        <span class="hide-menu">订单记录</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" data-href="Settles"
                       aria-expanded="false">
                        <i class="mdi mdi-credit-card-multiple"></i>
                        <span class="hide-menu">结算记录</span>
                    </a>
                </li>
                <?php if ($isSettleApply) { ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" data-href="SettleApply"
                           aria-expanded="false">
                            <i class="ti-export"></i>
                            <span class="hide-menu">申请结算</span>
                        </a>
                    </li>
                <?php } ?>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" data-href="UserInfo"
                       aria-expanded="false">
                        <i class="mdi mdi-contacts"></i>
                        <span class="hide-menu">个人信息</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" target="_blank" href="https://documenter.getpostman.com/view/5333712/S1EQTdcL"
                       aria-expanded="false">
                        <i class="mdi mdi-book-open-page-variant"></i>
                        <span class="hide-menu">开发文档</span>
                    </a>
                </li>
                <div class="devider"></div>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" data-href="Help"
                       aria-expanded="false">
                        <i class="mdi mdi-tooltip-text"></i>
                        <span class="hide-menu">使用说明</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" target="_blank"
                       href="http://qm.qq.com/cgi-bin/qm/qr?k=NKJzypWNotJIWbvQOO3fPxl42MuMu_Lk"
                       aria-expanded="false">
                        <i class="mdi mdi-qqchat"></i>
                        <span class="hide-menu">产品QQ群</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link exit" href="javascript:void(0);"
                       aria-expanded="false">
                        <i class="fa fa-power-off"></i>
                        <span class="hide-menu">注销账户</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>