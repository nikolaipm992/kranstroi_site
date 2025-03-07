$().ready(function () {

    $('[data-toggle="popover-icon"]').popover({
        "trigger": "hover",
        "html": true
    });

    $("body").on('click', ".template-map .nav a", function (event) {
        //event.preventDefault();
    });

    $('[data-toggle="popover"]').popover(
            {
                template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
                placement: 'auto',
                html: true
            }
    );

    $('[data-toggle="popover"]').css('cursor', 'pointer').attr('title', locale.help);
    $('.panel[data-toggle="popover"]').hover(
            function () {
                $(this).toggleClass('panel-primary text-primary').css('box-shadow', '0 0 6px rgba(122,122,122,0.2)');
            }, function () {
        $(this).toggleClass('panel-primary text-primary').css('box-shadow', 'none');
    });
    $('.template-image[data-toggle="popover"]').hover(
            function () {
                $(this).css('box-shadow', '0 0 6px rgba(122,122,122,0.2)');
            }, function () {
        $(this).css('box-shadow', 'none');
    });

    // ��������� ���� ������� �������� ����������
    $('#selectModal').on('show.bs.modal', function (event) {
        $('#selectModal .modal-title').html(locale.templater_table_title + $('[data-target="#selectModal"]').attr('data-title'));
        $('#selectModal .modal-footer .btn-primary').addClass('hidden');
        $('#selectModal .modal-footer [data-dismiss="modal"]').html(locale.close);
        $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
        $('#selectModal .modal-body').css('overflow-y', 'auto');
    });

    // ���� ��������� ������
    $('body').on('click', '.skin-serial', function () {
        var serial = prompt('����', $(this).attr('data-key'));
        var parent = $(this).closest('.panel');
        if (serial) {

            var data = [];
            data.push({name: 'ajax', value: 1});
            data.push({name: 'path', value: $(this).attr('data-path')});
            data.push({name: 'key_new', value: serial});
            data.push({name: 'editID', value: 1});
            data.push({name: 'actionList[editID]', value: 'actionSerial.system.edit'});
            $.ajax({
                mimeType: 'text/html; charset='+locale.charset,
                url: '?path=tpleditor',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(json['result']);
                        parent.removeClass('panel-warning');
                        parent.addClass('panel-success');
                        parent.find('[data-toggle="tooltip"]').hide();
                        parent.find('.active').removeClass('hide');

                    } else {
                        showAlertMessage(json['result'], true, true);
                        parent.removeClass('panel-success');
                        parent.addClass('panel-warning');
                    }
                }
            });
        }
    });

    // ����������� �������
    $('.skin-load').on('click', function () {

        var data = [];
        var id = $(this);
        var path = $(this).attr('data-path');
        var parent = $(this).closest('.panel');
        var type = $(this).attr('data-type');

        if ($(this).hasClass('skin-reload')) {
            var message = locale.confirm_reload_skin;
        } else
            var message = locale.confirm_load_skin;

        $.MessageBox({
            buttonDone: "OK",
            buttonFail: locale.cancel,
            message: message
        }).done(function () {
            id.tooltip('toggle');
            parent.find('.panel-heading').append(' - �����������...');
            //id.addClass('glyphicon glyphicon-save');
            data.push({name: 'template_load', value: path});
            data.push({name: 'template_type', value: type});
            data.push({name: 'editID', value: 1});
            data.push({name: 'ajax', value: 1});
            data.push({name: 'actionList[editID]', value: 'actionLoad.system.edit'});
            $.ajax({
                mimeType: 'text/html; charset='+locale.charset,
                url: '?path=tpleditor',
                type: 'post',
                data: data,
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json['success'] == 1) {
                        showAlertMessage(json['result']);
                        parent.addClass('panel-success');
                        id.tooltip('hide');
                        id.removeClass('btn-warning').addClass('btn-default');
                        //id.remove();
                        parent.find('.panel-heading').html(path);
                        $('#template-tree').append('<tr class="treegrid-all"><td><a href="?path=tpleditor&name=' + path + '">' + path + '</a></td></tr>');
                        parent.find('.panel-footer').find('.btn').removeClass('hide');

                    } else {
                        showAlertMessage(json['result'], true);
                        parent.addClass('panel-warning');
                        if (confirm(locale.confirm_load_template)) {
                            window.open('http://' + json['zip']);
                        }
                    }
                }
            });

        })
    });



    // ����������� ���������
    if ($('#fix-check:visible').length && typeof (WAYPOINT_LOAD) != 'undefined')
        var waypoint = new Waypoint({
            element: document.getElementById('fix-check'),
            handler: function (direction) {
                $('.navbar-action').toggleClass('navbar-fixed-top');
            }
        });

    // Ace
    if ($('#editor_src').length) {
        var editor = ace.edit("editor");
        var mod = $('#editor_src').attr('data-mod');
        var theme = $('#editor_src').attr('data-theme');
        editor.setTheme("ace/theme/" + theme);
        editor.session.setMode("ace/mode/" + mod);
        editor.setValue($('#editor_src').val(), 1);
        editor.getSession().setUseWrapMode(true);
        editor.setShowPrintMargin(false);
        editor.setAutoScrollEditorIntoView(true);
        //$('#editor').height($('.tree').height() - $('#editor').offset().top + 100);
        $('#editor').height($(window).height());
        editor.resize();

        // �����
        if ($.getUrlVar('search') !== undefined) {
            editor.find('@' + $.getUrlVar('search') + '@');
            editor.clearSelection();
        }
    }

    // �������� @VAR@ � Ace
    $("body").on('click', ".editor_var", function () {

        // ��������
        if ($(this).hasClass('btn-info')) {
            editor.insert($(this).attr('data-insert'));
            $(this).removeClass('btn-info');
            $(this).addClass('btn-default');
            $(this).find('.glyphicon').addClass('glyphicon-tag');
        }
        // �����
        else {
            editor.find($(this).attr('data-insert'));
            editor.clearSelection();
        }
    });

    // ��������� Ace
    $(".ace-full").on('click', function () {
        $(this).find('span').toggleClass('glyphicon-fullscreen');
        if ($('#editor').css('position') == 'relative') {
            $('#editor').css('position', 'fixed');
        } else {
            $('#editor').css('position', 'relative');
        }
    });

    // ��������� Ace [escape key]
    $(document).keyup(function (e) {
        if (e.keyCode == 27) {
            if ($('#editor').css('position') == 'fixed') {
                $('.glyphicon-resize-small').toggleClass('glyphicon-fullscreen');
                $('#editor').css('position', 'relativee');
            }
        }
    });


    // ��������� Ace
    $(".ace-save").on('click', function () {
        $('#editor_src').val(editor.getValue());
    });

    // ���������� ������� ���������
    $('.title-icon .glyphicon-chevron-down').on('click', function () {
        $('.tree').treegrid('expandAll');
    });

    $('.title-icon .glyphicon-chevron-up').on('click', function () {
        $('.tree').treegrid('collapseAll');
    });

    // ������ ���������
    $('.tree').treegrid({
        saveState: true,
        expanderExpandedClass: 'glyphicon glyphicon-triangle-bottom',
        expanderCollapsedClass: 'glyphicon glyphicon-triangle-right'
    });

    // ��������� ���������
    $(".treegrid-parent").on('click', function (event) {
        event.preventDefault();
        $('.' + $(this).attr('data-parent')).treegrid('toggle');
    });

});