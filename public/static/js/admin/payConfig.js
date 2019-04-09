$(function () {
    $('div[data-config-name]').each(function (key, value) {
        var dataDom = $(value);
        var configName = dataDom.attr('data-config-name');
        $.getJSON('/cy2018/api/Config', {keyName: configName}, function (data) {
            if (data['status'] === 1) {
                $.each(data['data'], function (keyName, configValue) {
                    if (typeof configValue === "boolean") {
                        configValue = configValue ? '1' : '0';
                    }
                    dataDom.find('[data-name="' + keyName + '"]').val(configValue);
                });
                dataDom.find('div[data-api-type="' + dataDom.find('[data-name="apiType"]').val() + '"]').show();
            }
        });
    });
    $('div[data-config-name] select[data-name="apiType"]').change(function () {
        var parentDom = $(this).parent().parent();
        parentDom.find('[data-api-type]').hide();
        parentDom.find('[data-api-type="' + $(this).val() + '"]').show();
    });
    $('div[data-config-name] button[data-save]').off("click").on('click', function () {
        var buttonDom = $(this);
        var configName = buttonDom.parent().attr('data-config-name');
        var configData = {};
        buttonDom.parent().find('[data-name]').each(function (key, value) {
            var dom = $(value);
            var keyName = dom.attr('data-name');
            var configValue = dom.val();
            if (keyName !== 'apiType' && keyName !== 'epayCenterUid') {
                if (configValue === '0' || configValue === '1') {
                    configValue = configValue !== '0';
                }
            }
            configData[keyName] = configValue;
        });
        $.post('/cy2018/api/Config', {
            keyName: configName,
            isArray: true,
            data: configData
        }, function (data) {
            if (data['status'] === 0) {
                swal({
                    title: '',
                    text: data['msg'],
                    showConfirmButton: false,
                    timer: 1500,
                    type: 'warning'
                });
                return true;
            }
            swal({
                title: '',
                text: data['msg'],
                showConfirmButton: false,
                timer: 1500,
                type: 'success'
            });
        }, 'json');
    });
});