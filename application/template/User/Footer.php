<script>
    var baseUrl = '<?php echo url('/', '', false, true);?>';
</script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://cdn.bootcss.com/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.bootcss.com/feather-icons/4.10.0/feather.min.js"></script>
<script src="https://cdn.bootcss.com/pace/1.0.2/pace.min.js"></script>
<script>
    $(document).ajaxStart(function () {
        Pace.restart();
    });
</script>
<script src="/static/js/ToolsFunction.js"></script>