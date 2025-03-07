$(document).ready(function () {
    $('.sberbank-refund').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '/phpshop/modules/sberbankrf/ajax/ajax.php',
            type: 'post',
            data: {operation: 'refund', orderId: $('input[name="sberbank_order_id"]').val()},
            dataType: "json",
            async: false,
            success: function(json) {
                if(json['success']) {
                    $.MessageBox({
                        buttonDone: false,
                        buttonFail: locale.close,
                        input: false,
                        message: json['message']
                    });
                } else {
                    $.MessageBox({
                        buttonDone: false,
                        buttonFail: locale.close,
                        input: false,
                        message: json['error']
                    });
                }
            }

        });
    });
});
