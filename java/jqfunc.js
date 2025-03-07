/**
 * ��������� JQuery �������
 * @package PHPShopJavaScript
 * @author PHPShop Software
 * @version 1.7
 */

// ������ � �������� ���� ���������
var MEGA_MENU_ICON = false;

// ���� ����������� ��������� �����������
var CATALOG_MENU = true;

// �������� ������
var FILTER = true;

var BRAND_MENU = true;

// ���������� ��������� ��� ������������ ��������� �������
var AJAX_SCROLL_HIDE_PAGINATOR = false;

// ����� ���������� �� �����
var ROOT_PATH = '';

// �������� �������� ����
var FIXED_NAVBAR = true;

// DaData.ru Token
var DADATA_TOKEN = false;



// HTML �������� �������� ��� ���� ��������
var waitText = '<span class="wait">&nbsp;</span>';

// �����������
function commentList(xid, comand, page, cid) {
    var message = "";
    var rateVal = 0;

    if (page === undefined) {
        page = 0;
    }

    if (cid === undefined) {
        cid = 0;
    }


    if (comand == "add") {
        message = $('#message').val();
        if (message == "") {
            return false;
        }
        if ($('input[name=rate][type=radio]:checked').val()) {
            rateVal = $('input[name=rate][type=radio]:checked').val();
        }
    }

    if (comand == "edit_add") {
        message = $('#message').val();
        cid = $('#commentEditId').val();
        $('#commentButtonAdd').show();
        $('#commentButtonEdit').hide();
    }

    if (comand == "dell") {
        if (confirm(locale.commentList.dell)) {
            cid = $('#commentEditId').val();
            $('#commentButtonAdd').show();
            $('commentButtonEdit').hide();
        } else {
            cid = 0;
        }
    }

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/comment.php',
        type: 'post',
        data: 'xid=' + xid + '&comand=' + comand + '&type=json&page=' + page + '&rateVal=' + rateVal + '&message=' + message + '&cid=' + cid,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {

                if (comand == "edit") {
                    $('#message').val(json['comment']);
                    $('#commentButtonAdd').hide();
                    $('#commentButtonEdit').show();
                    $('#commentButtonEdit').show();
                    $('#commentEditId').val(cid);
                } else {
                    document.getElementById('message').value = "";
                    if (json['status'] == "error") {
                        mesHtml = locale.commentList.mesHtml;
                        mesSimple = locale.commentList.mesHtml;

                        showAlertMessage(mesHtml);

                        if ($('#evalForCommentAuth')) {
                            eval($('#evalForCommentAuth').val());
                        }
                    }
                    $('#commentList').html(json['comment']);
                }
                if (comand == "edit_add") {
                    mes = locale.commentList.mes;
                    showAlertMessage(mes);

                }
                if (comand == "add" && json['status'] != "error") {
                    mes = locale.commentList.mes;
                    showAlertMessage(mes);
                }
            }
        }
    });
}


// �����������
var locale_def = {
    commentList: {
        mesHtml: "������� ���������� ����������� �������� ������ ��� �������������� �������������.\n<a href='/users/?from=true'>������������� ��� �������� �����������</a>.",
        mesSimple: "������� ���������� ����������� �������� ������ ��� �������������� �������������.\n������������� ��� �������� �����������.",
        mes: "��� ����������� ����� �������� ������ ������������� ������ ����� ����������� ���������...",
        dell: "�� ������������� ������ ������� �����������?",
    },
    OrderChekJq: {
        badReqEmail: "����������, ������� ���������� E-mail",
        badReqName: "�������� ��������,\n��� ������ �������� �� ����� ��� �� 3 ����",
        badReq: "�������� ��������,\n���� ����, ������������ ��� ����������",
        badDelivery: "����������,\n�������� ��������",
    },
    commentAuthErrMess: "�������� ����������� ����� ������ �������������� ������������.\n<a href='" + ROOT_PATH + "/users/?from=true'>����������, ������������� ��� �������� �����������</a>.",
};

