
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
        data: 'xid=' + product_id + '&num=' + num + '&type=json&addname=' + addname + '&xxid=' + parent,
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                showAlertMessage(json['message']);
                $("#num, #mobilnum").html(json['num']);
                $("#sum").html(json['sum']);
                $("#bar-cart, #order").addClass('active');
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
                $("#numcompare").html(json['num']);
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

// ���������� ������
$(".ok").addClass('btn btn-default btn-sm');
$("input:button").addClass('btn btn-default btn-sm');
$("input:submit").addClass('btn btn-primary');
$("input:text,input:password, textarea").addClass('form-control');


// �������� ������
function ButOn(Id) {
    Id.className = 'imgOn';
}

function ButOff(Id) {
    Id.className = 'imgOff';
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

// �������� ������� ����� ��������, ������ ��������
function NoFoto2(obj) {
    obj.height = 0;
    obj.width = 0;
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
            $(".template-product-list").html(data['products']);
            $('#price-filter-val-max').removeClass('has-error');
            $('#price-filter-val-min').removeClass('has-error');

            // ������������ ����� ������
            setEqualHeight(".product-description");
            setEqualHeight('.product-name-fix');
            setEqualHeight('.owl-item .product-block');
            setTimeout(function () {
                setEqualHeight('.template-product-list div:not(.product-col-1) .product-block-top ');
            }, 400);
            setEqualHeight('.product-block-price');
            setEqualHeight(".product-sklad-list-block");
            setEqualHeight('.product-block-wrapper .description-content');
            setEqualHeight(".product-block-wrapper:not(.product-col-1)");

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

            // lazyLoad
            setTimeout(function () {
                $(window).lazyLoadXT();
            }, 50);

            $("#pagination-block").html(data['pagination']);

            // ����� Waypoint
            Waypoint.refreshAll();
        },
        error: function (data) {
            $(obj).attr('checked', false);
            //$(obj).attr('disabled', true);

            if ($(obj).attr('name') == 'max')
                $('#price-filter-val-max').addClass('has-error');
            if ($(obj).attr('name') == 'min')
                $('#price-filter-val-min').addClass('has-error');

            window.location.hash = window.location.hash.split($(obj).attr('data-url') + '&').join('');
        }


    });
}

