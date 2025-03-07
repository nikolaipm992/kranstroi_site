// добавление товара в корзину
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
                $("#num, #mobilnum").html(json['num']);
                $("#sum").html(json['sum']);
                $(".sum").html(json['sum']);
                $("#bar-cart, #order").addClass('active');
            }
        }
    });
}

// добавление товара в корзину
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


// Фотогалерея
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

// оформление кнопок
$(".ok").addClass('btn btn-default btn-sm');
$("input:button").addClass('btn btn-default btn-sm');
$("input:submit").addClass('btn btn-primary');
$("input:text,input:password, textarea").addClass('form-control');


// Активная кнопка
function ButOn(Id) {
    Id.className = 'imgOn';
}

function ButOff(Id) {
    Id.className = 'imgOff';
}

function ChangeSkin() {
    document.SkinForm.submit();
}

// Смена валюты
function ChangeValuta() {
    document.ValutaForm.submit();
}

// Создание ссылки для сортировки
function ReturnSortUrl(v) {
    var s, url = "";
    if (v > 0) {
        s = document.getElementById(v).value;
        if (s != "")
            url = "v[" + v + "]=" + s + "&";
    }
    return url;
}

// Проверка наличия файла картинки, прячем картинку
function NoFoto2(obj) {
    obj.height = 0;
    obj.width = 0;
}

// Проверка наличия файла картинки, вставляем заглушку
function NoFoto(obj, pathTemplate) {
    obj.src = pathTemplate + '/images/shop/no_photo.gif';
}

// Сортировка по всем фильтрам
function GetSortAll() {
    var url = ROOT_PATH + "/shop/CID_" + arguments[0] + ".html?";

    var i = 1;
    var c = arguments.length;

    for (i = 1; i < c; i++)
        if (document.getElementById(arguments[i]))
            url = url + ReturnSortUrl(arguments[i]);

    location.replace(url.substring(0, (url.length - 1)) + "#sort");

}

// Инициализируем таблицу перевода на русский
var trans = [];
for (var i = 0x410; i <= 0x44F; i++)
    trans[i] = i - 0x350; // А-Яа-я
trans[0x401] = 0xA8;    // Ё
trans[0x451] = 0xB8;    // ё

// Таблица перевода на украинский
/*
 trans[0x457] = 0xBF;    // ї
 trans[0x407] = 0xAF;    // Ї
 trans[0x456] = 0xB3;    // і
 trans[0x406] = 0xB2;    // І
 trans[0x404] = 0xBA;    // є
 trans[0x454] = 0xAA;    // Є
 */

// Сохраняем стандартную функцию escape()
var escapeOrig = window.escape;

