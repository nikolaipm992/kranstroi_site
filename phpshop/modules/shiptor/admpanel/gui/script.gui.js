function shiptorvalidate(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[0-9]|\./;
    if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    }
}
$(document).ready(function() {
    $('body').on('click', 'input[name="shiptor_payment_status"]', function() {
        var value;
        if($('#shiptor_payment_status').prop('checked') === true) {
            value = 1;
        }
        if($('#shiptor_payment_status').prop('checked') === false) {
            value = 0;
        }

        $.ajax({
            mimeType: 'text/html; charset='+locale.charset,
            url: '/phpshop/modules/shiptor/ajax/ajax.php',
            type: 'post',
            data: {
                operation: 'changePaymentStatus',
                value: value,
                orderId: $('input[name="shiptor_order_id"]').val()
            },
            dataType: "json",
            async: false,
            success: function(json) {}
        });
    });
});