// ������� �������
function price_slider_load(min, max, obj) {

    if (Number(min) === 0) {
        min = 1;
    }

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
            if (hashes == '#')
                history.pushState('', document.title, window.location.pathname);
            else
                window.location.hash = hashes;
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

// ������������ ����� ������
function setEqualHeight(columns) {

    $(columns).closest('.row ').each(function () {
        var tallestcolumn = 0;

        $(this).find(columns).each(function () {
            var currentHeight = $(this).height();
            if (currentHeight > tallestcolumn) {
                tallestcolumn = currentHeight;
            }
        });

        if (tallestcolumn > 0) {
            $(this).find(columns).css('min-height', tallestcolumn);
        }
    });

}

function fasetFilterFix() {
    $('#faset-filter-body').on('click', 'h4', function () {
        if ($(this).parents('.faset-filter-block-wrapper').hasClass('active')) {
            $(this).parents('.faset-filter-block-wrapper').removeClass('active');
        } else {
            $(this).parents('.faset-filter-block-wrapper').addClass('active');
        }
    });
    $('.filter-title').on('click', function () {
        if ($('.filter-body-fix').hasClass('active')) {
            $('.filter-body-fix').removeClass('active');
        } else {
            $('.filter-body-fix').addClass('active');
            $('.filter-body-fix').addClass('active');
        }
    });
    $('#price-filter-body h4').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('#price-filter-form').removeClass('active');
            $('#price-filter-body').removeClass('active');
        } else {
            $(this).addClass('active');
            $('#price-filter-form').addClass('active');
            $('#price-filter-body').addClass('active');
        }
    });
}
function productPageSelect() {
    $('.table-optionsDisp select').each(function () {
        var selectID = $(this).attr('id');
        $('.product-page-option-wrapper').append('<div class="product-page-select ' + selectID + '""></div>')
        $(this).children('option').each(function () {
            var optionValue = $(this).attr('value');
            var optionHtml = $(this).html();
            $('.' + selectID + '').append('<div class="select-option" value="' + optionValue + '">' + optionHtml + '</div>')
        });
    });
    $('.product-page-option-wrapper .select-option:first-child').each(function () {
        $(this).addClass('noactive');
        var optionName = $(this).text();
        var optionNameFix = '�������� ' + optionName;
        $(this).text(optionNameFix);
    });

    $('.select-option').on('click', function () {
        if ($(this).hasClass('noactive')) {

        } else {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                var optionInputValue = [];
                $('.product-page-select .select-option.active').each(function () {
                    optionInputValue.unshift($(this).attr('value'));
                });
                var optionInputNewValue = optionInputValue.join();
                $('.product-page-option-wrapper input').attr('value', optionInputNewValue);
            } else {

                $(this).siblings().removeClass('active');
                $(this).addClass('active');
                var optionInputValue = [];
                $('.product-page-select .select-option.active').each(function () {
                    optionInputValue.unshift($(this).attr('value'));
                });
                var optionInputNewValue = optionInputValue.join('');
                $('.product-page-option-wrapper input').attr('value', optionInputNewValue);
            }
        }

    });
}
function pageTitleFix() {
    var titleText = $('.page-title').text();
    var titleTextProduct = $('.product-name').text();
    if ($('.page-title').hasClass('product-name')) {
        $('.shop-page-main-title').text(titleTextProduct);
    } else {
        $('.shop-page-main-title').text(titleText);
        $('.page-title').remove();
    }
}
function searchOpen() {
    $('.search-open-button').on('click', function () {
        $('.header-search-form').addClass("active");
    });
    $('.search-close.feather').on('click', function () {
        $('.header-search-form').removeClass("active");
        $('.header-search-form').trigger("reset");
        $("#search").popover('hide');
    });
}
function produtcPageQuantNumberFix() {
    $('.quant-parrent').appendTo('.product-page-input-number');
    $('.quant-parrent').removeClass('hide');
}
function breadcrumbsFix() {
    var breadcrumbContainerWidth = $('.secondary-nav .container').width();
    var titleWidth = $('.secondary-nav .shop-page-main-title').width();
    var breadcrumbWidth = $('.secondary-nav .breadcrumb').width();

    var allWidth = titleWidth + breadcrumbWidth;

    if (allWidth >= breadcrumbContainerWidth) {
        $('.secondary-nav .shop-page-main-title, .secondary-nav .breadcrumb').addClass('active');
    }
}
function mainPageProductSlider() {
    $('.owl-carousel .product-block-wrapper').unwrap();
    $('.spec-main').owlCarousel({
        margin: 10,
        nav: true,
        lazyLoad: true,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 5
            }
        }
    });
    $('.nowBuy').owlCarousel({
        margin: 10,
        nav: true,
        lazyLoad: true,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 5
            }
        }
    });
    $('.spec-main-icon').owlCarousel({
        margin: 10,
        nav: true,
        lazyLoad: true,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 5
            }
        }
    });
}
function mainNavMenuFix() {
    var body_width = $('body').width();
    if (body_width > 768) {
        var nav_weight = $('.main-navbar-top').width();
        var full_weight = 0;
        $('.header-menu-wrapper .main-navbar-top > li').each(function () {
            full_weight += $(this).innerWidth();
        });
        var menu_content = ('<div id="menu-fix" class="additional-nav-menu"><a href="#" class="dropdown-toggle link" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i></a><ul class="dropdown-menu dropdown-menu-right aditional-link" role="menu"></ul></div>');
        if ($('.header-menu-wrapper').find('.additional-nav-menu')) {
            var nav_weight_fix = nav_weight - 74;
        }

        if (nav_weight < full_weight) {
            var nav_weight_fix = nav_weight - 74;
            if (nav_weight_fix < 0) {
                nav_weight_fix = 0;
            }
            if ($('.header-menu-wrapper').find('#menu-fix')) {
                if ($('.header-menu-wrapper').find('#menu-fix').length == 0) {
                    $('.header-menu-wrapper > .row').append(menu_content);
                }

                if ($('.main-navbar-top').hasClass('fix')) {

                } else {
                    $('.main-navbar-top').addClass('fix');
                }
            }
            while (nav_weight_fix < full_weight) {
                $('.main-navbar-top > li:last-child').appendTo('.aditional-link');
                var full_weight = 0;
                $('.header-menu-wrapper .main-navbar-top > li').each(function () {
                    full_weight += $(this).width();
                });
            }
        }
        $('.main-navbar-top').addClass('active');
    }
}

$(document).ajaxStop(function () {

    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

});


