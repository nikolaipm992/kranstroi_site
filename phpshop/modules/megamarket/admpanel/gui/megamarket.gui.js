$().ready(function () {

    // ������� ��� ���������
    $("body").on('change', "#categories_all", function () {
        if (this.checked)
            $('[name="categories[]"]').selectpicker('selectAll');
        else
            $('[name="categories[]"]').selectpicker('deselectAll');
    });
});