// ����� ��������� ����� ���������� � �������, ���������, ������� � �.�.
function showAlertMessage(message, danger) {

    if (typeof danger != 'undefined') {
        if (danger === true) {
            danger = 'danger';
        }
        $('.success-notification').find('.alert').addClass('alert-' + danger);
    } else {
        $('.success-notification').find('.alert').removeClass('alert-danger');
        $('.success-notification').find('.alert').removeClass('alert-info');
    }

    var messageBox = '.success-notification';
    var innerBox = '#notification .notification-alert';

    if ($(messageBox).length > 0) {
        $(innerBox).html(' ');
        $(innerBox).html(message);
        $(messageBox).fadeIn('slow');

        setTimeout(function () {
            $(messageBox).delay(500).fadeOut(1000);
        }, 5000);
    }
}

// �������� ���������� email
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

// ���������� ������ � �������
function addToWishList(product_id, parent_id = 0) {

    if (parent_id === undefined) {
        parent_id = '';
    }

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/wishlist.php',
        type: 'post',
        data: 'product_id=' + product_id + '&parent_id=' + parent_id,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $(".wishlistcount").html(json['count']);
            }
        }
    });
}

// ������� ��������
function UpdateDeliveryJq(xid, param, stop_hook) {

    var sum = $("#OrderSumma").val();
    var wsum = $("#WeightSumma").html();

    if (param === undefined) {
        param = '';
    }

    $("form[name='forma_order'] input[name=dostavka_metod]").attr('disabled', true);
    $(this).html(waitText);

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/delivery.php',
        type: 'post',
        data: 'type=json&xid=' + xid + '&sum=' + sum + '&wsum=' + wsum + param,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                $("#DosSumma").html(json['delivery']);
                $("#d").val(xid);
                $("#d").data('free', json['free_delivery']);
                $("#TotalSumma").html(json['total']);
                $("#seldelivery").html(json['dellist']);
                if ($('input[name="dostavka_metod"]:disabled').length > 0) {
                    $('input[name="dostavka_metod"]:disabled').each(function (index, element) {
                        if ($(element).attr('title')) {
                            $(element).closest('label').tooltip({
                                title: $(element).attr('title'),
                                placement: 'top'
                            });
                        }
                    });
                }

                $("#userAdresData").hide();
                $("#seldelivery").html(json['userAdresData']);
                $("#userAdresData").html(json['adresList']);
                $("#userAdresData").fadeIn("slow");

                $('#deliveryInfo').html(null);

                // ���������� �������� �����
                var paymentStop = $('input[name="dostavka_metod"]:checked').attr('data-option');
                if (paymentStop !== undefined) {
                    var payment_array = paymentStop.split(",");
                }

                $('.paymOneEl input[name="order_metod"]').each(function () {
                    $(this).attr('disabled', false);
                });

                if ($.isArray(payment_array)) {
                    $.each(payment_array, function (index, value) {
                        $('.paymOneEl input[data-option="payment' + value + '"]').attr('disabled', true);
                        $('.paymOneEl input[data-option="payment' + value + '"]').attr('checked', false);
                    });
                }

                if ($("input#order_metod:checked").length == 0) {
                    $('input#order_metod').each(function () {
                        if (!this.disabled) {
                            this.checked = true;
                            return false;
                        }
                    });
                }

                // ���� ���� ��������
                if (json['hook'] !== undefined && json['hook'] !== "" && stop_hook === undefined) {
                    eval(json['hook']);
                }

                // ��������� ��� ��������� �� ������ ������
                if ($("form[name='forma_order'] input[name='fio_new']").val() == "") {
                    $("form[name='forma_order'] input[name='fio_new']").val($("form[name='forma_order'] input[name='name_new']").val());
                }

                // ��������� ������� �����, ���� ������
                $("#adres_id").change();

                // ��������� DaData.ru
                if (typeof $('#body').attr('data-token') !== 'undefined' && $('#body').attr('data-token').length) {
                    var DADATA_TOKEN = $('#body').attr('data-token');
                }
                if (DADATA_TOKEN !== false && DADATA_TOKEN !== undefined) {
                    var token = DADATA_TOKEN;
                    var type = "ADDRESS";
                    var $city = $("form[name='forma_order'] input[name='city_new']");
                    var $street = $("form[name='forma_order'] input[name='street_new']");
                    var $house = $("form[name='forma_order'] input[name='house_new']");

                    $city.suggestions({
                        token: token,
                        type: type,
                        hint: false,
                        bounds: "city-settlement",
                        onSelect: showPostalCode,
                        onSelectNothing: clearPostalCode
                    });

                    $street.suggestions({
                        token: token,
                        type: type,
                        hint: false,
                        bounds: "street",
                        constraints: $city,
                        onSelect: showPostalCode,
                        onSelectNothing: clearPostalCode
                    });

                    $house.suggestions({
                        token: token,
                        type: type,
                        hint: false,
                        bounds: "house",
                        constraints: $street,
                        onSelect: showPostalCode,
                        onSelectNothing: clearPostalCode
                    });
                    function showPostalCode(suggestion) {

                        // ��� �� ����� �������
                        if (typeof showPostalCodeHook !== 'undefined' && typeof showPostalCodeHook === 'function') {
                            showPostalCodeHook(suggestion.data.postal_code);
                        } else {
                            $("[name='index_new']").val(suggestion.data.postal_code);
                        }
                    }
                    function clearPostalCode() {

                        // ��� �� ����� �������
                        if (typeof clearPostalCodeHook !== 'undefined' && typeof clearPostalCodeHook === 'function') {
                            showPostalCodeHook(suggestion.data.postal_code);
                        } else {
                            $("[name='index_new']").val("");
                        }

                    }
                    $("form[name='forma_order'] input[name='org_name_new']").suggestions({
                        token: DADATA_TOKEN,
                        type: "PARTY",
                        count: 5
                    });
                }
            }
        }
    });
}

