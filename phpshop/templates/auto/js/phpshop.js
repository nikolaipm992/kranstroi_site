// ���������� ������ � �������
function addToCartList(product_id, num, parent, addname) {

    if (num === undefined)
        num = 1;

    if (addname === undefined)
        addname = '';

    if (parent === undefined)
        parent = 0;

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/cartload.php',
        type: 'post',
        data: 'xid=' + product_id + '&num=' + num + '&xxid=0&type=json&addname=' + addname + '&xxid=' + parent,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $("#num, .cartnum, #mobilnum").html(json['num']).removeClass('d-none');
                $("#sum").html(json['sum']);
                $("#order").removeClass('d-none');
            }
        }
    });
}

// ���������� ������ � �������
function addToCompareList(product_id) {

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/compare.php',
        type: 'post',
        data: 'xid=' + product_id + '&type=json',
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $(".numcompare").html(json['num']).removeClass('d-none');
            }
        }
    });
}

// �����������
function fotoload(xid, fid) {

    $.ajax({
        url: ROOT_PATH + '/phpshop/ajax/fotoload.php',
        type: 'post',
        data: 'xid=' + xid + '&fid=' + fid + '&type=json',
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                $("#fotoload").fadeOut('slow', function () {
                    $("#fotoload").html(json['foto']);
                    $("#fotoload").fadeIn('slow');
                });
            }
        }
    });
}

function ChangeSkin() {
    document.SkinForm.submit();
}

// ����� ������
function ChangeValuta() {
    document.ValutaForm.submit();
}

// �������� ������ ��� ����������
function ReturnSortUrl(v) {
    var s, url = "";
    if (v > 0) {
        s = document.getElementById(v).value;
        if (s != "")
            url = "v[" + v + "]=" + s + "&";
    }
    return url;
}

// �������� ������� ����� ��������, ��������� ��������
function NoFoto(obj, pathTemplate) {
    obj.src = pathTemplate + '/images/shop/no_photo.gif';
}

// ���������� �� ���� ��������
function GetSortAll() {
    var url = ROOT_PATH + "/shop/CID_" + arguments[0] + ".html?";

    var i = 1;
    var c = arguments.length;

    for (i = 1; i < c; i++)
        if (document.getElementById(arguments[i]))
            url = url + ReturnSortUrl(arguments[i]);

    location.replace(url.substring(0, (url.length - 1)) + "#sort");

}

// �������������� ������� �������� �� �������
var trans = [];
for (var i = 0x410; i <= 0x44F; i++)
    trans[i] = i - 0x350; // �-��-�
trans[0x401] = 0xA8;    // �
trans[0x451] = 0xB8;    // �

// ������� �������� �� ����������
/*
 trans[0x457] = 0xBF;    // �
 trans[0x407] = 0xAF;    // �
 trans[0x456] = 0xB3;    // �
 trans[0x406] = 0xB2;    // �
 trans[0x404] = 0xBA;    // �
 trans[0x454] = 0xAA;    // �
 */

// ��������� ����������� ������� escape()
var escapeOrig = window.escape;

// �������������� ������� escape()
window.escape = function (str)
{

    if (locale.charset == 'utf-8')
        return str;

    else {
        var str = String(str);
        var ret = [];
        // ���������� ������ ����� ��������, ������� ��������� ���������
        for (var i = 0; i < str.length; i++)
        {
            var n = str.charCodeAt(i);
            if (typeof trans[n] != 'undefined')
                n = trans[n];
            if (n <= 0xFF)
                ret.push(n);
        }
        return escapeOrig(String.fromCharCode.apply(null, ret));
    }
};

