<style>
    .transition{
        opacity: 1;
    }
</style>


<!-- ��������� ���� yandexwidget -->
<div class="modal fade bs-example-modal" id="yandexwidgetModal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">��������</h4>
                <p class="text-danger">�������� ����� � ����� ������ ������, ���� ��� ������� ��������� ������� � ���������</p>
            </div>
            <div class="modal-body">

                <script async src="//widget-pvz.dostavka.yandex.net/widget.js"></script>

                <div id="delivery-widget"></div>
                <style>
                    .widget__wrapper{
                        height:500px !important;
                    }
                </style>

                <!-- ��� ������� -->
                <script>
                    (function(w) {
                        function startWidget() {

                            var weight = Number($('input[name="yandexdelivery_weight"]').val());
                            var city = $('input[name="yandexdelivery_city"]').val();
                            var station = $('input[name="yandexdelivery_station"]').val();

                            w.YaDelivery.createWidget({
                                containerId: 'delivery-widget',   // ������������� HTML-�������� (����������), � ������� ����� ������������ ������
                                params: {
                                    city: city,               // ����� ������������ �� ����� ��� �������
                                    // ������� �������
                                    size:{
                                        "height": "500px",        // ������
                                        "width": "100%"           // ������
                                    },
                                    source_platform_station: station, // ������� ��������
                                    physical_dims_weight_gross: weight, // ��� �����������
                                    delivery_price: (price) => "�� " +price + " ���",     // ��������� ��������
                                 
                                    show_select_button: true,     // ����������� ������ ������ ��� (false - ������ ������, true - �������� ������)
                                    filter: {
                                        // ��� ������� ��������
                                        type: [
                                            "pickup_point",       // ����� ������ ������
                                            "terminal"            // ��������
                                        ],
                                        //is_yandex_branded: true, // ��� ������ ������ ������ (false - ����������� ���, true - ��� �������)
                                        // ������ ������
                                        payment_methods: [
                                            "already_paid",       // �������� ��� �������� �������������� �������
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
                <button type="button" class="btn btn-default" data-dismiss="modal">�������</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="yandexdelivery_weight" value="@yandexdelivery_weight@">
<input type="hidden" name="yandexdelivery_city" value="@yandexdelivery_city@">
<input type="hidden" name="yandexdelivery_station" value="@yandexdelivery_station@">
<script type="text/javascript" src="phpshop/modules/yandexdelivery/templates/yandexwidget.js?v=2" ></script>
<!--/ ��������� ���� yandexwidget -->