// �������� ����� ������
function OrderChekJq() {

    if ($("#makeyourchoise").val() != "DONE") {
        bad = 1;
    } else {
        bad = 0;
    }

    var badReq = 0;
    var badReqName = 0;
    var badReqEmail = 0;
    var badReqTel = 0;
    $('form[name="forma_order"] .req').each(function () {

        // ��������� ���������� e-mail � ��� ������������
        if ($(this).attr('name') == 'mail' && !IsEmail($(this).val())) {
            badReqEmail = 1;
        }

        if ($(this).attr('name') == 'name_new') {

            if ($(this).val().length < 3) {
                badReqName = 1;
            }
        }

        if ($(this).val() == "" || ($(this).attr('name') == 'rule' && $(this).prop('checked') == false) || (badReqEmail && $(this).attr('name') == 'mail') || (badReqName && $(this).attr('name') == 'name_new')) {

            // ��������� �� ����� �� ����� ������� ������������ ����
            if (badReq == 0) {
                var destination = $(this).parent().offset().top;
                var par = $(this);
                $("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 800, function () {
                    par.focus();
                });
            }

            if (badReq == 0) {
                badReq = 1;
            }

            $(this).addClass('reqActiv');
        }

    });

    if (badReqEmail == 1) {
        showAlertMessage(locale_def.OrderChekJq.badReqEmail);
    } else if (badReqName == 1) {
        showAlertMessage(locale_def.OrderChekJq.badReqName);
    } else if (badReq == 1) {
        showAlertMessage(locale_def.OrderChekJq.badReq);
    } else if (bad == 1) {
        showAlertMessage(locale_def.OrderChekJq.badDelivery);
        var destination = $('#seldelivery').offset().top;
        $("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 800);
    } else {
        $('form[name="forma_order"]').submit();
    }
}