// Переопределяем функцию escape()
window.escape = function (str)
{

    if (locale.charset == 'utf-8')
        return str;

    else {
        var str = String(str);
        var ret = [];
        // Составляем массив кодов символов, попутно переводим кириллицу
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

// Ajax фильтр обновление данных
function filter_load(filter_str, obj) {

    $.ajax({
        type: "POST",
        url: '?' + filter_str.split('#').join(''),
        data: {
            ajax: true,
            json: true
        },
        success: function (data) {
            $(".template-product-list").html(data['products']);
            $('#price-filter-val-max').removeClass('has-error');
            $('#price-filter-val-min').removeClass('has-error');

            // Выравнивание ячеек товара
            setEqualHeight(".product-description");
            setEqualHeight(".product-name-fix");
            setTimeout(function () {
                setEqualHeight(".caption img");
            }, 600);
            setEqualHeight(".caption img");

                // Блокировкам пустых значений и пересчет количества фильтра
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

            // Сброс Waypoint
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
// Ценовой слайдер
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

// Ajax фильтр событие клика
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

// Выравнивание ячеек товара
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
            $(this).find(columns).height(tallestcolumn);
        }
    });
}

function productPageSelect() {
    $(".table-optionsDisp select").each(function () {
        var selectID = $(this).attr("id");
        $(".product-page-option-wrapper").append(
                '<div class="product-page-select ' + selectID + '""></div>'
                );
        $(this)
                .children("option")
                .each(function () {
                    var optionValue = $(this).attr("value");
                    var optionHtml = $(this).html();
                    $("." + selectID + "").append(
                            '<div class="select-option" value="' +
                            optionValue +
                            '">' +
                            optionHtml +
                            "</div>"
                            );
                });
    });

    $(".select-option").on("click", function () {
        if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            var optionInputValue = [];
            $(".product-page-select .select-option.active").each(function () {
                optionInputValue.unshift($(this).attr("value"));
            });
            var optionInputNewValue = optionInputValue.join();
            $(".product-page-option-wrapper input").attr(
                    "value",
                    optionInputNewValue
                    );
        } else {
            $(this)
                    .siblings()
                    .removeClass("active");
            $(this).addClass("active");
            var optionInputValue = [];
            $(".product-page-select .select-option.active").each(function () {
                optionInputValue.unshift($(this).attr("value"));
            });
            var optionInputNewValue = optionInputValue.join("");
            $(".product-page-option-wrapper input").attr(
                    "value",
                    optionInputNewValue
                    );
        }
    });
}
// Коррекция знака рубля
function setRubznak() {
    $(".rubznak").each(function () {
        if (
                $(this).html() == "р." ||
                $(this).html() == "руб" ||
                $(this).html() == "p"
                )
            $(this).html("p");
    });
}
function productFilter() {
    $('.filter-list #faset-filter').on('click', 'h4', function () {
        if ($(this).parents('.faset-filter-block-wrapper').hasClass('active')) {
            $('.faset-filter-block-wrapper').removeClass('active');
            $(this).parents('.faset-filter-block-wrapper').removeClass('active');
        } else {
            $('.faset-filter-block-wrapper').removeClass('active');
            $(this).parents('.faset-filter-block-wrapper').addClass('active');
        }
    });
}
function searchOpen() {
    $('.search-open-button').on('click', function () {
        $('.search-big-block').addClass("active");
    });
    $('.search-close').on('click', function () {
        $('.search-big-block').removeClass("active");
        $('.header-search-form').trigger("reset");
    });
}

function topMenuFix() {
    var body_width = $('body').width();
    if (body_width > 768) {
        var nav_weight = $('.top-menu-list').width();
        var top_nav_weight = $('.catalog-menu-list').width();
        var full_weight = 0;
        var top_full_weight = 0;

        $('.top-menu-list > li').each(function () {
            full_weight += ($(this).width() + 30);
        });
        $('.catalog-menu-list > li').each(function () {
            top_full_weight += ($(this).width() + 46);
        });

        var menu_content = ('<div class="additional-nav-menu"><a href="#" class="dropdown-toggle link" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i></a><ul class="dropdown-menu  aditional-link" role="menu"></ul></div>');
        var main_menu_content = ('<div class="additional-main-menu"><a href="#" class="dropdown-toggle link" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars"></i></a><ul class="dropdown-menu  main-link" role="menu"></ul></div>');

        if ($('.top-menu-block').length) {
            if ($('.top-menu-block').find('.additional-nav-menu')) {
                var nav_weight_fix = nav_weight - 26;
            }

            if (nav_weight < full_weight) {

                var nav_weight_fix = nav_weight - 26;
                if ($('.top-menu-block').find('.additional-nav-menu')) {
                    $('.top-menu-block').append(menu_content);
                }

                while (nav_weight_fix < full_weight) {

                    $('.top-menu-list > li:last-child').appendTo('.aditional-link');
                    var full_weight = 0;
                    $('.top-menu-list > li').each(function () {
                        full_weight += ($(this).width() + 30);
                    });

                }

            }
        }

        if ($('.catalog-menu-wrap').find('.additional-main-menu')) {
            var top_nav_weight_fix = top_nav_weight - 46;
        }
        if (top_nav_weight < top_full_weight) {

            var top_nav_weight_fix = top_nav_weight - 46;
            if ($('.catalog-menu-wrap').find('.additional-main-menu')) {
                $('.catalog-menu-wrap').append(main_menu_content);
            }

            while (top_nav_weight_fix < top_full_weight) {

                $('.catalog-menu-list > li:last-child').prependTo('.main-link');
                var top_full_weight = 0;
                $('.catalog-menu-list > li').each(function () {
                    top_full_weight += $(this).width() + 46;
                });
            }

        }
        $('.top-menu-list').addClass('active');
    }
}
$(document).ajaxStop(function () {
    if ($(".filter-row #faset-filter-body .grid-item").length == 2) {
        $("#faset-filter-body .grid-item").css("width", "50%")
    }
    if ($(".filter-row #faset-filter-body .grid-item").length == 1) {
        $("#faset-filter-body .grid-item").css("width", "100%")
    }
    if ($(".filter-row #faset-filter-body .grid-item").length == 3) {
        $("#faset-filter-body .grid-item").css("width", "33%")
    }
    if ($(".filter-row #faset-filter-body .grid-item").length >= 3) {
        $("#faset-filter-body .grid-item").css("width", "33%")
    }

});

$(document).ready(function () {

    if (navigator.userAgent.match(/iPad/i) != null) {
        $('.main-menu-block > li > a.sub-marker').removeAttr("href")
        $('.main-menu-block > li').on("click", function () {

            $('.hover').removeClass('hover')
            $(this).addClass('hover')
        }

        )
    }
    if (navigator.userAgent.indexOf('Mac OS X') != -1) {
        $("body").addClass("mac");
    } else {
        $("body").addClass("pc");
    }

    topMenuFix();
    searchOpen();
    productPageSelect();
    setTimeout(function () {
        $(".top-menu-list").css("opacity", "1")
        $(".catalog-menu-list").css("opacity", "1")
        $(".additional-nav-menu").css("opacity", "1")
        $(".additional-main-menu").css("opacity", "1")
    }, 600);
    setTimeout(function () {
        $("input#order_metod[disabled='disabled']").parents('.paymOneEl').addClass('disabled')
    }, 2000);
    $("#seldelivery").on("click", function () {
        //  $(".delivOneEl").css("opacity", "0");

        setTimeout(function () {
            $("input#order_metod").each(function () {
                if ($(this).attr('disabled')) {
                    $(this).parents('.paymOneEl').addClass('disabled');
                } else {
                    $(this).parents('.paymOneEl').removeClass('disabled');
                }
            });
        }, 400);
    })


    productFilter();

    // логика кнопки оформления заказа
    $("button.orderCheckButton").on("click", function (e) {
        e.preventDefault();
        OrderChekJq();
    });

    // Выравнивание ячеек товара
    setEqualHeight(".thumbnail .description");
    setEqualHeight(".prod-title");
    setEqualHeight(".prod-photo");
    setEqualHeight(".product-name");
    setEqualHeight(".price-block");
    setTimeout(function () {
        setEqualHeight(".prod-desc");
    }, 600);
    setEqualHeight(".prod-sort");

    // Корректировка стилей меню
    $('.mega-more-parent').each(function () {
        if ($(this).hasClass('hide') || $(this).hasClass('hidden'))
            $(this).prev().removeClass('template-menu-line');
    });

    // Вывод всех категорий в мегаменю
    $('.mega-more').on('click', function (event) {
        event.preventDefault();
        $(this).hide();
        $(this).closest('.mega-menu-block').find('.template-menu-line').removeClass('hide');
    });

    // Направление сортировки в брендах
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

    // Ценовой слайдер
    $("#slider-range").on("slidestop", function (event, ui) {

        if (AJAX_SCROLL) {

            // Сброс текущей страницы
            count = current;

            price_slider_load(ui.values[0], ui.values[1]);
        } else {
            $('#price-filter-form').submit();
        }
    });
    // Фасетный фильтр
    if (FILTER && $("#sorttable table td").html()) {
        $("#faset-filter-body").html($("#sorttable table td").html());
        $("#faset-filter").removeClass("hide");
        // $(".product-filter").removeClass("hide");
        $('.mobile-filter').addClass('visible-filter')

    } else {
        $("#faset-filter").hide();
        // $(".product-filter").hide();
        $('.mobile-filter').hide();
    }

    if (!FILTER) {
        $("#faset-filter").hide();
        $("#sorttable").removeClass("hide");
    }
    var body_width = $('body').width();

    if (body_width > 769) {
        if ($(".filter-row #faset-filter-body .grid-item").length == 2) {
            $("#faset-filter-body .grid-item").css("width", "50%")
        }
        if ($(".filter-row #faset-filter-body .grid-item").length == 1) {
            $("#faset-filter-body .grid-item").css("width", "100%")
        }
        if ($(".filter-row #faset-filter-body .grid-item").length == 3) {
            $("#faset-filter-body .grid-item").css("width", "33%")
        }
        if ($(".filter-row #faset-filter-body .grid-item").length >= 3) {
            $("#faset-filter-body .grid-item").css("width", "33%")
        }
    }

    // Направление сортировки
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

    // Загрузка результата отбора при переходе
    if (window.location.hash != "" && $("#sorttable table td").html()) {

        var filter_str = window.location.hash.split(']').join('][]');

        // Проставление чекбоксов
        $.ajax({
            type: "POST",
            url: '?' + filter_str.split('#').join(''),
            data: {
                ajaxfilter: true
            },
            success: function (data) {
                if (data) {
                    $("#faset-filter-body").html(data);
                    $("#faset-filter-body").html($("#faset-filter-body").find('td').html());

                    // Загрузка результата отборки
                    filter_load(filter_str);

                }
            }
        });
    }

    // Ajax фильтр
    $('#faset-filter-body').on('change', 'input:checkbox', function () {

        // Сброс текущей страницы
        count = current;

        faset_filter_click($(this));
    });

    // Сброс фильтра
    $('#faset-filter-reset').on('click', function (event) {
        if ($(".filter-row #faset-filter-body .grid-item").length == 2) {
            $("#faset-filter-body .grid-item").css("width", "50%")
        }
        if ($(".filter-row #faset-filter-body .grid-item").length == 1) {
            $("#faset-filter-body .grid-item").css("width", "100%")
        }
        if ($(".filter-row #faset-filter-body .grid-item").length == 3) {
            $("#faset-filter-body .grid-item").css("width", "33%")
        }
        if ($(".filter-row #faset-filter-body .grid-item").length >= 3) {
            $("#faset-filter-body .grid-item").css("width", "33%")
        }

        if (AJAX_SCROLL) {
            event.preventDefault();
            $("#faset-filter-body").html($("#sorttable table td").html());
            filter_load('');
            history.pushState('', document.title, window.location.pathname);
            $("#slider-range").slider("option", "values", [price_min, price_max]);
            $.removeCookie('slider-range-min');
            $.removeCookie('slider-range-max');
            $(".pagination").show();
            if ($(".filter-row #faset-filter-body .grid-item").length == 2) {
                $("#faset-filter-body .grid-item").css("width", "50%")
            }
            if ($(".filter-row #faset-filter-body .grid-item").length == 1) {
                $("#faset-filter-body .grid-item").css("width", "100%")
            }
            if ($(".filter-row #faset-filter-body .grid-item").length == 3) {
                $("#faset-filter-body .grid-item").css("width", "33%")
            }
            if ($(".filter-row #faset-filter-body .grid-item").length >= 3) {
                $("#faset-filter-body .grid-item").css("width", "33%")
            }

            // Сброс текущей страницы
            count = current;
        }

    });

    // Пагинация товаров
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

    // закрепление навигации
    $('.breadcrumb, .slider.tabs').waypoint(function () {
        if (FIXED_NAVBAR) {
            $('#navigation').toggleClass('navbar-fixed-top');
            if (body_width < 769) {
                $('.top-menu').toggleClass('fix-menu');
            }

        }
        // toTop          
        $('#toTop').fadeToggle();
    });

    // быстрый переход
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

    // выбор каталога поиска
    $(".cat-menu-search").on('click', function () {
        $('#cat').val($(this).attr('data-target'));
        $('#catSearchSelect').html($(this).html());
    });

    $("body").on("click", ".fastView", function (e) {
        e.preventDefault();
        var url = $(this).attr("data-role");

        if (url.length > 2) {
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    ajax: true
                },
                success: function (data) {
                    $(".fastViewContent").html(data);
                    //$('body').addClass('fix');

                    productPageSelect();
                    $(".btn-number").click(function (e) {
                        e.preventDefault();

                        fieldName = $(this).attr("data-field");
                        type = $(this).attr("data-type");
                        var input = $("input[name='" + fieldName + "']");
                        var currentVal = parseInt(input.val());
                        if (!isNaN(currentVal)) {
                            if (type == "minus") {
                                if (currentVal > input.attr("min")) {
                                    input.val(currentVal - 1).change();
                                }
                                if (parseInt(input.val()) == input.attr("min")) {
                                    $(this).attr("disabled", true);
                                }
                            } else if (type == "plus") {
                                if (currentVal < input.attr("max")) {
                                    input.val(currentVal + 1).change();
                                }
                                if (parseInt(input.val()) == input.attr("max")) {
                                    $(this).attr("disabled", true);
                                }
                            }
                        } else {
                            input.val(0);
                        }
                    });
                    $(".input-number").change(function () {
                        var num = parseInt($(this).val());

                        $(this)
                                .closest(".addToCart")
                                .children(".addToCartFull")
                                .attr("data-num", num);
                    });

                    //image zoom
                    //JQueryZoom();
                    $(window).lazyLoadXT();
                }
            });
            /* $('.product-img-modal > img').load(function() {
             if ($('.bxslider').length) {
             $('.bxslider-pre').addClass('hide');
             $('.bxslider').removeClass('hide');
             slider = $('.bxslider').bxSlider({
             mode: 'fade',
             pagerCustom: '.bx-pager'
             });
             }
             });*/
        }
    });
    $(".odnotipList").appendTo(".odnotipListWrapper");
    //$(".odnotipList .product-block-wrapper-fix").unwrap();

    // подгрузка комментариев
    $("body").on("click", "#commentLoad", function () {
        commentList($(this).attr("data-uid"), "list");
    });

    // убираем пустые закладки подробного описания
    if ($("#files").html() == "Нет файлов")
        $("#filesTab").addClass("hide");

    if ($("#vendorenabled").html() == "")
        $("#settingsTab").addClass("hide");
    if ($("#desc").html() == "")
        $("#descTab").addClass("hide");
    if ($("#pages").html() != "")
        $("#pagesTab").addClass("show");
    if ($("#descTab").hasClass("hide") && $("#settingsTab").hasClass("hide")) {
        $("#commentTab").addClass('active'), $("#messages").addClass('active')
    }
    /*
     if ($('#vendorActionButton').val() == 'Применить') {
     $('#sorttable').addClass('show');
     }*/

    // Иконки в основном меню категорий
    if (MEGA_MENU_ICON === false) {
        $(".mega-menu-block img").hide();
    }

    // убираем меню брендов
    if (BRAND_MENU === false) {
        $("#brand-menu").hide();
    }

    if (
            $(".brands").length
            ) {
        $(".brands-link").css("display", "inline");
    }

    if (CATALOG_MENU === false) {
        $("#catalog-menu").hide();
    } else {
        $("#catalog-menu").removeClass("hide");
    }

    // добавление в корзину
    $('body').on('click', 'button.addToCartList', function () {

        addToCartList($(this).attr("data-uid"), $(this).attr("data-num"));
        $(this).attr("disabled", "disabled");

        $("#order").addClass("active");
        if ($(this).hasClass('listBtn')) {
            $(this).addClass("btn-success");
            $(this).children('.btn-text').text(locale.incart);
        }
    });

    // изменение количества товара для добавления в корзину
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
    // добавление в корзину подтипа
    $(".addToCartListParent").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-parent'));
        $('[itemprop="price"]').html($(this).attr('data-price'));
    });

    // добавление в корзину опции
    $(".addToCartListOption").on('click', function () {
        addToCartList($(this).attr('data-uid'), $(this).attr('data-num'), $(this).attr('data-uid'), $('#allOptionsSet' + $(this).attr('data-uid')).val());
    });

    // добавление в wishlist
    $('body').on('click', '.addToWishList', function () {

        // Подтип
        if ($("#parentSizeMessage").html()) {

            // Размер
            if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                addToWishList($('input[name="parentSize"]').val(), $(this).attr('data-uid'));
            }
            // Размер  и цвет
            else if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {
                addToWishList($('input[name="parentColor"]').val(), $(this).attr('data-uid'));
            } else
                showAlertMessage($("#parentSizeMessage").html());
        } else
            addToWishList($(this).attr('data-uid'));

    });

    // добавление в compare
    $('body').on('click', '.addToCompareList', function () {
        addToCompareList($(this).attr('data-uid'));
    });

    // отправка сообщения администратору из личного кабинета
    $("#CheckMessage").on('click', function () {
        if ($("#message").val() != '')
            $("#forma_message").submit();
    });

    // Визуальная корзина
    if ($("#cartlink").attr('data-content') == "") {
        $("#cartlink").attr('href', '/order/');
    }
    $('[data-toggle="popover"]').popover();
    $('a[data-toggle="popover"]').on('show.bs.popover', function () {
        $('a[data-toggle="popover"]').attr('data-content', $("#visualcart_tmp").html());
    });

    // Подсказки 
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});

    // Стилизация select
    $('.selectpicker').selectpicker({
        width: "100%"
    });

    // Переход из прайса на форму с описанием
    $('#price-form').on('click', function (event) {
        event.preventDefault();
        if ($(this).attr('data-uid') != "" && $(this).attr('data-uid') != "ALL")
            window.location.replace("../shop/CID_" + $(this).attr('data-uid') + ".html");
    });

    // Ajax поиск
    $(".search-input").on('input', function () {
        var words = $(this).val();
        var element = $(this);

        if (words.length > 2) {
            $.ajax({
                type: "POST",
                url: ROOT_PATH + "/search/",
                data: {
                    words: escape(words),
                    set: 2,
                    ajax: true
                },
                success: function (data) {

                    // Результат поиска
                    if (data != 'false') {

                        if (data != element.attr('data-content')) {
                            element.attr('data-content', data);

                            element.popover('show');
                        }
                    } else
                        element.popover('hide');
                }
            });
        } else {
            element.attr('data-content', '');
            element.popover('hide');

        }
    });
    // Повторная авторизация
    if ($('#usersError').html()) {
        $('form[name=user_forma] .form-group').addClass('has-error has-feedback');
        $('form[name=user_forma] .glyphicon').removeClass('hide');
        $('#userModal').modal('show');
        $('#userModal').on('shown.bs.modal', function () {

        });
    }

    // Проверка синхронности пароля регистрации
    $("form[name=user_forma_register] input[name=password_new2]").on('blur', function () {
        if ($(this).val() != $("form[name=user_forma_register] input[name=password_new]").val()) {
            $('form[name=user_forma_register] #check_pass').addClass('has-error has-feedback');
            $('form[name=user_forma_register] .glyphicon').removeClass('hide');
        } else {
            $('form[name=user_forma_register] #check_pass').removeClass('has-error has-feedback');
            $('form[name=user_forma_register] .glyphicon').addClass('hide');
        }
    });

    // Регистрация пользователя
    $("form[name=user_forma_register]").on("submit", function () {
        if (
                $(this)
                .find("input[name=password_new]")
                .val() !=
                $(this)
                .find("input[name=password_new2]")
                .val()
                ) {
            $(this)
                    .find("#check_pass")
                    .addClass("has-error has-feedback");
            $(this)
                    .find(".glyphicon")
                    .removeClass("hide");
            return false;
        } else
            $(this).submit();
    });

    // Ошибка регистрации
    if ($("#user_error").html()) {
        $("#user_error")
                .find(".list-group-item")
                .addClass("list-group-item-warning");
    }

    // формат ввода телефона
    $("form[name='forma_order'], input[name=returncall_mod_tel],input[name=tel],input[name=tel_new],input[name=oneclick_mod_tel]").on("click", function () {
        if (PHONE_FORMAT && PHONE_MASK) {
            $("input[name=tel_new], input[name=returncall_mod_tel],input[name=tel],input[name=oneclick_mod_tel]").mask(PHONE_MASK);
        }
    });




    /*   if (body_width > 769) {
     if ($(".bxslider").length) {
     $(".bxslider-pre").addClass("hide");
     $(".bxslider").removeClass("hide");
     
     slider = $(".product-1 .bxslider, .product-2 .bxslider, .product-3 .bxslider").bxSlider({
     controls: (body_width > 769) ? false : true,
     nextText: "",
     prevText: "",
     mode: (body_width > 769) ? "fade" : "horizontal",
     
     pager: ($(".bxslider>div").length > 1) ? true : false,
     pagerCustom: (body_width > 769) ? ".bx-pager" : null,
     infiniteLoop: true,
     onSliderLoad: function(currentIndex) {
     $(".bxslider")
     .children()
     .eq(currentIndex + 1)
     .addClass("active-slide");
     
     
     },
     onSlideAfter: function($slideElement) {
     $(".bxslider")
     .children()
     .removeClass("active-slide");
     $slideElement.addClass("active-slide");
     if (body_width > 769) {
     
     
     }
     }
     });
     $('.zoomContainer').remove();
     var width = $('.bx-wrapper').width();
     var height = $('.bx-wrapper .bx-viewport').height();
     if (body_width > 769) {
     
     thumbsSlider = $('.product-1 .bx-pager, .product-2 .bx-pager').bxSlider({
     
     wrapperClass: 'wrap',
     nextText: "",
     prevText: "",
     touchEnabled: false,
     minSlides: 1,
     maxSlides: (width < 248) ? 1 :
     (width < 246) ? 2 :
     (width < 369) ? 3 :
     (width < 492) ? 4 :
     (width < 615) ? 5 : 6,
     slideMargin: 6,
     moveSlides: 1,
     infiniteLoop: false,
     pager: false,
     slideWidth: 90,
     });
     
     }
     vertSlider = $('.product-3 .bx-pager').bxSlider({
     wrapperClass: 'wrap-vert',
     mode: "vertical",
     pager: false,
     touchEnabled: false,
     adaptiveHeight: true,
     easing: 'easeInOutQuint',
     //displaySlideQty: visibleThumbs,
     //moveSlideQty: visibleThumbs,
     infiniteLoop: false,
     slideWidth: 90,
     minSlides: (height < 240) ? 1 :
     (height < 360) ? 2 :
     (height < 480) ? 3 :
     (height < 600) ? 4 :
     (height < 720) ? 5 : 6,
     slideMargin: 6,
     });
     $('.controls').appendTo('.wrap')
     $(".bx-controls").appendTo(".controls");
     
     $(".product-4 .bxslider div").removeClass('zoom');
     if (body_width > 769) {
     $(".bx-controls").on("click", ".bx-next", function() {
     //   $('.zoomContainer').remove();
     current = $(".bigslider .active-slide");
     if ($(current).next('div').html() !== undefined) {
     $(current).removeClass("active-slide");
     $(current).css("display", "none");
     $(current).siblings('div').css("display", "none");
     $(current).next('div').addClass("active-slide");
     $(current).next('div').css("display", "block");
     }
     
     
     
     
     
     });
     }
     if (body_width > 769) {
     $(".bx-controls").on("click", ".bx-prev", function() {
     
     current = $(".bigslider .active-slide");
     if ($(current).prev('div').html() !== undefined) {
     $(current).removeClass("active-slide");
     $(current).css("display", "none");
     $(current).siblings('div').css("display", "none");
     $(current).prev('div').addClass("active-slide");
     $(current).prev('div').css("display", "block");
     }
     
     
     });
     }
     }	
     }
     else{
     
     if ($(".bxslider").length) {
     $(".bxslider-pre").addClass("hide");
     $(".bxslider").removeClass("hide");
     
     slider = $(".product-1 .bxslider, .product-2 .bxslider, .product-3 .bxslider").bxSlider({
     controls:  false ,
     nextText: "",
     prevText: "",
     mode:"fade",
     
     
     pager: true,
     pagerCustom: ".bx-pager",
     infiniteLoop: true,
     onSliderLoad: function(currentIndex) {
     $(".bxslider")
     .children()
     .eq(currentIndex + 1)
     .addClass("active-slide");
     
     
     },
     onSlideAfter: function($slideElement) {
     $(".bxslider")
     .children()
     .removeClass("active-slide");
     $slideElement.addClass("active-slide");
     
     }
     });
     
     
     var width = $('.bx-wrapper').width();
     var height = $('.bx-wrapper .bx-viewport').height();
     
     thumbsSlider = $('.product-1 .bx-pager, .product-2 .bx-pager').bxSlider({
     wrapperClass: 'wrap',
     
     nextText: "",
     prevText: "",
     
     touchEnabled: false,
     minSlides: 1,
     maxSlides: (width < 248) ? 1 :
     (width < 246) ? 2 :
     (width < 369) ? 3 :
     (width < 492) ? 4 :
     (width < 615) ? 5 : 6,
     slideMargin: 6,
     moveSlides: 1,
     infiniteLoop: false,
     pager: false,
     slideWidth: 90,
     });
     
     $('.controls').appendTo('.wrap')   
     $(".bx-controls").appendTo(".controls");
     $(".bx-controls").on("click", ".bx-next", function() {
     current = $(".bigslider .active-slide");
     if ($(current).next('div').html() !== undefined) {
     $(current).removeClass("active-slide");
     $(current).css("display", "none");
     $(current).siblings('div').css("display", "none"); 
     $(current).next('div').addClass("active-slide");
     $(current).next('div').css("display", "block");
     }
     });
     $(".bx-controls").on("click", ".bx-prev", function() {
     $('.zoomContainer').remove();
     current = $(".bigslider .active-slide");
     if ($(current).prev('div').html() !== undefined) {
     $(current).removeClass("active-slide");
     $(current).css("display", "none");
     $(current).siblings('div').css("display", "none");
     $(current).prev('div').addClass("active-slide");
     $(current).prev('div').css("display", "block");
     }
     });
     }
     }
     setTimeout(function() {
     if (body_width < 769) {
     $.removeData($('.main-slider .zoom'), 'elevateZoom');
     $('.zoomContainer').remove();
     $('.zoom').removeClass("zoom")
     }
     }, 600);
     
     
     
     
     if(body_width < 769){
     setTimeout(function() {	
     $(".bx-wrapper .bxslider img").pinchzoomer({maxZoom:3}, false)
     },400)
     $(".bx-pager").on("tap",function(){
     $(".bx-wrapper .bxslider img").pinchzoomer({maxZoom:3}, false);
     })
     }
     
     */
    if (body_width > 768) {
        // Фотогалерея в по карточке товара с большими изображениями

        $(document).on('click', '.bx-viewport', function (event) {

            event.preventDefault();
            $('#sliderModal').modal('show');
            $('.bxsliderbig').html($('.bxsliderbig').attr('data-content'));
            $('.modal .modal-body img').css("max-height", $(window).height() * 0.65);
            setTimeout(function () {



                $('.modal .modal-body .bx-viewport').css("max-height", $(window).height() * 0.65);
                $('.modal .modal-body .bx-viewport').css("opacity", "1")

            }, 600);
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

        // Закрытие модального окна фотогарелерии, клик по изображению


        $(document).on('click', '.bxsliderbig a', function (event) {
            event.preventDefault();
            slider.goToSlide(sliderbig.getCurrentSlide());
            $('#sliderModal').modal('hide');
        });

        // Закрытие модального окна фотогарелерии
        $('#sliderModal').on('hide.bs.modal', function () {
            slider.goToSlide(sliderbig.getCurrentSlide());
            sliderbig.destroySlider();
            delete sliderbig;
        });
    }

    // Сворачиваемый блок
    $(".collapse").on("show.bs.collapse", function () {
        $(this)
                .prev("h4")
                .find("i")
                .removeClass("fa-chevron-down");
        $(this)
                .prev("h4")
                .find("i")
                .addClass("fa-chevron-up");
        $(this)
                .prev("h4")
                .attr("title", "Скрыть");
    });
    $(".collapse").on("hidden.bs.collapse", function () {
        $(this)
                .prev("h4")
                .find("i")
                .removeClass("fa-chevron-up");
        $(this)
                .prev("h4")
                .find("i")
                .addClass("fa-chevron-down");
        $(this)
                .prev("h4")
                .attr("title", "Показать");
    });

    $(".input-number").change(function () {
        var num = parseInt($(this).val());

        $(this)
                .closest(".addToCart")
                .children(".addToCartFull")
                .attr("data-num", num);
    });
    // добавление в корзину подробное описание
    $("body").on("click", ".addToCartFull", function () {
        // Подтип
        if ($(".odnotip-" + $(this).attr("data-uid") + " #parentSizeMessage").html()) {
            // Размер
            if ($('input[name="parentColor"]').val() === undefined && $('input[name="parentSize"]:checked').val() !== undefined) {
                addToCartList(
                        $('input[name="parentSize"]:checked').val(),
                        $('input[name="quant[2]"]').val(),
                        $('input[name="parentSize"]:checked').attr("data-parent")
                        );
                $(".cart").css("display", "block");
                $(this).addClass("btn-success");
                $(this).text(locale.incart);
            }
            // Размер  и цвет
            else if (
                    $('input[name="parentSize"]:checked').val() > 0 &&
                    $('input[name="parentColor"]:checked').val() > 0
                    ) {
                var color = $('input[name="parentColor"]:checked').attr("data-color");
                var size = $('input[name="parentSize"]:checked').attr("data-name");
                var parent = $('input[name="parentColor"]:checked').attr("data-parent");

                $.ajax({
                    url: ROOT_PATH + "/phpshop/ajax/option.php",
                    type: "post",
                    data:
                            "color=" +
                            escape(color) +
                            "&parent=" +
                            parent +
                            "&size=" +
                            escape(size),
                    dataType: "json",
                    success: function (json) {
                        if (json["id"] > 0) {
                            if ($('input[name="parentSize"]:checked').val() > 0 && $('input[name="parentColor"]:checked').val() > 0) {

                                addToCartList(json["id"], $('input[name="quant[2]"]').val(), $('input[name="parentColor"]:checked').attr("data-parent"));

                                $(".addToCartFull[data-uid=" + parent + "]").addClass("btn-success");
                                $(".addToCartFull[data-uid=" + parent + "]").text(locale.incart);
                                $(".cart").css("display", "block");
                            } else
                                showAlertMessage($("#parentSizeMessage").html());
                            $(".input-number").change(function () {
                                var num = parseInt($(this).val());

                                $(this)
                                        .closest(".addToCart")
                                        .children(".addToCartFull")
                                        .attr("data-num", num);
                            });
                        }
                    }
                });
            } else
                showAlertMessage($("#parentSizeMessage").html());
        }
        // Опции характеристики
        else if ($(".options-" + $(this).attr("data-uid") + " #optionMessage").html()) {
            var optionCheck = true;
            var optionValue = $("#allOptionsSet" + $(this).attr("data-uid")).val();

            $(".optionsDisp select").each(function () {
                if ($(this).hasClass("req") && optionValue === "")
                    optionCheck = false;
            });

            if (optionCheck) {
                addToCartList(
                        $(this).attr("data-uid"),
                        $('input[name="quant[2]"]').val(),
                        $(this).attr("data-uid"),
                        optionValue
                        );
                $(".cart").css("display", "block");
                $(this).addClass("btn-success");
                $(this).text(locale.incart);
            } else
                showAlertMessage($("#optionMessage").html());
        }
        // Обычный товар
        else {
            addToCartList(
                    $(this).attr("data-uid"),
                    $('input[name="quant[2]"]').val()
                    );
            $(this).addClass("btn-success");
            $(this).text(locale.incart);
            $(".cart").css("display", "block");
        }
    });


    // выбор цвета 
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

                    // Смена цены
                    $('[itemprop="price"]').html(json['price']);

                    // Смена старой цены
                    if (json['price_n'] != "")
                        $('[itemscope] .old-price').html(json['price_n'] + '<span class="rubznak">' + $('[itemprop="priceCurrency"]').html() + '</span>');
                    else
                        $('[itemscope] .old-price').html('');

                    // Смена картинки
                    var parent_img = json['image_big'];
                    if (parent_img != "") {
                        var options = $('.bigThumbs div[data-big-image="' + parent_img + '"]').attr('data-options');
                        if (options) {
                            var touchNSwipe = TouchNSwipe.get("productSlider");
                            touchNSwipe.index(Number(options[options.search(/index:.+/) + 6]));
                        }
                    }

                    // Смена склада
                    $('#items').html(json['items']);
                }
            }
        });

    });

    // выбор размера
    $('body').on('change', 'input[name="parentSize"]', function () {
        var id = this.value;

        $('input[name="parentSize"]').each(function () {
            this.checked = false;
            $(this).parent('label').removeClass('label_active');
        });

        this.checked = true;
        $(this).parent('label').addClass('label_active');

        // Если нет цветов меняем сразу цену и картинку
        if ($('input[name="parentColor"]').val() === undefined) {

            // Смена цены
            $('[itemprop="price"]').html($(this).attr('data-price'));

            // Смена старой цены
            if ($(this).attr('data-priceold') != "")
                $('[itemscope] .old-price').html($(this).attr('data-priceold') + '<span class=rubznak>' + $('[itemprop="priceCurrency"]').html() + '</span>');
            else
                $('[itemscope] .old-price').html('');

            // Смена картинки
            var parent_img = $(this).attr('data-image');
            if (parent_img != "") {
                var options = $('.bigThumbs div[data-big-image="' + parent_img + '"]').attr('data-options');
                if (options) {
                    var touchNSwipe = TouchNSwipe.get("productSlider");
                    touchNSwipe.index(Number(options[options.search(/index:.+/) + 6]));
                }
            }

            // Смена склада
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
                $(this).parent('label').attr('title', 'Нет');
            }
        });
    });

    // plugin bootstrap minus and plus http://jsfiddle.net/laelitenetwork/puJ6G/
    $(".btn-number").click(function (e) {
        e.preventDefault();

        fieldName = $(this).attr("data-field");
        type = $(this).attr("data-type");
        var input = $("input[name='" + fieldName + "']");
        var currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if (type == "minus") {
                if (currentVal > input.attr("min")) {
                    input.val(currentVal - 1).change();

                    if ($('.btn-number[data-type="plus"]').attr('disabled')) {
                        $('.btn-number[data-type="plus"]').attr('disabled', false);
                    }
                }
                if (parseInt(input.val()) == input.attr("min")) {
                    $(this).attr("disabled", true);
                }
            } else if (type == "plus") {
                if (currentVal < input.attr("max")) {
                    input.val(currentVal + 1).change();

                    if ($('.btn-number[data-type="minus"]').attr('disabled')) {
                        $('.btn-number[data-type="minus"]').attr('disabled', false);
                    }
                }
                if (parseInt(input.val()) == input.attr("max")) {
                    $(this).attr("disabled", true);
                }
            }
        } else {
            input.val(0);
        }
    });
    //  Согласие на использование cookie
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

    // Варианты оплат
    $("body").on('click', "input#order_metod", function () {
        $('.paymOneEl').removeClass('active');
        $(this).closest('.paymOneEl').addClass('active');
    });

    $('.sortcat').on('click', function (e) {
        if ($(this).hasClass('active')) {
            e.preventDefault();

            var param = $(this).attr('href').replace('?', '');
            var href = window.location.href.split('?')[1].replace(param, '');

            window.location.href = '?' + href;
        }
    });

    $(document).click(function (e) {
        var div = $("#faset-filter-body .active");
        if (!div.is(e.target) && div.has(e.target).length === 0) {
            div.toggleClass('active');
        }
    });

     // Настройки https://previews.customer.envatousercontent.com/files/263390663/tns_options_example.html
    if ($('#productSlider').length > 0) {
        if ($('.heroSlide img').attr('src').indexOf('no_photo.png') + 1) {
            var src = $('.heroSlide img').attr('src');
            TouchNSwipe.remove("productSlider");
            $('#productSlider').append('<img  src="' + src + '" style="width: 100%;max-width: 480px;"/>');
        } else {
            $('.heroSlide img').each(function (index, element) {
                $(element).removeClass('hide');
            });

            var tns = TouchNSwipe.get('productSlider');
            tns.slider.on(ElemZoomSlider.INDEX_CHANGE, function (event) {
                $(event.currentTarget.getSlideElemAt(event.currentTarget._index)).find('img').removeClass('hide');
            });
        }
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
