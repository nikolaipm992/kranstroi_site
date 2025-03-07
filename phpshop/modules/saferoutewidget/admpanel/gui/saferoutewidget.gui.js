$().ready(function() {

    // ���������
    $('#link-activate-ddelivery').on('click', function(event) {
        event.preventDefault();
        var key = $(this).attr('data-key');
        if (key != "") {
            $.ajax({
                url: 'https://sdk.saferoute.ru/api/v1/integration/' + key + '/link.json',
                type: 'GET',
                dataType: 'html'
            });
            showAlertMessage('������� ����������� � Saferoute Widget');
        }
        else
            showAlertMessage('�� ������ API KEY', 'danger');
    });
});
