<span class="delivOneEl @deliveryActive@" style="display: block;opacity: 1;">
    <label>
        <input type="radio" value="@deliveryId@" @deliveryChecked@ @deliveryDisabled@ name="dostavka_metod" id="dostavka_metod" data-option="@deliveryPayment@"
               data-toggle="tooltip" data-placement="top" title="@deliveryDisabledReason@">
        <span class="deliveryName">
            &nbsp;<img src='@deliveryIcon@' title='@deliveryTitle@' height='30' class="@php __hide('deliveryIcon'); php@">&nbsp;
            @deliveryTitle@
        </span>
    </label>
</span>