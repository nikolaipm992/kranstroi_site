function boxberrywidgetStart() {
    $("#makeyourchoise").val(null);
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

    var info = locale.cdek.pickup_code +': ' + result.id + ', '+locale.cdek.city +' ' + result.name + ', '+locale.cdek.pickup_address+' ' + result.address + ', '+locale.cdek.pickup_phone+' ' + result.phone;
    
    var boxberry_sum = Number(result.price);
    if ($("#d").data('free') === 1) {
        boxberry_sum = 0;
    } else {
        var boxberryFee = Number($('#boxberryFee').val());
        if (boxberryFee > 0) {
            if (Number($('#boxberryFeeType').val()) == 1) {
                boxberry_sum = boxberry_sum + (boxberry_sum * boxberryFee / 100);
            } else {
                boxberry_sum = boxberry_sum + boxberryFee;
            }
        }
        boxberry_sum = Number(boxberry_sum.toFixed(Number($('#boxberryPriceFormat').val())));
    }

    $('input[name="boxberryInfo"]').val(info);
    $('input[name="boxberry_pvz_id_new"]').val(result.id);
    $('input[name="DeliverySum"]').val(boxberry_sum);

    $("#DosSumma").html(boxberry_sum);
    $("#TotalSumma").html(boxberry_sum + Number($('#OrderSumma').val()) - Number($('#SkiSumma').attr('data-discount')));


    $('input[name="city_new"]').val(result.name);
    $('#deliveryInfo').html(locale.cdek.pickup_address +': ' + result.address);
    $("#makeyourchoise").val('DONE');
}

function boxberrywidgetCourier() {

    var zip = $('input[name="index_new"]').val();
    var sum = $("#OrderSumma").val();
    var xid = $("#d").val();
    var weight = $('#boxberryCartWeight').val();
    var depth = $('#boxberryCartDepth').val();
    var height = $('#boxberryCartHeight').val();
    var width = $('#boxberryCartWidth').val();
    if (zip !== '') {

        $.ajax({
            url: ROOT_PATH + '/phpshop/ajax/delivery.php',
            type: 'post',
            data: 'type=json&xid=' + xid + '&sum=' + sum + 'weight=' + weight + '&depth=' + depth + '&height=' + height + '&width=' + width + '&zip=' + zip,
            dataType: 'json',
            success: function (json) {
                if (json['success'] == 1) {
                    $("#DosSumma").html(json['delivery']);
                    $("#TotalSumma").html(json['total']);
                    showAlertMessage(json['message']);
                    $("#makeyourchoise").val('DONE');

                    $('input[name="DeliverySum"]').val(json['delivery']);
                    $('input[name="boxberryInfo"]').val('Курьерская доставка Boxberry по индексу ' + zip);

                    $('#deliveryInfo').html('Курьерская доставка Boxberry по индексу ' + zip);
                } else if (json['success'] == 'indexError') {
                    $('input[name="index_new"]').val('');
                    showAlertMessage(json['message']);
                }
            }
        });

    }
}

// Выбор индекса в подсказках
function showPostalCodeHook(postal_code) {
    if ($('#boxberryCourierDeliveryId').val() == $("#d").val()) {
        $("[name='index_new']").val(postal_code);
        boxberrywidgetCourier();
    }
}

// Очистка индекса в подсказках
function clearPostalCodeHook() {

}

$(document).ready(function () {
    $('body').on('change', 'input[name="index_new"]', function () {
        if ($('#boxberryCourierDeliveryId').val() == $("#d").val()) {
            boxberrywidgetCourier();
        }
    });
});