<script type="text/javascript" src="https://widget.shiptor.ru/embed/widget.js"></script>
<script type="text/javascript" src="phpshop/modules/shiptor/templates/script.js?v=1.0"></script>
<input type="hidden" name="shiptor_delivery_id" value="@shiptor_deliery_id@">
<div id="shiptor_widget_delivery"
     data-weight='@shiptor_weight@'
     data-dimensions='@shiptor_dimensions@'
     data-price='@shiptor_price@'
     data-cod='@shiptor_cod@'
     data-card="0"
     data-declaredCost='@shiptor_declared_cost@'
     data-markup="@shiptor_fee@%"
     data-round="@shiptor_round@"
     data-courier="@shiptor_courier@"
     data-addDays="@shiptor_add_days@"
     data-yk="@shiptor_yandex_key@"
     data-pk="@shiptor_api_key@"
     class="_shiptor_widget">
</div>
<a href="#" data-role="shiptor_widget_show" id="shiptor-init" style="display: none;">Показать виджет</a> <!-- Что бы виджет не отображался сразу -->