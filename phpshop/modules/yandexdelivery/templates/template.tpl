<style>
    .transition{
        opacity: 1;
    }
</style>


<!-- Модальное окно yandexwidget -->
<div class="modal fade bs-example-modal" id="yandexwidgetModal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Доставка</h4>
                <p class="text-danger">Выберите город и пункт выдачи товара, если нет пунктов увеличьте масштаб и подождите</p>
            </div>
            <div class="modal-body">

                <script async src="//widget-pvz.dostavka.yandex.net/widget.js"></script>

                <div id="delivery-widget"></div>
                <style>
                    .widget__wrapper{
                        height:500px !important;
                    }
                </style>

                <!-- Код виджета -->
                <script>
                    (function(w) {
                        function startWidget() {

                            var weight = Number($('input[name="yandexdelivery_weight"]').val());
                            var city = $('input[name="yandexdelivery_city"]').val();
                            var station = $('input[name="yandexdelivery_station"]').val();

                            w.YaDelivery.createWidget({
                                containerId: 'delivery-widget',   // Идентификатор HTML-элемента (контейнера), в котором будет отображаться виджет
                                params: {
                                    city: city,               // Город отображаемый на карте при запуске
                                    // Размеры виджета
                                    size:{
                                        "height": "500px",        // Высота
                                        "width": "100%"           // Ширина
                                    },
                                    source_platform_station: station, // Станция отгрузки
                                    physical_dims_weight_gross: weight, // Вес отправления
                                    delivery_price: (price) => "от " +price + " руб",     // Стоимость доставки
                                 
                                    show_select_button: true,     // Отображение кнопки выбора ПВЗ (false - скрыть кнопку, true - показать кнопку)
                                    filter: {
                                        // Тип способа доставки
                                        type: [
                                            "pickup_point",       // Пункт выдачи заказа
                                            "terminal"            // Постамат
                                        ],
                                        //is_yandex_branded: true, // Тип пункта выдачи заказа (false - Партнерские ПВЗ, true - ПВЗ Яндекса)
                                        // Способ оплаты
                                        payment_methods: [
                                            "already_paid",       // Доступен для доставки предоплаченных заказов
                                        ]
                                    }
                                },
                            });
                        }
                        w.YaDelivery
                            ? startWidget()
                            : document.addEventListener('YaNddWidgetLoad', startWidget);
                    })(window);

                </script>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="yandexdelivery_weight" value="@yandexdelivery_weight@">
<input type="hidden" name="yandexdelivery_city" value="@yandexdelivery_city@">
<input type="hidden" name="yandexdelivery_station" value="@yandexdelivery_station@">
<script type="text/javascript" src="phpshop/modules/yandexdelivery/templates/yandexwidget.js?v=2" ></script>
<!--/ Модальное окно yandexwidget -->
