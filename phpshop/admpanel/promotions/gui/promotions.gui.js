$().ready(function() {
    
    $.fn.datetimepicker.dates['ru'] = locale;

    // ������� ��� ���������
    $('body').on('change', '#categories_all', function () {
        if (this.checked)
            $('[name="categories[]"]').selectpicker('selectAll');
        else
            $('[name="categories[]"]').selectpicker('deselectAll');
    });

    // datetimepicker
    $(".date").datetimepicker({
        format: 'dd-mm-yyyy',
        language: 'ru',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });

    // ������� ID ������ � ���� ���� - �����
    $("body").on('click', "#selectModal .search-action", function(event) {
        event.preventDefault();

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search&words=' + escape($('input:text[name=search_name]').val()) + '&cat=' + $('select[name=search_category]').val() + '&price_start=' + $('input:text[name=search_price_start]').val() + '&price_end=' + $('input:text[name=search_price_end]').val(),
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function(data) {
                $('#selectModal .modal-body').html(data);

            }

        });
    });

    // ������� ID ������ � ���� ����  -  2 ���
    $("body").on('click', "#selectModal .modal-footer .id-add-send", function(event) {
        event.preventDefault();

        $('.search-list input:checkbox:checked').each(function() {
            var id = $(this).attr('data-id');
            if ($('#products_new').tagExist(id))
            {
                this.disabled;
            }
            else
                $(selectTarget).addTag(id);
        });

        $('#selectModal').modal('hide');
    });

    // ����� �������� �� ����� � ��������� ���� ������� ������
    $('body').on('click', ".search-list  td", function() {
        $(this).parent('tr').find('input:checkbox[name=items]').each(function() {
            this.checked = !this.checked && !this.disabled;
        });
    });

    // ������� ID ������ � ���� ����  - 1 ���
    $(".tag-search").on('click', function(event) {
        event.preventDefault();

        selectTarget = $(this).attr('data-target');

        var data = [];
        data.push({name: 'selectID', value: 2});
        data.push({name: 'ajax', value: 1});
        data.push({name: 'currentID', value: $(selectTarget).val()});
        data.push({name: 'actionList[selectID]', value: 'actionSearch'});

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '?path=catalog.search',
            type: 'post',
            data: data,
            dataType: "html",
            async: false,
            success: function(data) {
                //$('#selectModal .modal-dialog').removeClass('modal-lg');
                $('#selectModal .modal-title').html(locale.add_cart_value);
                $('#selectModal .modal-footer .btn-primary').removeClass('edit-select-send');
                $('#selectModal .modal-footer .btn-primary').addClass('id-add-send');
                $('#selectModal .modal-footer .btn-delete').addClass('hidden');
                $('#selectModal .modal-body').css('max-height', ($(window).height() - 200) + 'px');
                $('#selectModal .modal-body').css('overflow-y', 'auto');
                $('#selectModal .modal-body').html(data);

                $(".search-list td input:checkbox").each(function() {
                    this.checked = true;
                });

                $('#selectModal').modal('show');
            }
        });
    });

    $('#products_new').tagsInput({
        'height': '100px',
        'width': '100%',
        'interactive': true,
        'defaultText': locale.enter,
        'removeWithBackspace': true,
        'minChars': 0,
        'maxChars': 0,
        'placeholderColor': '#666666'
    });

});