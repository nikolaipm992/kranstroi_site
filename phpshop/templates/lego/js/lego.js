[].forEach.call(document.querySelectorAll("img[data-src]"), function (img) {
    img.setAttribute("src", img.getAttribute("data-src"));
    img.onload = function () {
        img.removeAttribute("data-src");
    };
});

$(document).ready(function () {
	if($('.bigThumb').length < 1 ) {$('.controlHolder').hide()}
	
	
	$('.filter-menu').on('click', function() {
		
        $(".filter-menu").toggleClass('active');
		
    });
	$('.filter-menu label:nth-child(n+2)').on('click', function() {
		
   //  parent.location.hash = ''
		
    });
    setTimeout(function () {
        $('.slider.tabs').css('opacity', '1')
        $('.swiper-wrapper').css('opacity', '1')
    }, 400)
    $(".big-container .template-product-list .row .product-block-wrapper-fix").unwrap();

    var body_width = $('body').width();
    if (body_width > 992) {
        /*
         if ($(".big-container .template-product-list .col-md-3").length) {
         $(".template-product-list .product-block-wrapper-fix.col-md-3").css("width", "20%")
         var col_count = 5;
         var $e = $('.template-product-list');
         while ($e.children('.product-block-wrapper-fix').not('.row').length) {
         $e.children('.product-block-wrapper-fix').not('.row').filter(':lt(' + col_count + ')').wrapAll('<div class="row">');
         
         
         }}*/
        if ($(".big-container .template-product-list .col-md-4").length) {
            $(".template-product-list .product-block-wrapper-fix.col-md-4").removeClass("col-md-4").addClass('col-md-3')
            var col_count = 4;
            var $e = $('.template-product-list');
            while ($e.children('.product-block-wrapper-fix').not('.row').length) {
                $e.children('.product-block-wrapper-fix').not('.row').filter(':lt(' + col_count + ')').wrapAll('<div class="row">');

            }
        }
        if ($(".big-container .template-product-list .col-md-6").length) {
            $(".template-product-list .product-block-wrapper-fix.col-md-6").removeClass("col-md-6").addClass('col-md-4')
            var col_count = 3;
            var $e = $('.template-product-list');
            while ($e.children('.product-block-wrapper-fix').not('.row').length) {
                $e.children('.product-block-wrapper-fix').not('.row').filter(':lt(' + col_count + ')').wrapAll('<div class="row">');
            }
        }
    }
    if (body_width > 768 && body_width < 850) {

        if ($(".big-container .template-product-list .col-sm-3").length) {
            $(".template-product-list .product-block-wrapper-fix.col-sm-3").removeClass("col-sm-3").addClass('col-sm-4')
            var col_count = 3;
            var $e = $('.template-product-list');
            while ($e.children('.product-block-wrapper-fix').not('.row').length) {
                $e.children('.product-block-wrapper-fix').not('.row').filter(':lt(' + col_count + ')').wrapAll('<div class="row">');
            }
        }
    }
    if (body_width > 350 && body_width < 768) {

        if ($(".template-product-list .big-container .col-xs-6").length) {
            var col_count = 2;
            var $e = $('.template-product-list');
            while ($e.children('.product-block-wrapper-fix').not('.row').length) {
                $e.children('.product-block-wrapper-fix').not('.row').filter(':lt(' + col_count + ')').wrapAll('<div class="row">');
            }
        }
    }
    $(".sort-table-product-link").each(function () {

        $(this).wrapAll('<div class="link-block"></div>')
        $(this).html('');
        $(this).append('<img class="link-img" src=>')
        var src = $(this).attr("data-option")
        $(this).children(".link-img").attr("src", src);
    })
    $(".link-block").siblings("br").remove()

    $('#cartlink').click(function () {

    });

    if ($(".carousel-inner .item+.item").length) {
        $(".carousel-control, .carousel-indicators").css("visibility", "visible")
    }

    // Показ блока сейчас кто-то купил
    $(".modal-nowBuy").fadeIn(2000).delay(7000).fadeOut(1000);

    // Закрыть блок сейчас кто-то купил
    $('.nowbuy-close').on('click', function (e) {
        e.preventDefault();
        $('.modal-nowBuy').addClass('hide');
        $.cookie('nowbuy_close', 1, {
            path: '/',
            expires: 24
        });
    });


    if ($(".sidebar-left").hasClass("hide")) {
        $(".main-content").css("width", "100%")
        $(".main-content  .col-md-4").css("width", "25%")
    }
    if ($(".inner-nowbuy").hasClass("hide")) {
        $(".order").parents(".main-content").css("width", "100%")
        $(".order").parents(".main-content").children(".sidebar-left-inner").remove()
        $(".order").parents(".main").css("width", "100%")
        $(".order").parents(".main").addClass("big-container")

    }
    $(".big-container .catalog-wrap").removeClass("col-md-4");
    $(".big-container .catalog-wrap").addClass("col-md-3");


    // FlipClock
    if ($('.clock').length) {
        var now = new Date();
        var night = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
                $('.clock').attr('data-hour'), 0, 0
                );
        var msTillMidnight = night.getTime() / 1000 - now.getTime() / 1000;
        var clock = $('.clock').FlipClock({
            language: 'russian',
            coundown: true

        });
        clock.setTime(msTillMidnight);
        clock.setCountdown(true);
        clock.start();
    }

    $(".catalog > a").on('click', function () {
        $(this).siblings("ul").slideToggle();
        $(this).children("i").toggleClass("fa-chevron-down")
        $(this).children("i").toggleClass("fa-chevron-up")


    });

    var $grid = $('.grid').masonry({
        // options
        itemSelector: '.grid-item',
        columnWidth: '.grid-item',
    });
    $grid.imagesLoaded().progress(function () {
        $grid.masonry('layout');
    });


    $('.header-search [data-toggle="popover"]').popover({
        container: '.header-search'
    });
    if ($('.pageCatalContent').is(':empty')) {
        $('.pageCatal').remove();
    }

    $(".main-menu-block").appendTo(".menu-wrap .container-fluid");
    $("#catalog-menu ul").removeClass("dropdown-menu", "dropdown-parent");
    $("#catalog-menu ul").removeClass("dropdown-menu-indent-sm");
    $("#catalog-menu ul").removeClass("no-border-radius");
    $("#catalog-menu li").removeClass("dropdown");
    $("#catalog-menu li").removeClass("dropdown-right");
    $("#catalog-menu a").removeClass("sub-marker");
    var pathname = self.location.pathname;
    $("#catalog-menu li").each(function () {
        var path = $(this)
                .find("a")
                .attr("href");
        if (path === pathname) {
            $(this).addClass("visible-list");
            $(this).children("a").addClass('active-item');
            $(this)
                    .parents(".dropdown-parent")
                    .addClass("visible-list");
        }

    });
    $(".pageCatal a").each(function () {
        var path = $(this).attr("href");
        if (path === pathname) {

            $(this).addClass('active-item');
            $(this)
                    .parents("ul").css("display", "block");
        }

    });

    $("#catalog-menu > li:not(.visible-list)").remove();

    if (PHONE_FORMAT && PHONE_MASK) {
        $('.phone').attr("autocomplete", "off");
        $('input[name="tel_new"]').mask(PHONE_MASK);
        $('.phone').mask(PHONE_MASK);
    }

    $(".top-banner").each(function (index) {
        if ($(this).children('.sticker-text').is(':empty')) {
            $(this).hide();
        }
    });
    $(".top-banner .close").on('click', function () {
        $(".top-banner").remove();
    });


    $(".delivOneEl").on("click", function () {
        $(this).addClass('active');

    });

    //navbar

    var previousScroll = 0,
            navBarOrgOffset = $("#navbar").offset().top;

    $("#navigation").height($("#navbar").height());

 $(window).scroll(function() {
  var currentScroll = $(this).scrollTop();
  if (currentScroll  >100) {
   $('.top-navbar').addClass('fix-nav')
   $('.fixed-header .navbar-wrap').addClass('shadow')
  } else {
   $('.top-navbar').removeClass('fix-nav')
   $('.fixed-header .navbar-wrap').removeClass('shadow')
  }

});

    $(".mobile-filter").click(function () {
        $(".big-filter-wrapper").addClass("active")
    });
    $(".filter-close").click(function () {
        $(".big-filter-wrapper").removeClass("active")
    });

   $(".btn-menu-left").click(function() {
        $('.hidden-catalog').css('left', '0');

    });  $(".btn-menu-right").click(function() {
        $('.hidden-top').css('left', '0');

    });
       $(".btn-menu-left").on("click", function() {
        $('#navigation').css('z-index', '998')
        $(".back").removeClass("active");
        $(".dropdown-menu").removeClass("active");

        $(".hidden-menu li").removeClass("no-display");
        $(".hidden-menu i").removeClass("no-display");
    });
    $(".btn-menu-right").on("click", function() {
        $('#navigation').css('z-index', '998')
        $(".back").removeClass("active");
        $(".dropdown-menu").removeClass("active");

        $(".hidden-menu li").removeClass("no-display");
        $(".hidden-menu i").removeClass("no-display");
    });
    $('.hidden-menu .sub-marker').removeAttr('href')
    $('.hidden-menu .sub-marker').click(function () {

        $(this).siblings('.dropdown-menu').slideToggle();
        $(this).siblings('.dropdown-menu').toggleClass('active');

    });
    $(".hidden-menu .close").click(function () {
        $('#navigation').css('z-index', '998')
        $('.hidden-menu').css('left', '-100%');

    });
     $(".top-navbar .open-menu").click(function () {
        if ($(".top-navbar").hasClass("fix-nav")) {
            $('#navigation.fix-nav').css('z-index', '998')
            $(".menu-wrap").toggleClass('active act fixed-menu');

            $(".menu-wrap").fadeIn("slow")
        } else {
            $(".menu-wrap").toggleClass('active act');
            $(".menu-wrap").fadeIn("slow")
            $(".open-menu .fal").toggleClass("no-display")
        }
    });
    $(".menu-close ").click(function () {
        $(".menu-wrap").toggleClass('active act fixed-menu');
        $(".menu-wrap").fadeOut("slow")
    });
    $(document).mouseup(function (e) {
        if ($(".menu-wrap").hasClass('fixed-menu')) {
            var div = $(".menu-cont");
            if (!div.is(e.target) &&
                    div.has(e.target).length === 0) {
                $(".menu-wrap").toggleClass('active act fixed-menu');
            }
        }
    });
    $("header .container-fluid:not(.menu-cont)").click(function () {
        $(".menu-wrap").removeClass('active');
        $(".menu-wrap").fadeOut("slow");
    });
    length = $(".main-menu-block > li").length

    if (length > 12) {
        $('.main-menu-block').appendTo('.big-menu-wrap')
        $('.big-menu-wrap > ul > li >a.sub-marker').removeAttr('href')
        $('<i class="fa fa-angle-right" aria-hidden="true"></i>').appendTo('.big-menu-wrap > ul > li >a.sub-marker')
        $('.open-menu').addClass('visible-menu-btn')


    }
    $(".visible-menu-btn").click(function () {
        $('.big-menu').addClass('visible-menu')
        $('body').addClass('visible-body')
        $('.menu-close-btn').removeClass('no-display');


    })
    $(".menu-close-btn").click(function () {
        $('.big-menu').removeClass('visible-menu')
        $('body').removeClass('visible-body')
        $('.open-menu .fal').addClass('no-display');
        $('.menu-close-btn').addClass('no-display');
    })

    $('.big-menu li a.sub-marker').click(function () {
        $('.menu-back-btn').toggleClass('visible')
        $(this).parents('li').addClass('visible-menu-item')
        $(this).parents('li').siblings('li').css('display', 'none')

    })
    $('.menu-back-btn').click(function () {
        $('.menu-back-btn').toggleClass('visible')
        $(".big-menu li").removeClass('visible-menu-item')
        $('.big-menu li').css('display', 'block')
    }
    )
    $(document).mouseup(function (e) {
        if ($(".big-menu").hasClass('visible-menu')) {
            var div = $(".big-menu-wrap");
            if (!div.is(e.target) &&
                    div.has(e.target).length === 0) {
                $('.big-menu').removeClass('visible-menu')
                $('body').removeClass('visible-body')
                $('.open-menu .fal').addClass('no-display');
                $('.menu-close-btn').addClass('no-display');
            }
        }
    });


    $(".open-menu").click(function () {
        defaultHeight = $(".main-menu-block").height;
        $(".main-menu-block >li:first-child ul.dropdown-menu-indent-sm a").addClass("visible")
        $(function () {
            var s = $(".main-menu-block > li:first-child  .dropdown-menu-indent-sm > li "),
                    width = 0,
                    arr = [];
            s.each(function (indx, element) {
                arr[indx] = $(this).width()
                width += arr[indx];
            });
            var s = $(".main-menu-block > li:first-child  .dropdown-menu-indent-sm > li"),
                    height = 0,
                    arr = [];
            s.each(function (indx, element) {
                arr[indx] = $(this).height()
                height += arr[indx];
            });
            columns = Math.round(height / $(".main-menu-block").height());
            menuHeight = $(".main-menu-block").height();
            mWidth = columns * $('.main-menu-block>li').width();
            menuWidth = $(".menu-wrap > div").width() - $(".main-menu-block").width();
            if (mWidth > menuWidth) {
                menuHeight = menuHeight * 1.3;
                columns = Math.round(height / menuHeight);
                liWidth = $('.main-menu-block>li').width();
                mWidth = columns * $('.main-menu-block>li').width();
                if (mWidth > (menuWidth + 10)) {
                    menuHeight = menuHeight * 1.8;
                    columns = Math.round(height / menuHeight);
                    mWidth = columns * $('.main-menu-block>li').width();
                    $(".main-menu-block").css("height", menuHeight);
                    if (mWidth > (menuWidth - 10)) {
                        menuHeight = menuHeight * 1.2;

                        $(".main-menu-block").css("height", menuHeight);
                    } else {
                        $(".main-menu-block").css("height", menuHeight);
                    }
                }
            }
        });

    })
    setTimeout(function () {
        $(".main-menu-block > li").bind(' mouseover click', function () {
            console.log('new')
            $(".main-menu-block >li:first-child >ul.dropdown-menu-indent-sm a").removeClass("visible")

            var s = $(this).children(".dropdown-menu-indent-sm > li"),
                    width = 0,
                    arr = [];
            s.each(function (indx, element) {
                arr[indx] = $(this).width()
                width += arr[indx];
            });
            /*Считаем высоту всех ли*/
            var s = $(this).find(".dropdown-menu-indent-sm >li>"),
                    height = 0,
                    arr = [];
            s.each(function (indx, element) {
                arr[indx] = $(this).height()
                height += arr[indx];
            });

            /*Находим самый высокий блок*/
            var blockHeight = 0;
            $(this).find(".dropdown-menu-indent-sm >li>ul").each(function () {
                var h_block = parseInt($(this).height());
                if (h_block > blockHeight) {
                    blockHeight = h_block;
                }
                ;
            });
            blockHeight = blockHeight * 1.1

            /*высота ли в левом меню */
            var m = $('.main-menu-block > li'),
                    heights = 0,
                    arr = [];
            m.each(function (indx, element) {
                arr[indx] = $(this).height()
                heights += arr[indx];
            });
            var border = $('.main-menu-block > li').length;
            menuHeight = border + heights;
            $(".main-menu-block").css("height", menuHeight);
            $('.main-menu-block').css('min-height', blockHeight);
            minHeight = blockHeight



            if (minHeight > menuHeight) {
                $(".main-menu-block").css('height', blockHeight);
                columns = Math.round(height * 1.2 / minHeight);
            } else {
                columns = Math.round(height * 1.2 / menuHeight);
                columns2 = Math.round(height * 1.2 / menuHeight)
            }
            liWidth = $('.main-menu-block>li').width();
            mWidth = columns * liWidth;
            menuWidth = $(".menu-wrap > div").width() - $(".main-menu-block").width();
            var i = 0;
            while (mWidth > menuWidth) {
                //console.log(i);
                i++;
                menuHeight = menuHeight * 1.4;
                columns = Math.round(height / menuHeight)
                //console.log('зашли')
                mWidth = columns * $('.main-menu-block>li').width();
                $(".main-menu-block").css("height", menuHeight);
            }



        });
    }, 600)

    var text = $('#vendorenabled .panel-body').length;

    $(".panel-body").each(function () {
        var stringText = $(this).html();

        if (stringText === "")
            $(this)
                    .parents(".panel")
                    .addClass("no-display");
    });

    if ($("#faset-filter-body").html() === "") {
        $(".big-filter-wrapper").remove();
    }
    if ($(".bx-pager").html() === "") {
        $(".controls").remove();
    }
    if ($(".bx-pager").html() === "") {
        $(".wrap").remove();
    }

    $(".back").on("click", function () {

        $(".back").removeClass("active");
        $(".dropdown-menu").removeClass("active");
        $(".dropdown-menu .dropdown-menu").removeClass("subactive");
        $(".hidden-menu i").removeClass("no-display");
        $(".hidden-menu li").removeClass("no-display");
    });
    $(".btn-menu").on("click", function () {
        $('#navigation').css('z-index', '998')
        $(".back").removeClass("active");
        $(".dropdown-menu").removeClass("active");

        $(".hidden-menu li").removeClass("no-display");
        $(".hidden-menu i").removeClass("no-display");
    });


$('.orderCheckButton').on("click", function () {$('.top-navbar').hide()})



    $(".swiper-container > .swiper-wrapper  .product-block-wrapper-fix").unwrap();
    $(".main-content .catalog-wrap").unwrap();
    $(".swiper-container > .swiper-wrapper > div").addClass("swiper-slide");
    $(".brands-slider > .swiper-wrapper > li").addClass("swiper-slide");
    $(".gbook-slider > .swiper-wrapper > div").addClass("swiper-slide");
    if ($("#panel1 .product-block-wrapper-fix").length) {
        $("#panel2").removeClass("active");
        $("#panel2").removeClass("in");
    } else {



        var swiper1 = new Swiper(".active .spec-main-slider", {
            slidesPerView: 6,
            speed: 800,
            nextButton: ".btn-next2",
            prevButton: ".btn-prev2",
            preventClicks: false,
            effect: "slide",
            preventClicksPropagation: false,
            breakpoints: {
                550: {
                    slidesPerView: 2
                },
                730: {
                    slidesPerView: 2
                },
                950: {
                    slidesPerView: 3
                },
                1180: {
                    slidesPerView: 4
                },
                1300: {
                    slidesPerView: 5
                },
                1500: {
                    slidesPerView: 6
                }
            }
        });

    }
    var swiper7 = new Swiper(".gbook-slider", {
        slidesPerView: 3,
        speed: 800,
        nextButton: ".btn-next6",
        prevButton: ".btn-prev6",

        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            550: {
                slidesPerView: 1,
                autoHeight: true,
            },
            730: {
                slidesPerView: 2
            },
            1100: {
                slidesPerView: 3
            }
        }
    });
    var swiper4 = new Swiper(".nowBuy-slider", {
        slidesPerView: 5,
        speed: 800,
        nextButton: ".btn-next4",
        prevButton: ".btn-prev4",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            550: {
                slidesPerView: 1
            },
            730: {
                slidesPerView: 1
            },
            950: {
                slidesPerView: 3
            },
            1180: {
                slidesPerView: 4
            },
            1300: {
                slidesPerView: 4
            },
            1500: {
                slidesPerView: 4
            }
        }
    });
    var swiper5 = new Swiper(".brands-slider", {
        slidesPerView: 6,
        speed: 800,
        nextButton: ".btn-next5",
        prevButton: ".btn-prev5",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            550: {
                slidesPerView: 2
            },
            800: {
                slidesPerView: 4
            },
            1000: {
                slidesPerView: 4
            },
            1080: {
                slidesPerView: 4
            },
            1200: {
                slidesPerView: 4
            },
            1500: {
                slidesPerView: 5
            }
        }
    });
    var swiper5 = new Swiper(".compare-slider", {
        slidesPerView: 4,
        speed: 800,
        nextButton: ".btn-next10",
        prevButton: ".btn-prev10",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            550: {
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
                slidesPerView: 4
            }
        }
    });
    var swiper2 = new Swiper(".spec-main-icon-slider", {
        slidesPerView: 6,
        speed: 800,
        nextButton: ".btn-next3",
        prevButton: ".btn-prev3",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            550: {
                slidesPerView: 1
            },
            730: {
                slidesPerView: 1
            },
            950: {
                slidesPerView: 3
            },
            1180: {
                slidesPerView: 4
            },
            1300: {
                slidesPerView: 5
            },
            1500: {
                slidesPerView: 6
            }
        }
    });
    var swiper3 = new Swiper(".spec-hit-slider", {
        slidesPerView: 6,
        speed: 800,
        nextButton: ".btn-next1",
        prevButton: ".btn-prev1",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            550: {
                slidesPerView: 1
            },
            730: {
                slidesPerView: 1
            },
            950: {
                slidesPerView: 3
            },
            1180: {
                slidesPerView: 4
            },
            1300: {
                slidesPerView: 5
            },
            1500: {
                slidesPerView: 6
            }
        }
    });
    var swiper6 = new Swiper(".inner-nowbuy .last-slider", {
        slidesPerView: 4,
        speed: 800,
        nextButton: ".btn-next3",
        prevButton: ".btn-prev3",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            450: {
                slidesPerView: 1
            },
            550: {
                slidesPerView: 1
            },
            730: {
                slidesPerView: 1
            },
            950: {
                slidesPerView: 1
            },
            1180: {
                slidesPerView: 3
            },
            1300: {
                slidesPerView: 3
            },
            1500: {
                slidesPerView: 4
            }
        }
    });
     var swiper12 = new Swiper(".inner-nowbuy .list-slider", {
        slidesPerView: 4,
        speed: 800,
        nextButton: ".btn-next5",
        prevButton: ".btn-prev5",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            450: {
                slidesPerView: 1
            },
            550: {
                slidesPerView: 1
            },
            730: {
                slidesPerView: 1
            },
            950: {
                slidesPerView: 3
            },
            1180: {
                slidesPerView: 3
            },
            1300: {
                slidesPerView: 3
            },
            1500: {
                slidesPerView: 4
            }
        }
    });
    var swiper6 = new Swiper(".inner-nowbuy .nowBuy-slider", {
        slidesPerView: 4,
        speed: 800,
        nextButton: ".btn-next4",
        prevButton: ".btn-prev4",
        preventClicks: false,
        effect: "slide",
        preventClicksPropagation: false,
        breakpoints: {
            450: {
                slidesPerView: 1
            },
            550: {
                slidesPerView: 1
            },
            730: {
                slidesPerView: 1
            },
            950: {
                slidesPerView: 3
            },
            1180: {
                slidesPerView: 3
            },
            1300: {
                slidesPerView: 3
            },
            1500: {
                slidesPerView: 4
            }
        }
    });

    $("#actionTab li:nth-child(3)").click(function () {
        setTimeout(function () {
            var swiper1 = new Swiper(".spec-main-slider", {
                slidesPerView: 6,
                speed: 800,
                nextButton: ".btn-next2",
                prevButton: ".btn-prev2",
                preventClicks: false,
                effect: "slide",
                preventClicksPropagation: false,
                breakpoints: {
                    550: {
                        slidesPerView: 1
                    },
                    730: {
                        slidesPerView: 1
                    },
                    950: {
                        slidesPerView: 3
                    },
                    1180: {
                        slidesPerView: 4
                    },
                    1300: {
                        slidesPerView: 5
                    },
                    1500: {
                        slidesPerView: 6
                    }
                }
            });
        }, 400);
    });


    // Закрыть стикер в шапке
    $('.sticker-close').on('click', function (e) {
        e.preventDefault();
        $.cookie('sticker_close', 1, {
            path: '/',
            expires: 365
        });
    });

});