// Ajax ������ ���������� ������
function filter_load(filter_str, obj) {

    $.ajax({
        type: "POST",
        url: '?' + filter_str.split('#').join(''),
        data: {
            ajax: true,
            json: true
        },
        success: function (data)
        {
            if (data === 'empty_sort') {
                showAlertMessage('������ �� �������', true);
            } else {
                $(".template-product-list").html(data['products']);
                $('#price-filter-val-max').removeClass('has-error');
                $('#price-filter-val-min').removeClass('has-error');
                $("#pagination-block").html(data['pagination']);

                // ����������� ������ �������� � �������� ���������� �������
                if (typeof FILTER_CACHE !== "undefined" && FILTER_CACHE) {
                    $('#faset-filter-body [type="checkbox"]').each(function () {

                        if (data['logic'] == 0) {
                            $(this).attr('disabled', 'disabled');
                            $(this).next('.filter-item').addClass('filter-item-hide');
                        } else if (data['logic'] == 1 && $(this).attr('data-count') != 1) {
                            $(this).attr('disabled', 'disabled');
                            $(this).next('.filter-item').addClass('filter-item-hide');
                        }

                        if (FILTER_COUNT && data['logic'] == 0) {
                            $('[data-num="' + $(this).attr('name') + '"]').text(0);
                        }

                        for (var key in data['filter']) {
                            if ($(this).attr('name') == key) {

                                $(this).removeAttr('disabled');
                                $(this).next('.filter-item').removeClass('filter-item-hide');

                                if (FILTER_COUNT && data['logic'] == 0) {
                                    $('[data-num="' + $(this).attr('name') + '"]').text(data['filter'][key]);
                                } else if (data['logic'] == 1 && $(this).attr('data-count') != 1) {
                                    $('[data-num="' + $(this).attr('name') + '"]').text(data['filter'][key]);
                                }
                            }
                        }
                    });
                }
            }
        },
        error: function (data) {
            $(obj).attr('checked', false);

            if ($(obj).attr('name') == 'max') {
                $('#price-filter-val-max').addClass('has-error');
            }
            if ($(obj).attr('name') == 'min') {
                $('#price-filter-val-min').addClass('has-error');
            }

            window.location.hash = window.location.hash.split($(obj).attr('data-url') + '&').join('');
        }

    });
}

// ������� �������
function price_slider_load(min, max, obj) {

    var hash = window.location.hash.split('min=' + $.cookie('slider-range-min') + '&').join('');
    hash = hash.split('max=' + $.cookie('slider-range-max') + '&').join('');
    hash += 'min=' + min + '&max=' + max + '&';
    window.location.hash = hash;

    filter_load(hash, obj);

    $.cookie('slider-range-min', min);
    $.cookie('slider-range-max', max);

    $(".pagination").hide();

}

// Ajax ������ ������� �����
function faset_filter_click(obj) {

    if (AJAX_SCROLL) {

        var hash;
        var hashes = window.location.href.split('#')[0].slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            if (hash[0].match(/v\[(.*)\]/) && window.location.hash.indexOf(hash[0] + '=' + hash[1]) === -1) {
                window.location.hash += hash[0] + '=' + hash[1] + '&';
            }
        }

        if ($(obj).prop('checked')) {
            window.location.hash += $(obj).attr('data-url') + '&';

        } else {
            hashes = window.location.hash.split($(obj).attr('data-url') + '&').join('');
            if (hashes == '#') {
                history.pushState('', document.title, window.location.pathname);
            } else {
                window.location.hash = hashes;
            }
        }

        filter_load(window.location.hash.split(']').join('][]'), obj);
    } else {

        var href = window.location.href.split('?')[1];

        if (href == undefined)
            href = '';

        var last = href.substring((href.length - 1), href.length);
        if (last != '&' && last != '')
            href += '&';

        if ($(obj).prop('checked')) {
            href += $(obj).attr('data-url').split(']').join('][]') + '&';
        } else {
            href = href.split($(obj).attr('data-url').split(']').join('][]') + '&').join('');
        }

        window.location.href = catalogFirstPage + '?' + href;
    }
}