$(document).ready(function () {
    $('.header-menu-wrapper li').removeClass('active')
    setEqualHeight(".prod-title");
    setEqualHeight(".prod-photo");
    setEqualHeight(".product-name");
    setEqualHeight(".prod-desc");
    setEqualHeight(".prod-sort");
    if ($(".carousel-inner .item+.item").length) {

        $(".carousel-control, .carousel-indicators").css("visibility", "visible")
    }
    $('#sliderModal').on('show.bs.modal', function () {
        $('.modal .modal-body').css('overflow-y', 'hidden');
        setTimeout(function () {


            $('.modal .modal-body img').css("max-height", $(window).height() * 0.66);
            $('.modal .modal-body .bx-viewport').css("max-height", $(window).height() * 0.66);
            $('.modal .modal-body .bx-viewport').css("opacity", "1")

        }, 600);
        $('.modal .modal-body').css('max-height', $(window).height() * 0.8);
    });
    mainNavMenuFix();
    mainPageProductSlider();
    searchOpen();
    setEqualHeight('.product-name-fix');
    setEqualHeight('.owl-item .product-block, .template-product-list .product-block ');
    setEqualHeight('.product-block-price');
    setEqualHeight(".product-sklad-list-block");
    setEqualHeight('.product-block-wrapper .description-content');
    produtcPageQuantNumberFix();
    productPageSelect();
    pageTitleFix();
    breadcrumbsFix();

    // ������ ������ ���������� ������ 
    $("button.orderCheckButton").on("click", function (e) {
        e.preventDefault();
        OrderChekJq();
    });

    $('.show-shop-description').on('click', function () {
        if ($('.shop-description .description-text').hasClass('active')) {
            $('.shop-description .description-text').removeClass('active');
            $('.show-shop-description').removeClass('iconz-chevron-up');
            $('.show-shop-description').addClass('iconz-chevron-down');
        } else {
            $('.shop-description .description-text').addClass('active');
            $('.show-shop-description').removeClass('iconz-chevron-down');
            $('.show-shop-description').addClass('iconz-chevron-up');

        }
    });

    // ������������� ������ ����
    $('.mega-more-parent').each(function () {
        if ($(this).hasClass('hide') || $(this).hasClass('hidden'))
            $(this).prev().removeClass('template-menu-line');
    });

    // ����� ���� ��������� � ��������
    $('.mega-more').on('click', function (event) {
        event.preventDefault();
        $(this).hide();
        $(this).closest('.mega-menu-block').find('.template-menu-line').removeClass('hide');
    });


    // ����������� ���������� � �������
    $('#filter-selection-well input:radio').on('change', function () {
        window.location.href = $(this).attr('data-url');
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
        $("#faset-filter").removeClass('hide');
    } else {

        $("#faset-filter").hide();
    }

    if (!FILTER) {
        $("#faset-filter").hide();
        $("#sorttable").removeClass('hide');
    }

    // ����������� ����������
    $('#filter-well input:radio').on('change', function () {
        if (AJAX_SCROLL) {

            count = current;
            var hashes = window.location.hash;
            hashes = hashes.split('s=1&').join('');
            hashes = hashes.split('s=2&').join('');
            hashes = hashes.split('s=3&').join('');
            hashes = hashes.split('f=2&').join('');
            hashes = hashes.split('f=1&').join('');
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
            $("#slider-range").slider("option", "values", [price_min, price_max]);

            // ����� ������� ��������
            count = current;
            $('#faset-filter-body > .faset-filter-block-wrapper:first-child').addClass('active');
        }
    });

    // ��������� �������
    $('.pagination a').on('click', function (event) {
        if (AJAX_SCROLL) {
            event.preventDefault();
            window.location.href = $(this).attr('href') + window.location.hash;
        }
    });

    // toTop
    $('#toTop').on('click', function (event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: $("header").offset().top - 100}, 500);
    });

    // ����������� ���������
    /*$('.breadcrumb, .slider').waypoint(function() {
     if (FIXED_NAVBAR){
     $('#navigation').toggleClass('navbar-fixed-top');
     }
     
     // toTop          
     $('#toTop').fadeToggle();
     });*/

    // ������� �������
    $(document).on('keydown', function (e) {
        if (e == null) { // ie
            key = event.keyCode;
            var ctrl = event.ctrlKey;
        } else { // mozilla
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


    // ������ �������� �����������
    $('.image').on('error', function () {
        $(this).attr('src', '/phpshop/templates/unishop/images/shop/no_photo.gif');
        return true;
    });

    // ��������� ������������
    $("body").on('click', '#commentLoad', function () {
        commentList($(this).attr('data-uid'), 'list');
    });

    /*
     if ($('#vendorActionButton').val() == '���������') {
     $('#sorttable').addClass('show');
     }*/

    // ������ � �������� ���� ���������
    if (MEGA_MENU_ICON === false) {
        $('.mega-menu-block img').hide();
    }

    // ������� ���� �������
    if (BRAND_MENU === false) {
        $('#brand-menu').hide();
    }

    if (CATALOG_MENU === false) {
        $('#catalog-menu').hide();
    } else {
        $('#catalog-menu').removeClass('hide');
    }

    // ���������� � �������
    $('body').on('click', '.addToCartList', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'));
        $(this).attr('disabled', 'disabled');
        $(this).addClass('btn-success');
        $('#order').addClass('active');
        if ($(this).attr('data-cart') !== undefined)
            $(this).find('.fa-shopping-cart').next('span').text($(this).attr('data-cart'));
    });

    // ��������� ���������� ������ ��� ���������� � �������
    $('body').on('change', '.addToCartListNum', function () {
        var num = (Number($(this).val()) || 1);
        var id = $(this).attr('data-uid');
        /*
         if (num > 0 && $('.addToCartList').attr('data-uid') === $(this).attr('data-uid'))
         $('.addToCartList').attr('data-num', num);*/
        if (num > 0) {
            $(".addToCartList").each(function () {
                if ($(this).attr('data-uid') === id)
                    $('.addToCartList[data-uid=' + id + ']').attr('data-num', num);
            });
        }

    });

    // ���������� � ������� �������
    $(".addToCartListParent").on('click', function () {
        addToCartList($('input[name="parentIdNt"]:checked').val(), $(this).attr('data-num'), $(this).attr('data-parent'));

    });

    $('input[name="parentIdNt"]').on('change', function () {
        $('[itemprop="price"]').html($(this).attr('data-price'));
    });

    // ���������� � ������� �����
    $(".addToCartListOption").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-uid'), $('#allOptionsSet' + $(this).attr('data-uid')).val());
    });

    // ���������� � wishlist
    $('body').on('click', '.addToWishList', function () {
        addToWishList($(this).attr('data-uid'));
    });

    // ���������� � compare
    $('body').on('click', '.addToCompareList', function () {
        addToCompareList($(this).attr('data-uid'));
    });

    // �������� ��������� �������������� �� ������� ��������
    $("#CheckMessage").on('click', function () {
        if ($("#message").val() != '')
            $("#forma_message").submit();
    });

    // ���������� �������
    $("#cartlink").on('click', function () {
        if ($(this).attr('data-content') == "") {
            window.location.href = $('#body').attr('data-dir') + '/order/';
        }
    });

    $('[data-toggle="popover"]').popover();
    $('a[data-toggle="popover"]').on('show.bs.popover', function () {
        $('a[data-toggle="popover"]').attr('data-content', $("#visualcart_tmp").html());
    });

    // ��������� 
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

    // ���������� select
    $('.selectpicker').selectpicker({
        width: "auto"
    });

    // ������� �� ������ �� ����� � ���������
    $('#price-form').on('click', function (event) {
        event.preventDefault();
        if ($(this).attr('data-uid') != "" && $(this).attr('data-uid') != "ALL")
            window.location.replace("../shop/CID_" + $(this).attr('data-uid') + ".html");
    });

    // Ajax �����
    $("#search").on('input', function () {
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

                        if (data != $("#search").attr('data-content')) {
                            $("#search").attr('data-content', data);

                            $("#search").popover('show');
                        }
                    } else
                        $("#search").popover('hide');
                }
            });
        } else {
            $("#search").attr('data-content', '');
            $("#search").popover('hide');
        }
    });

    // ��������� �����������
    if ($('#usersError').html()) {
        $('form[name=user_forma] .form-group').addClass('has-error has-feedback');
        $('form[name=user_forma] .glyphicon').removeClass('hide');
        $('#userModal').modal('show');
        $('#userModal').on('shown.bs.modal', function () {

        });
    }

    // �������� ������������ ������ �����������
    $("form[name=user_forma_register] input[name=password_new2]").on('blur', function () {
        if ($(this).val() != $("form[name=user_forma_register] input[name=password_new]").val()) {
            $('form[name=user_forma_register] #check_pass').addClass('has-error has-feedback');
            $('form[name=user_forma_register] .glyphicon').removeClass('hide');
        } else {
            $('form[name=user_forma_register] #check_pass').removeClass('has-error has-feedback');
            $('form[name=user_forma_register] .glyphicon').addClass('hide');
        }
    });

    // ����������� ������������
    $("form[name=user_forma_register]").on('submit', function () {
        if ($(this).find("input[name=password_new]").val() != $(this).find("input[name=password_new2]").val()) {
            $(this).find('#check_pass').addClass('has-error has-feedback');
            $(this).find('.glyphicon').removeClass('hide');
            return false;
        } else
            $(this).submit();
    });

    // ������ �����������
    if ($("#user_error").html()) {
        $("#user_error").find('.list-group-item').addClass('list-group-item-warning');
    }

    // ������ ����� ��������
    $("form[name='forma_order'], input[name=returncall_mod_tel],input[name=tel],input[name=tel_new],input[name=oneclick_mod_tel]").on("click", function () {
        if (PHONE_FORMAT && PHONE_MASK) {
            $("input[name=tel_new], input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]").mask(PHONE_MASK);
        }
    });

    /*
     setTimeout(function() {
     $('input[name=tel_new]').mask("+7 (999) 999-99-99");
     
     $('input[name=tel_new]').on('keyup', function(event) {
     reserveVal = $(this).cleanVal();
     phone = $(this).cleanVal().slice(0,10);
     $(this).val($(this).masked(phone));
     if($(this).cleanVal()[1] == '9') {
     if($(this).cleanVal()[0] == '8' || $(this).cleanVal()[0] == '7') {
     phone = reserveVal.slice(1);
     $(this).val($(this).masked(phone)); 
     }
     }
     });}, 2500);
     $('input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]').mask("+7 (999) 999-99-99");
     
     $('input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]').on('keyup', function(event) {
     reserveVal = $(this).cleanVal();
     phone = $(this).cleanVal().slice(0,10);
     $(this).val($(this).masked(phone));
     if($(this).cleanVal()[1] == '9') {
     if($(this).cleanVal()[0] == '8' || $(this).cleanVal()[0] == '7') {
     phone = reserveVal.slice(1);
     $(this).val($(this).masked(phone)); 
     }
     }
     })*/

    // ����������� � �� �������� ������
    if ($('.bxslider').length) {
        $('.bxslider-pre').addClass('hide');
        $('.bxslider').removeClass('hide');
        slider = $('.bxslider').bxSlider({
            mode: 'fade',
            pagerCustom: '.bx-pager'
        });
    }

    // ����������� � �� �������� ������ � �������� �������������
    $(document).on('click', '.bxslider a', function (event) {
        event.preventDefault();
        $('#sliderModal').modal('show');
        $('.bxsliderbig').html($('.bxsliderbig').attr('data-content'));

        sliderbig = $('.bxsliderbig').bxSlider({
            mode: 'fade',
            pagerCustom: '.bx-pager-big'
        });


        if ($('.bx-pager-big').length == 0) {
            $('.modal-body').append('<div class="bx-pager-big">' + $('.bxsliderbig').attr('data-page') + '</div>');
            sliderbig.reloadSlider();
        }

        sliderbig.goToSlide(slider.getCurrentSlide());

    });

    // �������� ���������� ���� �������������, ���� �� �����������
    $(document).on('click', '.bxsliderbig a', function (event) {
        event.preventDefault();
        slider.goToSlide(sliderbig.getCurrentSlide());
        $('#sliderModal').modal('hide');
    });

    // �������� ���������� ���� �������������
    $('#sliderModal').on('hide.bs.modal', function () {
        slider.goToSlide(sliderbig.getCurrentSlide());
        sliderbig.destroySlider();
        delete sliderbig;
    });

    // ������ ������ ����� � �������� ������
    $('.empty-check').each(function () {
        if ($(this).find('a').html() === undefined && $(this).find('.vendorenabled').html() === undefined) {
            $(this).fadeOut('slow');
        }
    });

    // ������������� ���� 
    $('.collapse').on('show.bs.collapse', function () {
        $(this).prev('h4').find('i').removeClass('fa-chevron-down');
        $(this).prev('h4').find('i').addClass('fa-chevron-up');
        $(this).prev('h4').attr('title', '������');
    });
    $('.collapse').on('hidden.bs.collapse', function () {
        $(this).prev('h4').find('i').removeClass('fa-chevron-up');
        $(this).prev('h4').find('i').addClass('fa-chevron-down');
        $(this).prev('h4').attr('title', '��������');
    });

    // ���������� � ������� ��������� ��������
    $("body").on('click', ".addToCartFull", function () {

        // ������
        if ($('#parentSizeMessage').html()) {

            // ������
            if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                addToCartList($('input[name="parentSize"]:checked').val(), $('input[name="quant[2]"]').val(), $('input[name="parentSize"]:checked').attr('data-parent'));
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
                            if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0)
                                addToCartList(json['id'], $('input[name="quant[2]"]').val(), $('input[name="parentColor"]:checked').attr('data-parent'));
                            else
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

            if (optionCheck)
                addToCartList($(this).attr('data-uid'), $('input[name="quant[2]"]').val(), $(this).attr('data-uid'), optionValue);
            else
                showAlertMessage($('#optionMessage').html());
        }
        // ������� �����
        else {
            addToCartList($(this).attr('data-uid'), $('input[name="quant[2]"]').val());
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

                        $(".bx-pager img").each(function (index, el) {
                            if ($(this).attr('data-big-image') == parent_img) {
                                slider.goToSlide(index);
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

                $(".bx-pager img").each(function (index, el) {
                    if ($(this).attr('src') == parent_img) {
                        slider.goToSlide(index);
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


    // plugin bootstrap minus and plus http://jsfiddle.net/laelitenetwork/puJ6G/
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

    $('#faset-filter-body > .faset-filter-block-wrapper:first-child').addClass('active');
    fasetFilterFix();
    $('.order-page-num-input-fix').removeClass('hide');

    //Odnotip List
    $('.odnotipList').appendTo('.odnotipListWrapper');
    $('.odnotipList .product-block-wrapper').unwrap();

    $(window).resize(function () {
        setEqualHeight('.product-description');

        setEqualHeight('.product-name-fix');
        setEqualHeight('.product-block');
        setEqualHeight('.product-block-price');
        setEqualHeight(".product-sklad-list-block");
        setEqualHeight('.product-block-wrapper .description-content');
        breadcrumbsFix();
        mainNavMenuFix();
    });

    // ��������� DaData.ru
    var DADATA_TOKEN = $('#body').attr('data-token');
    if (DADATA_TOKEN) {

        /*
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
         */
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

    // �������� �� ������������� cookie
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
        $('.cookie-message').removeClass('hide');
    }

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

    // ������� ������ � �����
    $('.sticker-close').on('click', function (e) {
        e.preventDefault();
        $(".top-banner").remove();
        $.cookie('sticker_close', 1, {
            path: '/',
            expires: 365
        });
    });
});

// Recaptcha
if ($("#recaptcha_default").length || $("#recaptcha_returncall").length) {

    $.getScript("https://www.google.com/recaptcha/api.js?render=explicit")
            .done(function () {
                if (typeof grecaptcha !== "undefined") {
                    grecaptcha.ready(function () {
                        if ($("#recaptcha_default").length)
                            grecaptcha.render("recaptcha_default", {"sitekey": $("#recaptcha_default").attr('data-key'), "size": $("#recaptcha_default").attr('data-size')});

                        if ($("#recaptcha_returncall").length)
                            grecaptcha.render("recaptcha_returncall", {"sitekey": $("#recaptcha_returncall").attr('data-key'), "size": $("#recaptcha_returncall").attr('data-size')});

                    });
                }
            });
}

// Hcaptcha
if ($("#hcaptcha_default").length || $("#hcaptcha_returncall").length) {
    $.getScript("https://js.hcaptcha.com/1/api.js?render=explicit")
            .done(function () {
                if (typeof hcaptcha !== "undefined") {
                    if ($("#hcaptcha_default").length)
                        hcaptcha.render("hcaptcha_default", {"sitekey": $("#hcaptcha_default").attr('data-key'), "size": $("#hcaptcha_default").attr('data-size')});
                    if ($("#hcaptcha_returncall").length)
                        hcaptcha.render("hcaptcha_returncall", {"sitekey": $("#hcaptcha_returncall").attr('data-key'), "size": $("#hcaptcha_returncall").attr('data-size')});
                }
            });
}
