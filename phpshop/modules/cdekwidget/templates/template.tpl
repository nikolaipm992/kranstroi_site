@cdek_scripts@
<div class="modal fade bs-example-modal" id="cdekwidgetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{��������}</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
            </div>
            <div class="modal-body" style="width:100%;">
                <div id="forpvz" style="height: 600px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="ddelivery-close">{�������}</button>
            </div>
        </div>
    </div>
</div>
<!--/ ��������� ���� cdekwidget -->
<script>
    var PHPShopCDEKOptions = {
        defaultCity: '@cdek_default_city@',
        cityFrom: '@cdek_city_from@',
        products: $.parseJSON('@cdek_cart@'),
        ymapApiKey: '@cdek_ymap_key@',
        admin: @cdek_admin@,
        russiaOnly: @russia_only@
    };
</script>