$().ready(function () {

    // ��������� ������ ���� �������� �� �������� ��������
    $('.breadcrumb > li > a').each(function () {
        var linkHref = $(this).attr('href');
        $('.sidebarNav').each(function () {
            if ($(this).attr('data-cid') == linkHref) {
                $(this).addClass("show");

                // ������ �����
                $('[aria-controls="' + $(this).attr('id') + '"]').addClass('sidebarNavOpen');

                $('.dropdown-item').each(function () {
                    if ($(this).attr('data-id') == $('#body').attr('data-id') && $('#body').attr('data-subpath') == 'CID') {
                        $(this).addClass('text-primary');
                    }
                });
            }
        });

        $('.dropdown-nav-link').each(function () {
            if ($(this).attr('data-id') == $('#body').attr('data-id') && $('#body').attr('data-subpath') == 'CID') {
                $(this).addClass('text-primary');
                $($(this).attr('data-target')).addClass("show");
            }

            if ($(this).attr('href') == linkHref) {
                $(this).addClass('text-primary');
            }
        });

        $('.dropdown-item').each(function () {

            if ($(this).attr('href') == linkHref) {
                $(this).addClass('text-primary');
            }
        });


    });

    $(".filter-btn").on('click', function () {
        $("#faset-filter").fadeIn();
    });
    $(".filter-close").click(function () {
        $("#faset-filter").fadeOut();
    });

    $(".faset-filter-name .close").on('click', function () {
        $("#faset-filter").fadeOut();
    });


    // ������ ������ ���������� ������ 
    $("button.orderCheckButton").on("click", function (e) {
        e.preventDefault();
        OrderChekJq();
    });

    $('#price-filter-body input').on('change', function () {
        if (AJAX_SCROLL) {
            price_slider_load($('#price-filter-body input[name=min]').val(), $('#price-filter-body input[name=max]').val(), $(this));
        } else {
            $('#price-filter-form').submit();
        }
    });

    // ������� �������
    $("#slider-range").on("slidestop", function (event, ui) {

        if (AJAX_SCROLL) {

            // ����� ������� ��������
            count = current;

            price_slider_load(ui.values[ 0 ], ui.values[ 1 ]);
        } else {
            $('#price-filter-form').submit();
        }
    });

    // �������� ������
    if (FILTER && $("#sorttable table td").html()) {
        $("#faset-filter-body").html($("#sorttable table td").html());
        $("#faset-filter").removeClass('d-none');
        $('.filter-btn').addClass('visible-filter');
    } else {
        $("#faset-filter").hide();

    }

    if (!FILTER) {
        $("#faset-filter").hide();
        $("#sorttable").removeClass('d-none');
    }

    // ����������� ���������� � �������
    $('#filter-well-select-stat').on('change', function () {
        window.location.href = this.value;
    });

    // ����������� ����������
    $('#filter-well-select').on('change', function () {

        if (AJAX_SCROLL) {

            count = current;
            var hashes = window.location.hash;
            hashes = hashes.split('s=1&').join('');
            hashes = hashes.split('s=2&').join('');
            hashes = hashes.split('s=3&').join('');
            hashes = hashes.split('f=2&').join('');
            hashes = hashes.split('f=1&').join('');
            hashes += 's=' + this.value + '&';
            window.location.hash = hashes;

            filter_load(window.location.hash);
        } else {

            var href = window.location.href.split('?')[1];

            if (href == undefined)
                href = '';

            var last = href.substring((href.length - 1), href.length);
            if (last != '&' && last != '')
                href += '&';

            href = href.split('s=1&').join('');
            href = href.split('s=2&').join('');
            href = href.split('f=2&').join('');
            href = href.split('f=1&').join('');
            href += $(this).attr('name') + '=' + this.value;
            window.location.href = '?' + href;
        }

    });


    // ����������� ����������
    $('#filter-well .filter-item').on('click', function (event) {
        event.preventDefault();
        $('#filter-well .nav-link').removeClass('active');
        $(this).addClass('active');


        if (AJAX_SCROLL) {

            count = current;

            var hashes = window.location.hash;
            hashes = hashes.split($(this).attr('name') + '=1&').join('');
            hashes = hashes.split($(this).attr('name') + '=2&').join('');
            hashes += $(this).attr('name') + '=' + $(this).attr('value') + '&';
            window.location.hash = hashes;

            filter_load(window.location.hash);
        } else {

            var href = window.location.href.split('?')[1];

            if (href == undefined)
                href = '';

            var last = href.substring((href.length - 1), href.length);
            if (last != '&' && last != '')
                href += '&';

            href = href.split($(this).attr('name') + '=1&').join('');
            href = href.split($(this).attr('name') + '=2&').join('');
            href += $(this).attr('name') + '=' + $(this).attr('value');
            window.location.href = '?' + href;
        }
    });

    // �������� ���������� ������ ��� ��������
    if (window.location.hash != "" && $("#sorttable table td").html()) {

        var filter_str = window.location.hash.split(']').join('][]');

        // ������������ ���������
        $.ajax({
            type: "POST",
            url: '?' + filter_str.split('#').join(''),
            data: {
                ajaxfilter: true
            },
            success: function (data)
            {
                if (data) {
                    $("#faset-filter-body").html(data);
                    $("#faset-filter-body").html($("#faset-filter-body").find('td').html());

                    // �������� ���������� �������
                    filter_load(filter_str);
                }
            }
        });
    }

    // Ajax ������
    $('#faset-filter-body').on('change', 'input:checkbox', function () {

        // ����� ������� ��������
        count = current;
        faset_filter_click($(this));
    });


    // ����� �������
    $('#faset-filter-reset').on('click', function (event) {
        if (AJAX_SCROLL) {
            event.preventDefault();
            $("#faset-filter-body").html($("#sorttable table td").html());
            filter_load('');
            history.pushState('', document.title, window.location.pathname);
            $.removeCookie('slider-range-min');
            $.removeCookie('slider-range-max');
            $(".pagination").show();

            // ����� ������� ��������
            count = current;

            // ����� ��������
            var max = $("#slider-range").slider("option", "max");
            var min = $("#slider-range").slider("option", "min");
            $('#price-filter-body input[name=min]').val(max);
            $('#price-filter-body input[name=max]').val(min);
            $("#slider-range").slider({
                values: [min, max]
            });
        }

    });

    // ��������� �������
    $('.pagination a').on('click', function (event) {
        if (AJAX_SCROLL) {
            event.preventDefault();
            window.location.href = $(this).attr('href') + window.location.hash;
        }
    });

    // ������� �������
    $(document).on('keydown', function (e) {
        if (e == null) {
            key = event.keyCode;
            var ctrl = event.ctrlKey;
        } else {
            key = e.which;
            var ctrl = e.ctrlKey;
        }
        if ((key == '123') && ctrl)
            window.location.replace(ROOT_PATH + '/phpshop/admpanel/');
        if (key == '120') {
            $.ajax({
                url: ROOT_PATH + '/phpshop/ajax/info.php',
                type: 'post',
                data: 'type=json',
                dataType: 'json',
                success: function (json) {
                    if (json['success']) {
                        confirm(json['info']);
                    }
                }
            });
        }
    });

    // ����� �������� ������
    $(".cat-menu-search").on('click', function () {
        $('#cat').val($(this).attr('data-target'));
        $('#catSearchSelect').html($(this).html());
    });


    $('.comment-more').bind('click', function (event) {
        event.preventDefault();
        $('#commentList .media:nth-child(n+3)').fadeToggle();
        $(this).toggleClass('click');
        $(this).toggleClass('hide-click');
        $('.comment-more.click').text('������');
        $('.comment-more.hide-click').text('�������� ���');
    });

    // ������� ������ �������� ���������� ��������
    if ($('#files').html() != '��� ������')
        $('#filesTab').addClass('show');

    if ($('#vendorenabled').html() != '')
        $('#settingsTab').addClass('show');

    if ($('#pages').html() != '')
        $('#pagesTab').addClass('show');

    // ������� ���� �������
    if (BRAND_MENU === false) {
        $('#brand-menu').hide();
    }

    // ���������� � �������
    $('body').on('click', '.addToCartList', function (e) {
        e.preventDefault();
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'));
        $(this).attr('disabled', 'disabled');
        $(this).addClass('btn-soft-primary');
        $(this).text(locale.incart);
        $('#order').removeClass('d-none');
    });

    // ��������� ���������� ������ ��� ���������� � �������
    $('body').on('change', '.addToCartListNum', function () {
        var num = (Number($(this).val()) || 1);
        var id = $(this).attr('data-uid');
        if (num > 0) {
            $(".addToCartList").each(function () {
                if ($(this).attr('data-uid') === id)
                    $('.addToCartList[data-uid=' + id + ']').attr('data-num', num);
            });
        }

    });

    // ���������� � ������� �������
    $(".addToCartListParent").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-parent'));
        $('[itemprop="price"]').html($(this).attr('data-price'));
    });

    // ���������� � ������� �����
    $(".addToCartListOption").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-uid'), $('#allOptionsSet' + $(this).attr('data-uid')).val());
    });

    // ���������� � wishlist
    $('body').on('click', '.addToWishList', function (event) {
        event.preventDefault();
        addToWishList($(this).attr('data-uid'));
        $(".wishlistcount").removeClass('d-none');
        $(this).addClass('active');
    });

    // ���������� � compare
    $('body').on('click', '.addToCompareList', function (event) {
        event.preventDefault();
        addToCompareList($(this).attr('data-uid'));
    });

    // �������� ��������� �������������� �� ������� ��������
    $("#CheckMessage").on('click', function () {
        if ($("#message").val() != '')
            $("#forma_message").submit();
    });

    // ��������� 
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

    // ������� �� ������ �� ����� � ���������
    $('#price-form').on('click', function (event) {
        event.preventDefault();
        if ($(this).attr('data-uid') != "" && $(this).attr('data-uid') != "ALL") {
            window.location.replace("../shop/CID_" + $(this).attr('data-uid') + ".html");
        }
    });

    // Ajax �����
    $(".search-slide-down-suggestions").hide();
    $("#searchSlideDownControl").on('input', function () {
        var words = $(this).val();
        if (words.length > 2) {
            $.ajax({
                type: "POST",
                url: ROOT_PATH + "/search/",
                data: {
                    words: escape(words),
                    set: 2,
                    ajax: true
                },
                success: function (data)
                {

                    // ��������� ������
                    if (data != 'false') {

                        if (data != $("#searchSlideDownContent").html()) {
                            $("#searchSlideDownContent").html(data);
                            $(".search-slide-down-suggestions").show();
                        }
                    } else {
                        $("#searchSlideDownContent").html(null);
                        $(".search-slide-down-suggestions").hide();
                    }
                }
            });
        } else {
            $("#searchSlideDownContent").html(null);
            $(".search-slide-down-suggestions").hide();
        }
    });

    // ���������� ������
    $(".btn-default").addClass('btn-primary').addClass('transition-3d-hover');

    // ����� ����� ������ ���-�� �����
    $(".modal-nowBuy").fadeIn(2000).delay(7000).fadeOut(1000);

    // ������� ���� ������ ���-�� �����
    $('.nowbuy-close').on('click', function (e) {
        e.preventDefault();
        $('.modal-nowBuy').addClass('d-none');
        $.cookie('nowbuy_close', 1, {
            path: '/',
            expires: 24
        });
    });

    // ������ ����� ��������
    $("form[name='forma_order'], input[name=returncall_mod_tel],input[name=tel],input[name=tel_new],input[name=oneclick_mod_tel]").on("click", function () {
        if (PHONE_FORMAT && PHONE_MASK) {
            $("input[name=tel_new], input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]").mask(PHONE_MASK);
        }
    });

    // Title
    $('.shop-page-main-title').text($('.page-title').text());

    // ���������� � ������� ��������� ��������
    $("body").on('click', ".addToCartFull", function () {

        // ������
        if ($('#parentSizeMessage').html()) {

            // ������
            if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                addToCartList($('input[name="parentSize"]:checked').val(), $('input[name="quant[2]"]').val(), $('input[name="parentSize"]:checked').attr('data-parent'));
                $(this).text(locale.incart);
            }
            // ������  � ����
            else if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {

                var color = $('input[name="parentColor"]:checked').attr('data-color');
                var size = $('input[name="parentSize"]:checked').attr('data-name');
                var parent = $('input[name="parentColor"]:checked').attr('data-parent');

                $.ajax({
                    url: ROOT_PATH + '/phpshop/ajax/option.php',
                    type: 'post',
                    data: 'color=' + escape(color) + '&parent=' + parent + '&size=' + escape(size),
                    dataType: 'json',
                    success: function (json) {
                        if (json['id'] > 0) {
                            if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {
                                addToCartList(json['id'], $('input[name="quant[2]"]').val(), $('input[name="parentColor"]:checked').attr('data-parent'));
                                $(this).text(locale.incart);
                            } else
                                showAlertMessage($('#parentSizeMessage').html());
                        }
                    }
                });
            } else
                showAlertMessage($('#parentSizeMessage').html());
        }
        // ����� ��������������
        else if ($('#optionMessage').html()) {
            var optionCheck = true;
            var optionValue = $('#allOptionsSet' + $(this).attr('data-uid')).val();
            $('.optionsDisp select').each(function () {
                if ($(this).hasClass('req') && optionValue === '')
                    optionCheck = false;
            });

            if (optionCheck) {
                addToCartList($(this).attr('data-uid'), $('input[name="quant[2]"]').val(), $(this).attr('data-uid'), optionValue);
                $(this).text(locale.incart);
            } else
                showAlertMessage($('#optionMessage').html());
        }
        // ������� �����
        else {
            addToCartList($(this).attr('data-uid'), $('input[name="quant[1]"]').val());
            $(this).text(locale.incart);
        }

    });

    // ����� ����� 
    $('body').on('change', 'input[name="parentColor"]', function () {

        $('input[name="parentColor"]').each(function () {
            this.checked = false;
            $(this).parent('label').removeClass('label_active');
        });

        this.checked = true;
        $(this).parent('label').addClass('label_active');


        var color = $('input[name="parentColor"]:checked').attr('data-color');
        var size = $('input[name="parentSize"]:checked').attr('data-name');
        var parent = $('input[name="parentColor"]:checked').attr('data-parent');

        $.ajax({
            url: ROOT_PATH + '/phpshop/ajax/option.php',
            type: 'post',
            data: 'color=' + escape(color) + '&parent=' + parent + '&size=' + escape(size),
            dataType: 'json',
            success: function (json) {
                if (json['id'] > 0) {

                    // ����� ����
                    $('[itemprop="price"]').html(json['price']);

                    // ����� ������ ����
                    if (json['price_n'] != "")
                        $('[itemscope] .price-old').html(json['price_n'] + '<span class="rubznak">' + $('[itemprop="priceCurrency"]').html() + '</span>');
                    else
                        $('[itemscope] .price-old').html('');

                    // ����� ��������
                    var parent_img = json['image_big'];
                    if (parent_img != "") {

                        $(".js-slide").each(function (index, el) {
                            if ($(this).attr('data-big-image') == parent_img) {
                                $('#heroSlider').slick('slickGoTo', index);
                            }

                        });
                    }

                    // ����� ������
                    $('#items').html(json['items']);

                }
            }
        });

    });

    // ����� �������
    $('body').on('change', 'input[name="parentSize"]', function () {
        var id = this.value;

        $('input[name="parentSize"]').each(function () {
            this.checked = false;
            $(this).parent('label').removeClass('label_active');
        });

        this.checked = true;
        $(this).parent('label').addClass('label_active');

        // ���� ��� ������ ������ ����� ���� � ��������
        if ($('input[name="parentColor"]').val() === undefined) {

            // ����� ����
            $('[itemprop="price"]').html($(this).attr('data-price'));

            // ����� ������ ����
            if ($(this).attr('data-priceold') != "")
                $('[itemscope] .price-old').html($(this).attr('data-priceold') + '<span class=rubznak>' + $('[itemprop="priceCurrency"]').html() + '</span>');
            else
                $('[itemscope] .price-old').html('');

            // ����� ��������
            var parent_img = $(this).attr('data-image');
            if (parent_img != "") {

                $(".js-slide").each(function (index, el) {
                    if ($(this).attr('data-big-image') == parent_img) {
                        $('#heroSlider').slick('slickGoTo', index);
                    }

                });
            }

            // ����� ������
            $('#items').html($(this).attr('data-items'));
        }

        $('.selectCartParentColor').each(function () {
            $(this).parent('label').removeClass('label_active');
            if ($(this).hasClass('select-color-' + id)) {
                $(this).parent('label').removeClass('not-active');
                $(this).parent('label').attr('title', $(this).attr('data-color'));

                $(this).val(id);
            } else {
                $(this).parent('label').addClass('not-active');
                $(this).parent('label').attr('title', '���');
            }
        });
    });


    // plugin bootstrap minus and plus
    $('.btn-number').click(function (e) {
        e.preventDefault();

        fieldName = $(this).attr('data-field');
        type = $(this).attr('data-type');
        var input = $("input[name='" + fieldName + "']");
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if (type == 'minus') {

                if (currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change();
                }
                if (parseInt(input.val()) == input.attr('min')) {
                    $(this).attr('disabled', true);
                }

            } else if (type == 'plus') {

                if (currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change();
                }
                if (parseInt(input.val()) == input.attr('max')) {
                    $(this).attr('disabled', true);
                }

            }
        } else {
            input.val(0);
        }
    });


    // ��������� DaData.ru
    var DADATA_TOKEN = $('#body').attr('data-token');
    if (DADATA_TOKEN) {

        $('[name="name_new"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="name_person"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="oneclick_mod_name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[name="returncall_mod_name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "NAME",
            params: {
                parts: ["NAME"]
            },
            count: 5
        });
        $('[type="email"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "EMAIL",
            suggest_local: false,
            count: 5
        });
        $('[name="org_name"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "PARTY",
            count: 5
        });
        $('[name="company"]').suggestions({
            token: DADATA_TOKEN,
            partner: "PHPSHOP",
            type: "PARTY",
            count: 5
        });
    }

    //  �������� �� ������������� cookie
    $('.cookie-message a').on('click', function (e) {
        e.preventDefault();
        $.cookie('usecookie', 1, {
            path: '/',
            expires: 365
        });
        $(this).parent().slideToggle("slow");
    });
    var usecookie = $.cookie('usecookie');
    if (usecookie == undefined && COOKIE_AGREEMENT) {
        $('.cookie-message p').html(locale.cookie_message);
        $('.cookie-message').removeClass('d-none');
    }

    $('.rating-group .btn').bind(' mouseover click', function () {
        $(this).prevAll('.btn').addClass('hover');
        $(this).addClass('hover');
        $(this).nextAll('.btn').removeClass('hover');

    });

    $('.rating-group .btn').on('click', function () {
        $(this).addClass('active');
    });

    $('form[name="ajax-form"]').on('submit', function (e) {
        e.preventDefault();
        var modalId = $(this).attr('data-modal');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (data) {
                if (modalId) {
                    $('#' + modalId).modal('toggle');
                    $('#thanks-box ').modal('show');
                    $("#thanks-box .modal-body").html(data['message']);
                }
            }
        });
    });

