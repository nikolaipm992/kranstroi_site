function grastinwidgetStart() {
    $('input[name="grastinInfo"]').remove();
    $('input[name="grastinPartnerId"]').remove();
    $('input[name="grastinDeliveryType"]').remove();
    $('input[name="grastinPVZCode"]').remove();
    $('input[name="grastinSum"]').remove();

    $('<input type="hidden" name="grastinInfo">').insertAfter('#dop_info');
    $('<input type="hidden" name="grastinPartnerId">').insertAfter('#dop_info');
    $('<input type="hidden" name="grastinPVZCode">').insertAfter('#dop_info');
    $('<input type="hidden" name="grastinSum">').insertAfter('#dop_info');
    $('#deliveryInfo2').html('');

    if ($('#grastinwidgetModal').length === 0) {
        $.ajax({
            url: ROOT_PATH + '/phpshop/modules/grastinwidget/ajax/grastin.php',
            type: 'post',
            data: {
                weight: $('input[name="grastinWeight"]').val(),
            },
            success: function(json) {
                $('#grastin-container').html(json);
                $("#grastinwidgetModal").modal("toggle");
            }
        });
    } else {
        $("#grastinwidgetModal").modal("toggle");
        $('#grastin-submit').attr('disabled', true);
    }
}

function grastinPvzWidgetCallback(result) {
    //console.log(result);
    $("#DosSumma").html(0);
    $("#TotalSumma").html(Number($('#OrderSumma').val()));
    $('#deliveryInfo').html('');

    if (result.deliveryType == 'courier' && typeof result.partnerId !== 'undefined' && result.partnerId !== '') {
        grastinSetCourier(result);
        grastinSetResult(result);
    }

    if (result.deliveryType == 'pvz' && typeof result.currentId !== 'undefined') {
        grastinSetPvz(result);
        grastinSetResult(result);
    }

    if (result.deliveryType == 'pvz' && result.partnerId == 'post') {
        grastinSetPostPvz(result);
        grastinSetResult(result);
    }

    if (result.cost == 0) {
        $('#grastin-submit').attr('disabled', true);
    }
}

function grastinSetResult(result) {
    var deliveryCost = result.cost;
    if($("#d").data('free') === 1) {
        deliveryCost = 0;
    }

    $('input[data-option="payment3"]').attr('disabled', false);
    $('input[name="city_new"]').val(result.cityTo);
    $('input[name="grastinPartnerId"]').val(result.partnerId);
    $('input[name="grastinSum"]').val(deliveryCost);
    $("#DosSumma").html(deliveryCost);
    $("#TotalSumma").html(Number(deliveryCost) + Number($('#OrderSumma').val()));
    $('#grastin-submit').attr('disabled', false);
}

function grastinSetCourier(result) {
    $('input[name="grastinInfo"]').val('Курьерская доставка ' + result.partnerId + ': город ' + result.cityTo);
    $('#deliveryInfo').html('Курьерская доставка ' + result.partnerId + ': город ' + result.cityTo);
}

function grastinSetPvz(result) {
    $('input[name="grastinInfo"]').val('Самовывоз с ПВЗ ' + result.partnerId + ', информация о ПВЗ: ' + result.pvzData.name);
    $('#deliveryInfo').html('Самовывоз с ПВЗ ' + result.partnerId + ', информация о ПВЗ: ' + result.pvzData.name);

    if (result.partnerId == 'grastin')
        $('input[name="grastinPVZCode"]').val(result.pvzData.name);
    else
        $('input[name="grastinPVZCode"]').val(result.currentId);

    // Блокируем поля адреса, если выбран pvz
    if ($('input[name="street_new"]').length > 0) {
        $('input[name="street_new"]').attr('disabled', true)
    }
    if ($('input[name="house_new"]').length > 0) {
        $('input[name="house_new"]').attr('disabled', true)
    }
    if ($('input[name="flat_new"]').length > 0) {
        $('input[name="flat_new"]').attr('disabled', true)
    }
}

function grastinSetPostPvz(result) {
    $('input[name="grastinInfo"]').val('Почта России: город ' + result.cityTo);
    $('input[name="grastinDeliveryType"]').val('post');
    $('#deliveryInfo').html('Почта России: город ' + result.cityTo);
}