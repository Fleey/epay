<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $webName . ' - 聚合支付系统'; ?></title>
    <link rel="stylesheet" href="/static/css/resource/sweetalert.min.css">
    <link rel="stylesheet" href="/static/css/resource/style.css">
    <link rel="stylesheet" href="/static/css/resource/prism.css">
    <link rel="stylesheet" href="/static/css/user/style.css">
</head>
<body>
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>
<div id="main-wrapper">
    <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-header border-right">
                <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
                <a class="navbar-brand" href="index.html">
                    <!-- Logo icon -->
                    <b class="logo-icon">
                        <img src="/static/images/logo-icon.png" alt="homepage" class="dark-logo"/>
                    </b>
                    <span class="logo-text">
                        <span style="color: #0f0f0f;"><?php echo $webName; ?>聚合支付平台</span>
                    </span>
                </a>
                <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                   data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                   aria-expanded="false" aria-label="Toggle navigation"></a>
            </div>
            <div class="navbar-collapse collapse" id="navbarSupportedContent">
                <ul class="navbar-nav float-left mr-auto">
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)"
                           data-sidebartype="mini-sidebar"><i
                                    class="mdi mdi-menu font-18"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <?php include_once env('APP_PATH') . '/template/User/Sidebar.php'; ?>
    <div class="page-wrapper" style="display: block;">
        <div class="page-content container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            页面数据加载中。。。
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/js/resource/jquery.min.js"></script>
<script src="/static/js/resource/popper.min.js"></script>
<script src="/static/js/resource/bootstrap.min.js"></script>

<script src="/static/js/resource/app.min.js"></script>
<script src="/static/js/resource/app.init.js"></script>

<script src="/static/js/resource/sweetalert.min.js"></script>
<script src="/static/js/resource/perfect-scrollbar.jquery.min.js"></script>
<script src="/static/js/resource/sparkline.js"></script>
<script src="/static/js/resource/waves.js"></script>
<script src="/static/js/resource/sidebarmenu.js"></script>
<script src="/static/js/resource/custom.min.js"></script>
<script src="/static/js/resource/PrismJS.js"></script>
<script>var baseUrl = '/';</script>
<script src="/static/js/ToolsFunction.js"></script>
<script src="/static/js/user/main.js"></script>
</body>
</html>