// CAPTCHA
    $('body').on('click', '[data-toggle="modal"]', function () {

        // reCAPTCHA
        var oneclick = $($(this).attr('data-target')).find('#recaptcha_oneclick').get(0);
        var returncall = $($(this).attr('data-target')).find('#recaptcha_returncall').get(0);
        var notice = $($(this).attr('data-target')).find('#recaptcha_notice').get(0);
        var pricemail = $($(this).attr('data-target')).find('#recaptcha_pricemail').get(0);
        var review = $($(this).attr('data-target')).find('#recaptcha_review').get(0);
        var forma = $($(this).attr('data-target')).find('#recaptcha_forma').get(0);

        if (typeof oneclick !== "undefined" || typeof returncall !== "undefined" || typeof notice !== "undefined" || typeof pricemail !== "undefined" || typeof review !== "undefined" || typeof forma !== "undefined") {
            $.getScript("https://www.google.com/recaptcha/api.js?render=explicit")
                    .done(function () {
                        if (typeof grecaptcha !== "undefined") {

                            grecaptcha.ready(function () {
                                try {
                                    if (returncall)
                                        grecaptcha.render(returncall, {"sitekey": $(returncall).attr('data-key'), "size": $(returncall).attr('data-size')});
                                    if (oneclick)
                                        grecaptcha.render(oneclick, {"sitekey": $(oneclick).attr('data-key'), "size": $(oneclick).attr('data-size')});
                                    if (notice)
                                        grecaptcha.render(notice, {"sitekey": $(notice).attr('data-key'), "size": $(notice).attr('data-size')});
                                    if (pricemail)
                                        grecaptcha.render(pricemail, {"sitekey": $(pricemail).attr('data-key'), "size": $(pricemail).attr('data-size')});
                                    if (review)
                                        grecaptcha.render(review, {"sitekey": $(review).attr('data-key'), "size": $(review).attr('data-size')});
                                    if (forma)
                                        grecaptcha.render(forma, {"sitekey": $(forma).attr('data-key'), "size": $(forma).attr('data-size')});
                                } catch (e) {
                                }
                            });
                        }
                    });
        } else {

            // hCAPTCHA
            var oneclick = $($(this).attr('data-target')).find('#hcaptcha_oneclick').get(0);
            var returncall = $($(this).attr('data-target')).find('#hcaptcha_returncall').get(0);
            var notice = $($(this).attr('data-target')).find('#hcaptcha_notice').get(0);
            var pricemail = $($(this).attr('data-target')).find('#hcaptcha_pricemail').get(0);
            var review = $($(this).attr('data-target')).find('#hcaptcha_review').get(0);
            var forma = $($(this).attr('data-target')).find('#hcaptcha_forma').get(0);

            if (typeof oneclick !== "undefined" || typeof returncall !== "undefined" || typeof notice !== "undefined" || typeof pricemail !== "undefined" || typeof review !== "undefined" || typeof forma !== "undefined") {

                $.getScript("https://js.hcaptcha.com/1/api.js?render=explicit")
                        .done(function () {
                            if (typeof hcaptcha !== "undefined") {
                                try {
                                    if (returncall)
                                        hcaptcha.render(returncall, {"sitekey": $(returncall).attr('data-key'), "size": $(returncall).attr('data-size')});
                                    if (oneclick)
                                        hcaptcha.render(oneclick, {"sitekey": $(oneclick).attr('data-key'), "size": $(oneclick).attr('data-size')});
                                    if (notice)
                                        hcaptcha.render(notice, {"sitekey": $(notice).attr('data-key'), "size": $(notice).attr('data-size')});
                                    if (pricemail)
                                        hcaptcha.render(pricemail, {"sitekey": $(pricemail).attr('data-key'), "size": $(pricemail).attr('data-size')});
                                    if (review)
                                        hcaptcha.render(review, {"sitekey": $(review).attr('data-key'), "size": $(review).attr('data-size')});
                                    if (forma)
                                        hcaptcha.render(forma, {"sitekey": $(forma).attr('data-key'), "size": $(forma).attr('data-size')});
                                } catch (e) {
                                }

                            }
                        });
            }
        }
    });

