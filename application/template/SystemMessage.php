<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>站点提示信息</title>
    <style type="text/css">
        html {
            background: #eee
        }
        body {
            background: #fff;
            color: #333;
            font-family: "微软雅黑", "Microsoft YaHei", sans-serif;
            margin: 2em auto;
            padding: 1em 2em;
            max-width: 700px;
            -webkit-box-shadow: 10px 10px 10px rgba(0, 0, 0, .13);
            box-shadow: 10px 10px 10px rgba(0, 0, 0, .13);
            opacity: .8
        }
        #error-page {
            margin-top: 50px
        }
        h3 {
            text-align: center
        }
    </style>
</head>
<body id="error-page">
<h3>站点提示信息</h3>
<?php echo $msg; ?>
</body>
</html>