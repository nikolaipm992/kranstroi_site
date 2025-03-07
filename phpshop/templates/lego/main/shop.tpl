<!DOCTYPE html>
<html lang="@lang@" prefix="og: https://ogp.me/ns#">
    <head>
        <meta charset="@charset@">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@pageTitl@</title>
        <meta name="description" content="@pageDesc@">
        <meta name="keywords" content="@pageKeyw@">
        <meta name="copyright" content="@pageReg@">

        <!-- OpenGraph -->
        <meta property="og:title" content="@ogTitle@">
        <meta property="og:image" content="http://@serverName@@ogImage@">
        <meta property="og:url" content="http://@ogUrl@">
        <meta property="og:type" content="website">
        <meta property="og:description" content="@ogDescription@">

        <link rel="apple-touch-icon" href="@icon@">
        <link rel="icon" href="@icon@" type="image/x-icon">
        <link rel="mask-icon" href="@icon@" >

        <!-- Preload -->
        <link rel="preload" href="@pathTemplate@css/bootstrap.min.css" as="style">
        <link rel="preload" href="@pathTemplateMin@/style.css" as="style">
        <link rel="preload" href="@pathTemplate@css/icomoon.css" as="style">
        <link rel="preload" href="@pathTemplate@/fonts/rouble.woff"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/StemMedium/StemMedium.woff2"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/StemLight/StemLight.woff2"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/fa-light-300.woff2"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/fontawesome-webfont.woff?v=4.2.0"  as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/icomoon/icomoon.ttf?c5w26q"  as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link href="@pathTemplate@css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplateMin@css/@lego_theme@.css" rel="stylesheet">
        <link href="@pathTemplateMin@/style.css" type="text/css" rel="stylesheet">

        <!-- Header -->
        @header@
        <!--/ Header -->

        <!-- jQuery -->
        <script src="@pathTemplate@/js/jquery-1.11.0.min.js"></script>
        <script src="@pathMin@java/jqfunc.js"></script>

        <!-- Container -->
        @container@
        <!--/ Container -->

        <section class="spec @php __hide('specMain'); php@ @hideSite@">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-xs-12 main">
                        <h2 class="product-head page-header">
                            <a href="/spec/" title="{Спецпредложения}">{Спецпредложения}</a>
                        </h2>
                        <div class="swiper-slider-wrapper">
                            <div class="swiper-button-prev-block">
                                <div class="swiper-button-prev btn-prev1">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-button-next-block">
                                <div class="swiper-button-next btn-next1">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-container spec-hit-slider">
                                <div class="swiper-wrapper">
                                    @specMain@
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <section class="spec @php __hide('productlist_list'); php@ @hideSite@">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-xs-12 inner-nowbuy main">

                        <h2 class="product-head page-header">
                            @productlist_title@
                        </h2>

                        <div class="swiper-slider-wrapper">
                            <div class="swiper-button-prev-block">
                                <div class="swiper-button-prev btn-prev5">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-button-next-block">
                                <div class="swiper-button-next btn-next5">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-container list-slider">
                                <div class="swiper-wrapper">
                                    <div class="row"> @productlist_list@</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <section class="spec @php __hide('productsimilar_list'); php@ @hideSite@">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-xs-12 inner-nowbuy main">

                        <h2 class="product-head page-header">
                            @productsimilar_title@
                        </h2>

                        <div class="swiper-slider-wrapper">
                            <div class="swiper-button-prev-block">
                                <div class="swiper-button-prev btn-prev5">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-button-next-block">
                                <div class="swiper-button-next btn-next5">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-container list-slider">
                                <div class="swiper-wrapper">
                                    <div class="row"> @productsimilar_list@</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <section class="spec @php __hide('productlastview'); php@ @hideSite@">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-xs-12 inner-nowbuy main">

                        <h2 class="product-head page-header">
                            <a href="" title="@productlastview_title@">@productlastview_title@</a>
                        </h2>

                        <div class="swiper-slider-wrapper">
                            <div class="swiper-button-prev-block">
                                <div class="swiper-button-prev btn-prev3">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-button-next-block">
                                <div class="swiper-button-next btn-next3">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-container last-slider">
                                <div class="swiper-wrapper">
                                    @productlastview@
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <section class="brands @php __hide('brandsList'); php@ @hideSite@">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-xs-12 main">

                        <div class="swiper-slider-wrapper">
                            <div class="swiper-button-prev-block">
                                <div class="swiper-button-prev btn-prev5">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-button-next-block">
                                <div class="swiper-button-next btn-next5">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-container brands-slider">
                                <ul class="swiper-wrapper">
                                    @brandsList@
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- toTop -->
        <div class="visible-lg visible-md">
            <a href="#" id="toTop"><span id="toTopHover"></span>{Наверх}</a>
        </div>
        <!--/ toTop -->

        <!-- Footer Section Starts -->
        @footer@
        <!-- Footer Section Ends -->


        <div class="modal product-number-fix fade bs-example-modal-sm" id="modalProductView" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg fastViewContent"></div>
        </div>

        <!-- Модальное окно мобильного поиска -->
        <div class="modal fade bs-example-modal-sm" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">{Поиск}</h4>
                    </div>
                    <div class="modal-body">
                        <form  action="/search/" role="search" method="get">
                            <div class="input-group">
                                <input name="words" maxlength="50" class="form-control" placeholder="Искать.." required="" type="search">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                </span>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!--/ Модальное окно мобильного поиска -->

        <!-- Модальное окно авторизации-->
        <div class="modal fade bs-example-modal-sm" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm auto-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">{Авторизация}</h4>
                        <span id="usersError" class="hide">@usersError@</span>
                    </div>
                    <form method="post" name="user_forma">
                        <div class="modal-body">
                            <div class="form-group">

                                <input type="email" name="login" class="form-control" placeholder="Email" required="" value="@UserLogin@">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                                <br>

                                <input type="password" name="password" class="form-control" placeholder="{Пароль}" required="" value="@UserPassword@">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                            </div>
                            <div class="flex-row">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="1" name="safe_users" @UserChecked@> {Запомнить}
                                    </label>
                                </div>
                                <a href="/users/sms.html" class="pass @sms_login_enabled@">SMS</a>
                                <a href="/users/sendpassword.html" class="pass">{Забыли пароль}</a>
                            </div>

                        </div>
                        <div class="modal-footer flex-row">

                            <input type="hidden" value="1" name="user_enter">
                            <button type="submit" class="btn btn-primary">{Войти}</button>

                            <a href="/users/register.html">{Зарегистрироваться}</a>
                        </div>

                        <!-- Yandex ID -->
                        @yandexid@
                        <!-- End Yandex ID -->

                        <!-- VK ID -->
                        @vkid@
                        <!-- End VK ID -->
                    </form>
                </div>
            </div>
        </div>
        <!--/ Модальное окно авторизации-->

        @editor@

        <div class="search-big-block">
            <form id="search_form_small" action="/search/" role="search" method="get" class="header-search-form">
                <input class="form-control input-lg" name="words" maxlength="50"  placeholder="{Поиск}..." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                <button class="header-search-button" type="submit">
                    <i class="icons-search"></i>
                </button>
            </form>
            <i class="search-close fa fa-times" aria-hidden="true"></i>
        </div>

        <!-- Согласие на использование cookie  -->
        <div class="cookie-message hide"><p></p><a href="#" class="btn btn-default btn-sm">Ok</a></div>

        <link href="@pathTemplate@css/swiper.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/suggestions.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/bootstrap-select.min.css" rel="stylesheet"> 
        <link href="@pathTemplate@css/jquery-ui.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/jquery.bxslider.css" rel="stylesheet">
        <link href="@pathTemplateMin@css/bar.css" rel="stylesheet">
        <link href="@pathTemplate@css/fontawesome-light.css" rel="stylesheet">
        <link href="@pathTemplate@css/touchnswipe.min.css" rel="stylesheet">
        <link href="@pathTemplate@css/tns_prod.min.css" rel="stylesheet">
        <script src="@pathTemplate@/js/tooltipster.bundle.min.js"></script>
        <script src="@pathTemplate@/js/popper.min.js"></script>
        <script src="@pathTemplate@/js/hammer.min.js"></script>
        <script src="@pathTemplate@/js/TweenMax.min.js"></script>
        <script src="@pathTemplate@/js/jquery.touchnswipe.min.js"></script>
        <script src="@pathTemplate@/js/bootstrap.min.js"></script>
        <script src="@pathTemplate@/js/swiper.min.js"></script>
        <script src="@pathTemplate@/js/jquery.elevatezoom.js"></script>
        <script src="@pathTemplate@/js/sticky-sidebar.js"></script>
        <script src="@pathTemplate@/js/jquery.lazyloadxt.min.js"></script>
        <script src="@pathTemplate@/js/imagesloaded.pkgd.js"></script>
        <script src="@pathTemplate@/js/masonry.pkgd.min.js"></script>
        <script src="@pathTemplate@/js/bootstrap-select.min.js"></script>
        <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
        <script src="@pathTemplate@/js/flipclock.min.js"></script>
        <script src="@pathTemplate@/js/lego.js"></script>
        <script src="@pathTemplate@/js/phpshop.js"></script>
        <script src="@pathTemplate@/js/jquery.cookie.js"></script>
        <script src="@pathTemplate@/js/jquery.waypoints.min.js"></script>
        <script src="@pathTemplate@/js/inview.min.js"></script>
        <script src="@pathTemplate@/js/jquery-ui.min.js"></script>
        <script src="@pathTemplate@/js/jquery.bxslider.min.js"></script>
        <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
        <script src="@pathTemplate@/js/jquery.ui.touch-punch.min.js"></script>

        @visualcart_lib@
        <div class="visible-lg visible-md">
