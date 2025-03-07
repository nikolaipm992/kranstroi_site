// Переопределение функции
var TABLE_EVENT = true;

$(document).ready(function () {

    // Запуск восстановления бекапа
    $("#dropdown_action .restore").on('click', function (event) {
        event.preventDefault();
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_restore + ' PHPShop ' + $(this).attr('data-id') + '?'
        }).done(function () {
            window.location.href = '?path=update.restore&version=' + data_id;
        })


    });

    // Восстановление БД
    if ($('.install-restore-bd').length) {
        var data = [];
        data.push({name: 'lfile', value: '/phpshop/admpanel/dumper/backup/restore.sql'});
        data.push({name: 'saveID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'clean', value: 1});
        data.push({name: 'actionList[saveID]', value: 'actionSave'});
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=exchange.sql',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == 1) {
                    $('.install-restore-bd').toggleClass('hide');
                    $('.navbar-action .navbar-brand').append(' - ' + locale.done);
                } else {
                    $('.install-restore-bd').toggleClass('hide');
                    $('.install-restore-bd-danger').toggleClass('hide');
                    $('.install-restore-bd-danger').html('<strong>' + locale.backup_false + '</strong><br>' + json['error']);

                }
            }

        });
    }


    // Переход в журнал из списка бекапов
    $("#dropdown_action .log").on('click', function (event) {
        event.preventDefault();
        window.open('https://www.phpshop.ru/docs/update.html#EE' + $(this).attr('data-id'));
    });


    // Обновление БД
    if ($('.install-update-bd').length) {
        var data = [];
        data.push({name: 'lfile', value: '/phpshop/admpanel/dumper/backup/update.sql'});
        data.push({name: 'saveID', value: 1});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'clean', value: 1});
        data.push({name: 'actionList[saveID]', value: 'actionSave'});
        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '?path=exchange.sql',
            type: 'post',
            data: data,
            dataType: "json",
            async: false,
            success: function (json) {
                if (json['success'] == 1) {
                    $('.install-update-bd').toggleClass('hide');
                    $('.navbar-action .navbar-brand').append(' - ' + locale.done);
                } else {
                    $('.install-update-bd').toggleClass('hide');
                    $('.install-update-bd-danger').toggleClass('hide');
                    $('.install-update-bd-danger').html('<strong>' + locale.backup_false + '</strong><br>' + json['error']);
                }
            }

        });
    }


    // Запуск обновления
    $(".navbar-action .update-start").on('click', function (event) {
        event.preventDefault();

        $('.progress').toggleClass('hide');
        $('#product_edit').append('<input type="hidden" name="saveID" value="1">');
        $('#product_edit').submit();
    });


    if (typeof ($.cookie('data_length')) == 'undefined')
        var data_length = [10, 25, 50, 75, 100, 500, 1000];
    else
        var data_length = [parseInt($.cookie('data_length')), 10, 25, 50, 75, 100, 500, 1000];

    var table = $('#data').dataTable({
        "lengthMenu": data_length,
        "paging": true,
        "ordering": false,
        "info": false,
        "language": locale.dataTable,
        "aaSorting": [],
        "columnDefs": [
            {"orderable": false, "targets": 0}
        ]

    });

});