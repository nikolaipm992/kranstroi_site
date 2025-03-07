
function UpdatePromotion(promo) {

    var sum = $("#OrderSumma").val();
    var ssum = $("#SkiSumma").attr('data-discount');
    var promocode = $("#promocode").val();
    var tipoplcheck = $("#order_metod:checked").val();
    var dostavka = $("#DosSumma").html();
    var wsum = $("#WeightSumma").html();

    if (typeof promocode == "undefined" || promocode.length === 0) {
        promocodei = promo;
    } else {
        promocodei = promocode;
    }

    $("#promotion_load").show();

    $.ajax({
        url: '/phpshop/modules/promotions/ajax/promotions.php',
        type: 'post',
        data: 'promocode=' + promocodei + '&sum=' + sum + '&type=json&ssum=' + ssum + '&tipoplcheck=' + tipoplcheck + '&dostavka=' + dostavka + '&wsum=' + wsum,
        dataType: 'json',
        success: function (json) {

            if (json['success']) {
                var messageBox = '.success-notification';

                // ���� ��� �������� ��� ����������� �������, ������� ������� alert
                if ($(messageBox).length < 1) {
                    json['mes'] = json['mesclean'];
                }

                // ���� ������������� �����
                if (json['status'] == 1) {

                    //������� �������� �����
                    var totalsum = parseFloat($("#TotalSumma").html().replace(/ /g, ''));
                    var totalajax = parseFloat(json['total']);

                    if (parseInt(totalsum) >= parseInt(totalajax)) {

                        $("#TotalSumma").html(json['total']);
                        $("#SkiSumma").html(json['discount']);
                        $("#OrderSumma").val(json['totalsummainput']);
                        //$("#SkiSummaAll").html(json['discountall']);

                        //���������� ��������
                        if (json['freedelivery'] == 0) {
                            $("#DosSumma").html(json['delivery']);
                        }

                        $("#promocode").parent('.form-group, .input-group').addClass("has-success");
                        $("#promocode").parent('.form-group, .input-group').removeClass("has-error");

                        $(".paymOneEl").addClass("paymOneElr");
                        // $(".paymOneEl").removeClass("paymOneEl");

                        if (json['deliverymethodcheck'] != 0) {

                            $("input[name=order_metod]").change(function () {
                                if (this.value != json['deliverymethodcheck']) {
                                    $(this).closest('.paymOneEl').removeClass('active').attr("disabled", true);
                                    $('#order_metod[value="' + json['deliverymethodcheck'] + '"]').closest('.paymOneElr').addClass('active');
                                    $('#order_metod[value="' + json['deliverymethodcheck'] + '"]').prop('checked', true);
                                    showAlertMessage('��� ������� �����-���� ���������� ������� ������ ��� ������!');
                                }

                            });
                        }

                        $("#promotion_load").hide();

                        //������� ���������
                        if (json['mes'] != '') {
                            showAlertMessage(json['mes']);
                        }

                    } else {
                        showAlertMessage('��� ������� �����-���� ������ �������� ������� ��� ����������� ������');
                        $("#promotion_load").hide();
                    }

                } else if (json['status'] == 9) {

                    $("#TotalSumma").html(json['total']);
                    $("#SkiSumma").html(json['discount']);
                    $("#OrderSumma").val(json['totalsummainput']);
                    $("#SkiSummaAll").html(json['discountall']);

                    //���������� ��������
                    if (json['freedelivery'] == 0) {
                        $("#DosSumma").html(json['freedelivery']);
                    }

                    // ������� ��������� ���������
                    $("#promocode").parent('.form-group').removeClass("has-success");
                    $("#promocode").parent('.form-group').removeClass("has-error");

                    if (json['deliverymethodcheck'] != 0) {
                        $('input[name=order_metod]').attr("disabled", true);
                        $('input[name=order_metod]:checked').attr("disabled", false);
                        $(".paymOneElr").click(function () {
                            showAlertMessage('��� ������� �����-���� ���������� ������� ������ ��� ������!');
                        });
                    }

                    $("#promotion_load").hide();

                }
                //���� ������������� �����
                else {

                    // �������� ������ ���������
                    $("#promocode").parent('.form-group').removeClass("has-success");
                    $("#promocode").parent('.form-group').addClass("has-error");

                    $("#promotion_load").hide();

                    //�������������� �� ������ �����
                    if (json['action'] == 1) {
                        $("html, body").delay(2000).animate({scrollTop: $('#order_metod').offset().top}, 2000);

                    }
                    //������� ���������
                    if (json['mes'] != '')
                        showAlertMessage(json['mes']);
                }


            }
        }
    });
}
