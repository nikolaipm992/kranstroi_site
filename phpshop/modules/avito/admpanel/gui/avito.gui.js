$().ready(function () {
    
    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'dd-mm-yyyy',
            weekStart: 1,
            language: 'ru',
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }

    // ����� ������
    $(".btn-order-search").on('click', function () {
        $('#order_search').submit();
    });

    // ����� ������ - �������
    $(".btn-order-cancel").on('click', function () {
        window.location.replace('?path=modules.dir.avito.orders');
    });

    // ������� ��� ���������
    $("body").on('change', "#categories_all", function () {
        if (this.checked)
            $('[name="categories[]"]').selectpicker('selectAll');
        else
            $('[name="categories[]"]').selectpicker('deselectAll');
    });
});