// ������� ��������� ������
function wpiGenerateRandomNumber(limit) {
    limit = limit || 8;
    var password = '';
    var chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    var list = chars.split('');
    var len = list.length, i = 0;
    do {
        i++;
        var index = Math.floor(Math.random() * len);
        password += list[index];

    } while (i < limit);
    return password;
}

$(document).ready(function () {

    // DaData.ru �����
    if (typeof $('#body').attr('data-token') !== 'undefined' && $('#body').attr('data-token').length) {
        var DADATA_TOKEN = $('#body').attr('data-token');
    }

    // �������� ��������� �� ����� �� ������ ��������
    $('#notification').on('click', 'img', function () {
        $(this).parent().fadeOut('slow', function () {
            $(this).hide();
        });
    });

    // ������ ��������� ������ ��� �����������
    $(".passGen").click(function () {
        var str = wpiGenerateRandomNumber(8);
        $(this).closest('form').find("input[name='password_new'], input[name='password_new2']").val(str);
        showAlertMessage('��� ��������������� ������ ����� ������ �� ��� email ����� �����������');
    });

    // ���������� ������ � �� ������ ��� ������ ��� �����
    $('form').on('reset', function (e) {
        setTimeout(function () {
            $("#order_metod").change();
        });
    });

    // �������� ����� ��������� ������������
    $("input#order_metod").change(function () {
        var str = "";
        str = ".showYurDataForPaymentClass" + $("input#order_metod:checked").val();
        if (str != "" && $(str).html()) {
            $("#showYurDataForPaymentLoad").html($(str).clone().removeClass().show());
            if (DADATA_TOKEN !== false && DADATA_TOKEN !== undefined) {
                $("#showYurDataForPaymentLoad input[name='org_name_new']").suggestions({
                    token: DADATA_TOKEN,
                    type: "PARTY",
                    count: 5,
                    onSelect: showSuggestion
                });
                $("#showYurDataForPaymentLoad input[name='org_bank_new']").suggestions({
                    token: DADATA_TOKEN,
                    type: "BANK",
                    count: 5,
                    onSelect: showSuggestionBank
                });
            }
        } else {
            $("#showYurDataForPaymentLoad").html('');
        }
    });

    // �������� ������ � ������ ������
    $("input#order_metod:first").attr('checked', 'checked').change().closest('.paymOneEl').addClass('active');

    // ��� ��������� ������, ��������� �����. ����
    $("#adres_id").change(function () {
        var str = "";
        $(this).find("option:selected").each(function () {
            str = $(this).val();
        });
        if (!str) {
            return;
        }

        // �������� ������ �������
        var obj = jQuery.parseJSON($("input:hidden.adresListJson").val());
        $.each(obj, function (index, value) {
            $.each(value, function (index2, value2) {
                $("input[name='" + index2 + "']").val("");
            });
        });

        $.each(obj[str], function (index, value) {
            if (value != "") {
                name = "input[name='" + index + "']";
                $(name).val(value);
                $(name).removeClass('reqActiv');
            }
        });
    }).change();

    // ������ ������� �� ������
    $("form[name='forma_order']").on('change', 'select.citylist', function () {
        var par = $(this).attr("name");
        if (par == "city_new") {
            return false;
        }
        if (par == "country_new") {
            $("form[name='forma_order'] select.citylist[name=city_new] option[value!='']").remove();
            $("form[name='forma_order'] select.citylist[name=state_new] option[value!='']").remove();
        }
        if (par == "state_new") {
            $("form[name='forma_order'] select.citylist[name=city_new] option[value!='']").remove();
        }

        $("form[name='forma_order'] select.citylist").attr("disabled", true);
        $(this).after(waitText);
        $.ajax({
            url: ROOT_PATH + '/phpshop/ajax/citylist.php',
            type: 'post',
            data: {
                country: $("form[name='forma_order'] select.citylist[name=country_new] option:selected").attr('for'),
                region: $("form[name='forma_order'] select.citylist[name=state_new] option:selected").attr('for'),
                par: par
            },

            success: function (data) {
                $("#citylist .wait").remove();
                $("form[name='forma_order'] select.citylist[name=country_new]").attr("disabled", false);
                switch (par) {
                    case "country_new":
                        $("form[name='forma_order'] select.citylist[name=state_new]").html(data);
                        $("form[name='forma_order'] select.citylist[name=state_new]").attr("disabled", false);
                        break;
                    case "state_new":
                        $("form[name='forma_order'] select.citylist[name=city_new]").html(data);
                        $("form[name='forma_order'] select.citylist[name=city_new]").attr("disabled", false);
                        $("form[name='forma_order'] select.citylist[name=state_new]").attr("disabled", false);
                        break;
                }
            }
        });
    });

    // ����� ������� ��������
    $("form[name='forma_order']").on('click', 'input[name=dostavka_metod]', function () {
        $(this).next().after(waitText);
        UpdateDeliveryJq($(this).val());
    });

    // ��� ����� ����� ������������, ��������� ����������� ��� � ����� ���� �� ����
    $("form[name='forma_order']").on('change', 'input[name=name_new]', function () {
        if ($("form[name='forma_order'] input[name='fio_new']").val() == "") {
            $("form[name='forma_order'] input[name='fio_new']").val($(this).val());
        }
    });

    // ��������� ����� ������� ��������� ��� ������������ ����� ��� �������� �� ���
    $('form[name="forma_order"]').on('keyup blur change', '.req', function () {
        if ($(this).val() != '') {
            $(this).removeClass('reqActiv');
        } else {
            $(this).addClass('reqActiv');
        }
    });

    // ������ � �������
    $('textarea.commentTextarea').on('focus', function () {
        if ($('input#commentAuthFlag').val() == 0) {
            $(this).val("").attr('readonly', 'readonly');
            showAlertMessage(locale_def.commentAuthErrMess);
            if (document.getElementById('evalForCommentAuth')) {
                eval(document.getElementById('evalForCommentAuth').value);
            }
        }
    });

    // ��������� ������ � �������
    var cart_lang = [];
    for (var i = 0; i < 100; i++) {
        cart_lang[i] = '��';
    }
    cart_lang[1] = '';
    cart_lang[2] = '�';
    cart_lang[3] = '�';
    cart_lang[4] = '�';
    cart_lang[21] = '';
    cart_lang[22] = '�';
    cart_lang[23] = '�';
    cart_lang[24] = '�';
    if (cart_lang[$('#num').text()] != 'undefined') {
        $('#lang-cart').text('�����' + cart_lang[$('#num').text()]);
    }

    $(".button").click(function () {
        setTimeout(function () {
            if (cart_lang[$('#num').text()] != 'undefined') {
                $('#lang-cart').text('�����' + cart_lang[$('#num').text()]);
            }
        }, 1000);
    });

    // �������� ��������� � �������
    $('#notification').on('close.bs.alert', function (e) {
        e.preventDefault();
        $('#notification').css('display', 'none');
    });

});

// ����� ��������� DaData.ru � ����� ����������� ������
function showSuggestion(suggestion) {
    var data = suggestion.data;
    if (!data) {
        return;
    }
    $("input[name='org_inn_new']").val(data.inn);
    $("input[name='org_kpp_new']").val(data.kpp);
    $("input[name='org_yur_adres_new']").val(data.address.value);
    $("input[name='org_fakt_adres_new']").val(data.address.value);
}

function showSuggestionBank(suggestion) {
    var data = suggestion.data;
    if (!data) {
        return;
    }
    $("input[name='org_bik_new']").val(data.bic);
    $("input[name='org_city_new']").val(data.address.data.city);
    $("input[name='org_kor_new']").val(data.correspondent_account);
}