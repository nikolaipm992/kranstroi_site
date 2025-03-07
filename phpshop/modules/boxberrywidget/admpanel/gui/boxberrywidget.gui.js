function getPVZ() {
    var api_token = $('input[name=api_key_new]').val();
    boxberry.open('setPVZ', api_token, '', '', '', '', '', '', '', '');
}
function setPVZ(result) {
    $('input[name="pvz_id_new"]').val(result.id);
}

function boxberrywidgetStart() {
    var api_token = $('#boxberryApiKey').val();
    var city = $('#boxberryCity').val();
    var weight = $('#boxberryCartWeight').val();
    var cartSum = $('#OrderSumma').val();
    var depth = $('#boxberryCartDepth').val();
    var height = $('#boxberryCartHeight').val();
    var width = $('#boxberryCartWidth').val();
    boxberry.open('boxberryWidget', api_token, city, '', cartSum, weight, 0, height, width, depth);
}

function boxberryWidget(result) {

    var info = locale.cdek.pickup_code + ': ' + result.id + ', ' + locale.cdek.city + ' ' + result.name + ', ' + locale.cdek.pickup_address + ' ' + result.address + ', ' + locale.cdek.pickup_phone + ' ' + result.phone;

    $.ajax({
        mimeType: 'text/html; charset=' + locale.charset,
        url: '/phpshop/modules/boxberrywidget/ajax/ajax.php',
        type: 'post',
        data: {
            cost: result.price,
            operation: 'changeAddress',
            pvz: result.id,
            orderId: $('input[name="boxberry_order_id"]').val(),

            info: info
        },
        dataType: "json",
        async: false,
        success: function (json) {
            if (json['success']) {
                location.reload();
            } else {
                console.log(json['error'])
            }
        }
    });

}


$(document).ready(function () {
    $('body').on('change', 'input[name="boxberry_payment_status"]', function () {
        var value;

        if ($(this).prop('checked') === true) {
            value = 1;
        } else
            value = 0;

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: '../modules/boxberrywidget/ajax/ajax.php',
            type: 'post',
            data: {
                operation: 'changePaymentStatus',
                value: value,
                orderId: $('input[name="boxberry_order_id"]').val()
            },
            dataType: "json",
            async: false,
            success: function (json) {
                console.log(json['error']);
            }
        });
    });

    $('.boxberry-change-address').on('click', function () {
        boxberrywidgetStart();
    });

});