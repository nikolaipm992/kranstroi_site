$(document).ready(function () {
    $("form[name='forma_order']").on('click', 'input[name=dostavka_metod]', function () {
        shiptorEnableAddressFields();
    });

    $("#shiptor_widget_delivery").on("onMethodSelect", function(ce) {
        shiptorEnableAddressFields();
    });

    $("#shiptor_widget_delivery").on("onPvzSelect", function(ce) {
        $('input[name="shiptorPVZId"]').val(ce.originalEvent.detail.id);
        $('input[name="shiptorKladr"]').val(ce.originalEvent.detail.kladr_id);

        shiptorDisableAddressFields(ce.originalEvent['detail']);
    });

    $("#shiptor_widget_delivery").on("onWidgetSuccess", function(ce) {
        var deliveryCost = ce.originalEvent.detail.method.cost.total.sum;
        if($("#d").data('free') === 1) {
            deliveryCost = 0;
        }

        var message = '';
        if(ce.originalEvent.detail.method.type === 'pvz') {
            message = '['+ce.originalEvent.detail.pvz.courier+'] '+ce.originalEvent.detail.pvz.address+' Срок доставки: '+ce.originalEvent.detail.pvz.shipping_days;
        }
        if(ce.originalEvent.detail.method.type === 'post') {
            message = ce.originalEvent.detail.method.method.name + ' Срок доставки: ' + ce.originalEvent.detail.method.days;
        }
        if(ce.originalEvent.detail.method.type === 'courier') {
            message = '['+ce.originalEvent.detail.method.method.name+']'+' Срок доставки: '+ce.originalEvent.detail.method.days;
        }

        $('input[name="shiptorType"]').val(ce.originalEvent.detail.method.type);
        $('input[name="shiptorSum"]').val(deliveryCost);
        $('input[name="shiptorMethodId"]').val(ce.originalEvent.detail.method.method.id);
        $('input[name="shiptorInfo"]').val(message);
        $('#deliveryInfo').html(message);
        $("#makeyourchoise").val('DONE');
        $("#DosSumma").html();
        $("#TotalSumma").html(Number(deliveryCost) + Number($('#OrderSumma').val()));
    });
});

function shiptorStart() {
    $("#makeyourchoise").val('DONE');
    $('input[name="shiptorSum"]').remove();
    $('input[name="shiptorInfo"]').remove();
    $('input[name="shiptorMethodId"]').remove();
    $('input[name="shiptorPVZId"]').remove();
    $('input[name="shiptorType"]').remove();
    $('input[name="shiptorKladr"]').remove();

    $('<input type="hidden" name="shiptorSum">').insertAfter('#dop_info');
    $('<input type="hidden" name="shiptorInfo">').insertAfter('#dop_info');
    $('<input type="hidden" name="shiptorType">').insertAfter('#dop_info');
    $('<input type="hidden" name="shiptorMethodId">').insertAfter('#dop_info');
    $('<input type="hidden" name="shiptorPVZId">').insertAfter('#dop_info');
    $('<input type="hidden" name="shiptorKladr">').insertAfter('#dop_info');

    initShiptor();
}

function initShiptor() {
    if(!window.ShiptorWidget) {
        setTimeout(function () {initShiptor();}, 1000);
    }

    window.ShiptorWidget.init();
}

function shiptorDisableAddressFields(obj) {
    if($('input[name="city_new"]').length) {
        $('input[name="city_new"]').prop('disabled', true).val(obj['prepare_address']['settlement']);
    }
    if($('input[name="index_new"]').length) {
        $('input[name="index_new"]').prop('disabled', true).val(obj['prepare_address']['postal_code']);
    }
    if($('input[name="street_new"]').length) {
        $('input[name="street_new"]').prop('disabled', true).val(obj['prepare_address']['street']);
    }
    if($('input[name="house_new"]').length) {
        $('input[name="house_new"]').prop('disabled', true).val(obj['prepare_address']['house']);
    }
    if($('input[name="flat_new"]').length) {
        $('input[name="flat_new"]').prop('disabled', true);
    }
}

function shiptorEnableAddressFields() {
    if($('input[name="city_new"]').length) {
        $('input[name="city_new"]').prop('disabled', false);
    }
    if($('input[name="index_new"]').length) {
        $('input[name="index_new"]').prop('disabled', false);
    }
    if($('input[name="street_new"]').length) {
        $('input[name="street_new"]').prop('disabled', false);
    }
    if($('input[name="house_new"]').length) {
        $('input[name="house_new"]').prop('disabled', false);
    }
    if($('input[name="flat_new"]').length) {
        $('input[name="flat_new"]').prop('disabled', false);
    }
}