// Recaptcha
    if ($("#recaptcha_default").length) {
        $.getScript("https://www.google.com/recaptcha/api.js?render=explicit")
                .done(function () {
                    if (typeof grecaptcha !== "undefined") {
                        grecaptcha.ready(function () {

                            if ($("#recaptcha_default").length)
                                grecaptcha.render("recaptcha_default", {"sitekey": $("#recaptcha_default").attr('data-key'), "size": $("#recaptcha_default").attr('data-size')});

                        });
                    }
                });
    }

// Hcaptcha
    if ($("#hcaptcha_default").length) {
        $.getScript("https://js.hcaptcha.com/1/api.js?render=explicit")
                .done(function () {
                    if (typeof hcaptcha !== "undefined") {
                        if ($("#hcaptcha_default").length) {
                            hcaptcha.render("hcaptcha_default", {"sitekey": $("#hcaptcha_default").attr('data-key'), "size": $("#hcaptcha_default").attr('data-size')});
                        }
                    }
                });
    }


    // ������� ������ � �����
    $('.sticker-close').on('click', function (e) {
        e.preventDefault();
        $(".top-banner").remove();
        $.cookie('sticker_close', 1, {
            path: '/',
            expires: 365
        });
    });


    $(".swiper-container > .swiper-wrapper > div").addClass("swiper-slide");

    if ($(".swiper-container").length) {
        var swiper5 = new Swiper(".compare-slider", {
            slidesPerView: 3,
            speed: 800,
            nextButton: ".btn-next10",
            prevButton: ".btn-prev10",
            preventClicks: false,
            effect: "slide",

            preventClicksPropagation: false,
            breakpoints: {
                450: {
                    slidesPerView: 2
                },
                610: {
                    slidesPerView: 2
                },
                850: {
                    slidesPerView: 3
                },
                1000: {
                    slidesPerView: 4
                },
                1080: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 3
                },
                1500: {
                    slidesPerView: 3
                }
            }
        });
    }

    // ��������� ������
    $('#mobile-filter').on('click', function (e) {
        e.preventDefault();
        $("#faset-filter-body").appendTo('#mobile-filter-wrapper');
    });

    // ��������� �����������
    if ($('#usersError').html()) {
        $('input[name=login],input[name=password]').addClass('is-invalid');
        $('#signupModal').modal('show');
    }

});