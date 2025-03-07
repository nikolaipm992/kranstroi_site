function grastinvalidate(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[0-9]|\./;
    if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    }
}

$(document).ready(function () {
    if (typeof $('#body').attr('data-token') !== 'undefined' && $('#body').attr('data-token').length)
        var DADATA_TOKEN = $('#body').attr('data-token');
    if (DADATA_TOKEN !== false && DADATA_TOKEN !== undefined) {
        $("input[name='to_city_new']").suggestions({
            token: DADATA_TOKEN,
            type: 'ADDRESS',
            hint: false,
            bounds: "city-settlement",
            onSelect: function (response) {
                $("input[name='to_city_new']").val(response.data.city)
            }
        });
    }
});
