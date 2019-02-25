<script>
    var baseUrl = '<?php echo url('/', '', false, true);?>';
</script>
<script src="/static/js/resource/jquery.min.js"></script>
<script src="/static/js/resource/popper.min.js"></script>
<script src="/static/js/resource/bootstrap.min.js"></script>
<script src="/static/js/resource/sweetalert.min.js"></script>
<script src="/static/js/resource/feather.min.js"></script>
<script src="/static/js/resource/pace.min.js"></script>
<script src="/static/js/resource/jquery.dataTables.min.js"></script>
<script src="/static/js/resource/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ajaxStart(function () {
        Pace.restart();
    });
</script>
<script src="/static/js/ToolsFunction.js"></script>