// ��������������� �������
var STATUS_EVENT = true;
var TABLE_EVENT = true;

$().ready(function () {

    if (typeof ($.cookie('data_length')) == 'undefined')
        var data_length = [10, 25, 50];
    else
        var data_length = [parseInt($.cookie('data_length')), 10, 25, 50];

    var table = $('#data').dataTable({
        "lengthMenu": data_length,
        "paging": true,
        "ordering": true,
        "info": false,
        "language": locale.dataTable,
        "aaSorting": [],
        "columnDefs": [
            {"orderable": false, "targets": 0}
        ],
        "fnDrawCallback": function () {

            // ��������� �� ������ dropdown
            $('.data-row').hover(
                    function () {
                        $(this).find('#dropdown_action').show();
                    },
                    function () {
                        $(this).find('#dropdown_action').hide();
                    });

            // Toggle
            $('.toggle-event').bootstrapToggle();

            // ���������� �� ������
            $(".data-row .manual").on('click', function (event) {
                event.preventDefault();
                window.open($(this).closest('.data-row').find('.modules-list > a').attr('data-wiki'));
            });

            // ������������� ������
            $('table#data tr').each(function (key, value) {
                if (key > 0) {
                    if ($(value).find('.install-date').html() != '') {
                        $(value).addClass('success');
                    }
                }
            });
        },

    });

    // �������� ������
    $('body').on('click', '.load-module', function () {
        var file = prompt('URL');
        if (file.length > 0) {

            var data = [];
            data.push({name: 'ajax', value: 1});
            data.push({name: 'url', value: file});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=modules.load&id=load',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(json['result']);
                    } else
                        showAlertMessage(json['result'], true, true);
                }
            });
        }
    });

    // ���������� �� �������� ������
    $(".select-action .off").on('click', function (event) {
        event.preventDefault();

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_off
        }).done(function () {

            var data = [];
            data.push({action: 0, ajax: 1, id: 'button'});
            $.ajax({
                mimeType: 'text/html; charset=' + locale.charset,
                url: '?path=modules.action&id=' + $.getUrlVar('id'),
                type: 'post',
                data: data,
                dataType: "html",
                async: false,
                success: function () {
                    window.location.href = '?path=modules';
                }
            });
        })
    });

    // ���������� � ���������� ��������
    $(".select-action .module-off-select").on('click', function (event) {
        event.preventDefault();

        if ($('input:checkbox:checked').length) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_off
            }).done(function () {

                $('input:checkbox:checked').each(function () {
                    var id = $(this).attr('data-id');
                    var parent = $(this).closest('.data-row');

                    // ������ ����� ���������
                    this.checked = !this.checked && !this.disabled;

                    $('.list_edit_' + id).append('<input type="hidden" name="action" value="0">');
                    $('.list_edit_' + id).ajaxSubmit({
                        success: function (json) {
                            var data = $.parseJSON(json);

                            parent.removeClass('success');
                            parent.find('.install-date').html(data['date']);

                            // �������
                            var count = $('#mod-install-count').html();
                            if (count > 0)
                                count--;
                            $('#mod-install-count').html(count);

                            // ������� ����
                            $('#modules-menu').find('a[href="' + parent.find('.modules-list>a').attr('href') + '"]').parent('li').empty();

                            // ������ ����
                            parent.find('#dropdown_status_' + id).html(locale.off);
                            parent.find('.toggle-event').bootstrapToggle('back');

                            // ����� ����
                            parent.find('.dropdown-menu .off').html('�������� <span class="glyphicon glyphicon-play"></span>');
                            parent.find('.dropdown-menu .off').attr('class', 'on');

                            // ���� ��������� ��������
                            parent.find('.dropdown-menu .option').empty();

                            showAlertMessage(locale.module_done);
                        }
                    });
                });
            })
        } else
            alert(locale.select_no);
    });

    // ��������� � ���������� ��������
    $(".select-action .module-on-select").on('click', function (event) {
        event.preventDefault();

        if ($('input:checkbox:checked').length) {

            $.MessageBox({
                buttonDone: "OK",
                buttonFail: locale.cancel,
                message: locale.confirm_on
            }).done(function () {

                $('input:checkbox:checked').each(function () {
                    var id = $(this).attr('data-id');
                    var parent = $(this).closest('.data-row');

                    // ������ ����� ���������
                    this.checked = !this.checked && !this.disabled;

                    $('.list_edit_' + id).append('<input type="hidden" name="action" value="1">');
                    $('.list_edit_' + id).ajaxSubmit({
                        success: function (json) {
                            var data = $.parseJSON(json);

                            parent.addClass('success');
                            parent.find('.install-date').html(data['date']);

                            // �������
                            var count = $('#mod-install-count').html();
                            count++;
                            $('#mod-install-count').html(count);

                            // ������� ����
                            $('#modules-menu').append('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">' + parent.find('.modules-list>a').html().split(/.\d+/).join('') + '</a></li>');

                            // ������ ����
                            parent.find('#dropdown_status_' + id).html(locale.on);
                            parent.find('.toggle-event').bootstrapToggle('back');

                            // ������ ����
                            parent.find('.dropdown-menu .on').html('��������� <span class="glyphicon glyphicon-stop"></span>');
                            parent.find('.dropdown-menu .on').attr('class', 'off');

                            // ���� ��������� ����������
                            parent.find('.dropdown-menu .divider').before('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">���������</a></li>');
                            showAlertMessage(locale.module_done);
                        }
                    });
                });
            })

        } else
            alert(locale.select_no);
    });

    // ������� ��������� ������� ������
    $('body').on('change', '.toggle-event', function () {

        var id = $(this).attr('data-id');
        var element = $(this);

        if ($(this).prop('checked') === true)
            var val = 1;
        else
            var val = 0;

        var caption = $(this).html();
        var parent = $(this).closest('.data-row');
        var message;

        if (val == 0)
            message = locale.confirm_off;
        else
            message = locale.confirm_on;

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: message
        }).fail(function () {
            element.bootstrapToggle('back');
        }).done(function () {

            $('.status_edit_' + id).append('<input type="hidden" name="action" value="' + val + '">');
            $('.status_edit_' + id).ajaxSubmit({
                success: function (json) {
                    var data = $.parseJSON(json);

                    $("#dropdown_status_" + id).html(caption);
                    showAlertMessage(locale.module_done);

                    var count = $('#mod-install-count').html();
                    if (val > 0)
                        count++;
                    else
                        count--;

                    $('#mod-install-count').html(count);

                    parent.toggleClass('success');
                    parent.find('.install-date').html(data['date']);

                    // ����������/�������� ���� ������ � �������� ����
                    if (val == 1) {
                        $('#modules-menu').append('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">' + parent.find('.modules-list>a').html().split(/.\d+/).join('') + ' <span class="text-muted glyphicon glyphicon-star"></span></a></li>');

                        parent.find('.dropdown-menu .on').html('��������� <span class="glyphicon glyphicon-stop"></span>');
                        parent.find('.dropdown-menu .on').attr('class', 'off');

                        // ���� ��������� ����������
                        parent.find('.dropdown-menu .divider').before('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">���������</a></li>');
                    } else {
                        $('#modules-menu').find('a[href="' + parent.find('.modules-list>a').attr('href') + '"]').parent('li').empty();

                        parent.find('.dropdown-menu .off').html('�������� <span class="glyphicon glyphicon-play"></span>');
                        parent.find('.dropdown-menu .off').attr('class', 'on');

                        // ���� ��������� ��������
                        parent.find('.dropdown-menu .option').empty();
                    }

                }
            });
        });
    });

    // ���������� �������� �� ������ dropmenu
    $("body").on('click', ".data-row #dropdown_action .on", function (event) {
        event.preventDefault();
        var parent = $(this).closest('.data-row');
        var id = $(this);
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_on
        }).done(function () {

            $('.list_edit_' + data_id).append('<input type="hidden" name="action" value="1">');
            $('.list_edit_' + data_id).ajaxSubmit({
                success: function (json) {

                    var data = $.parseJSON(json);

                    if (data['success'] == 1) {

                        parent.toggleClass('success');

                        id.html('��������� <span class="glyphicon glyphicon-stop"></span>');
                        id.attr('class', 'off');

                        var count = $('#mod-install-count').html();

                        count++;
                        $('#mod-install-count').html(count);

                        parent.find('.install-date').html(data['date']);

                        // ���� ��������� ����������
                        id.closest('ul').find('.divider').before('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">���������</a></li>');
                        // ������� ����
                        $('#modules-menu').append('<li><a href="' + parent.find('.modules-list>a').attr('href') + '">' + parent.find('.modules-list>a').html().split(/.\d+/).join('') + ' <span class="text-muted glyphicon glyphicon-star"></span></a></li>');

                        // ������ ����
                        parent.find('#dropdown_status_' + id.attr('data-id')).html('���.');
                        parent.find('.toggle-event').bootstrapToggle('back');

                        showAlertMessage(locale.module_done);
                    } else
                        showAlertMessage(locale.save_false, true);
                }
            });
        })

    });

    $("body").on('click', ".data-row #dropdown_action .off", function (event) {
        event.preventDefault();
        var parent = $(this).closest('.data-row');
        var id = $(this);
        var data_id = $(this).attr('data-id');

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: locale.confirm_on
        }).done(function () {

            $('.list_edit_' + data_id).append('<input type="hidden" name="action" value="0">');
            $('.list_edit_' + data_id).ajaxSubmit({
                success: function (json) {
                    parent.toggleClass('success');

                    id.html('�������� <span class="glyphicon glyphicon-play"></span>');
                    id.attr('class', 'on');

                    var count = $('#mod-install-count').html();
                    var data = $.parseJSON(json);
                    count--;
                    $('#mod-install-count').html(count);

                    parent.find('.install-date').html(data['date']);

                    // ���� ��������� ��������
                    id.closest('ul').find('.option').empty();

                    // ������� ����
                    $('#modules-menu').find('a[href="' + parent.find('.modules-list>a').attr('href') + '"]').parent('li').empty();

                    // ������ ����
                    parent.find('#dropdown_status_' + id.attr('data-id')).html('<span class="text-muted">����</span>');
                    parent.find('.toggle-event').bootstrapToggle('back');

                    showAlertMessage(locale.module_done);
                }
            });
        })


    });

    // ������ ���������� ����
    $(".data-row .dropdown-menu .off, .select-action .off").append(' <span class="glyphicon glyphicon-stop"></span>');
    $(".data-row .dropdown-menu .on").append(' <span class="glyphicon glyphicon-play"></span>');

    // ������������� ������
    $('table#data tr').each(function (key, value) {
        if (key > 0) {
            if ($(value).find('.install-date').html() != '') {
                $(value).addClass('success');
            }
        }
    });

    // ������ �� ������ ��� ����������
    $("body").on('click', ".modules-list > a", function (event) {
        event.preventDefault();
        if ($(this).closest('.data-row').find('.install-date').html() == '') {
            if ($(this).attr('data-wiki') != "")
                window.open($(this).attr('data-wiki'));
        } else
            window.location.href = $(this).attr('href');
    });

    // ��������� �� ������
    $(".data-row .option").on('click', function (event) {
        event.preventDefault();
        window.location.href = $(this).closest('.data-row').find('.modules-list > a').attr('href');
    });

    // ��������� ������� ���������
    if (typeof modcat != 'undefined')
        $('.treegrid-' + modcat).addClass('treegrid-active');

    // ������ ���������
    if (typeof (TREEGRID_LOAD) != 'undefined')
        $('.tree').treegrid({
            saveState: true,
            expanderExpandedClass: 'glyphicon glyphicon-triangle-bottom',
            expanderCollapsedClass: 'glyphicon glyphicon-triangle-right'
        });
});