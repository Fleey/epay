<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $webName.' - 聚合支付后台'; ?></title>
    <?php include_once env('APP_PATH') . '/template/Admin/Head.php'; ?>
    <link rel="stylesheet" href="/static/css/user/style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include_once env('APP_PATH') . '/template/Admin/Sidebar.php'; ?>
        <main id="main" role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4"></main>
    </div>
</div>
<?php include_once env('APP_PATH') . '/template/Admin/Footer.php'; ?>
<script src="/static/js/admin/main.js"></script>
</body>
</html>