<!DOCTYPE html>
<html lang="@lang@">
    <head>
        <meta charset="@charset@">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title>@pageTitl@</title>
        <meta name="description" content="@pageDesc@">
        <meta name="keywords" content="@pageKeyw@">
        <meta name="copyright" content="@pageReg@">
        <link rel="apple-touch-icon" href="@icon@">
        <link rel="icon" href="@icon@" type="image/x-icon">
        <link rel="mask-icon" href="@icon@">

        <!-- OpenGraph -->
        <meta property="og:title" content="@ogTitle@">
        <meta property="og:image" content="http://@serverName@@ogImage@">
        <meta property="og:url" content="http://@ogUrl@">
        <meta property="og:type" content="website">
        <meta property="og:description" content="@ogDescription@">

        <!-- Preload -->
        <link rel="preload" href="@pathTemplateMin@css/@unit_theme@.css" as="style">
        <link rel="preload" href="@pathTemplateMin@/style.css" as="style">
        <link rel="preload" href="@pathTemplateMin@css/icomoon.css" as="style">
        <link rel="preload" href="@pathTemplate@css/font-awesome.min.css" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/rouble.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/AvenirNextCyr-Bold.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/AvenirNextCyr-Light.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/AvenirNextCyr-Regular.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/AvenirNextCyr-Medium.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="@pathTemplate@/fonts/glyphicons-halflings-regular.woff2" as="font" type="font/woff2" crossorigin>

        <!-- Bootstrap -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplateMin@css/@unit_theme@.css" rel="stylesheet">

    </head>

    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Template -->
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,700&display=swap&subset=cyrillic,cyrillic-ext" rel="stylesheet">
        <link href="@pathTemplateMin@/style.css" type="text/css" rel="stylesheet">

        <!-- Header -->
        <div class="mobile-fix-menu @hideSite@">
            <div class="d-flex justify-content-between">
                <span class="back-btn d-flex align-items-center"><i class="icons icons-prev2" style="backface-visibility: hidden;"></i> {�����}</span>
                <button type="button" class="menu-close"><span aria-hidden="true" class="fal fa-times"></span></button>
            </div>
            <ul class="m-menu">
                @leftCatal@
            </ul>
        </div>
        <header>
            <div class="container ">
                <!-- ������-������� -->
                <div class="top_banner_parent @php __hide('sticker_top'); php@">
                    <div class="top-banner @php __hide('sticker_close','cookie'); php@">
                        <div class="sticker-text">@sticker_top@</div>
                        <span class="close sticker-close">x</span>
                    </div>
                </div>
                <!-- /������-������� -->

                <div class="d-flex align-items-center justify-content-between">
                    <div class="logo">
                        <a href="/"><img src="@logo@" alt=""></a>
                    </div>
                    <div class="category-btn @hideSite@">

                        <div class="category-icon">
                            <div><svg width="24" height="5">
                                <path stroke-width="2" d="M0 5h24"></path>
                                </svg></div>
                            <div><svg width="24" height="5">
                                <path stroke="#454444" stroke-width="2" d="M0 5h24"></path>
                                </svg></div>
                            <div><svg width="24" height="5">
                                <path stroke="#454444" stroke-width="2" d="M0 5h24"></path>
                                </svg></div>
                            <div><svg width="24" height="5">
                                <path stroke="#454444" stroke-width="2" d="M0 5h24"></path>
                                </svg></div>

                        </div> <a>{���������}</a>
                    </div>
                    <div class="header-call d-flex align-items-center justify-content-start">
                        @returncall@
                        <div class="call-number d-flex flex-column">
                            <a href="tel:@telNumMobile@">@telNum@</a>
                            <a href="tel:@telNum2@">@telNum2@</a>
                        </div>
                    </div>
                    <div class="header-search">
                        <form action="/search/" role="search" method="get">
                            <div class="input-group">
                                <input name="words" maxlength="50" class="form-control search-input" placeholder="{������}.." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" data-content="">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><span
                                            class="icons icons-search"></span></button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <ul class="header-btn d-flex align-items-center justify-content-start hidden-sm @hideSite@">
                        <li role="presentation">@wishlist@</li>
                        <li role="presentation">
                            <a href="/compare/">
                                <span id="numcompare">@numcompare@</span><span class="icons icons-green icons-small icons-compare"></span></a>
                        </li>
                    </ul>
                    <div class="header-cart hidden-sm @hideCatalog@"><a id="cartlink" data-trigger="hover" data-container="#cart" data-toggle="popover" data-placement="bottom" data-html="true" data-url="/order/" data-content='@visualcart@' href="/order/"><span
                                class="icons icons-blue icons-big icons-cart"></span><span id="num" class="">@num@</span>
                            <span class="visible-lg-inline">{�������} {��} </span><span id="sum" class="">@sum@</span><span
                                class="rubznak">@productValutaName@</span></a>
                        <div id="visualcart_tmp" class="hide">@visualcart@</div>
                    </div>
                    <ul class="header-user"> @usersDisp@</ul>
                </div>
            </div>
            <div class="drop-menu drop @hideSite@">
                <div class="drop-shadow">
                    <div class="container">
                        <ul class="mobile-menu">
                            @leftCatal@
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!--/ Header -->

        <!-- Fixed navbar -->
        <div class="container sticky">
            <nav class="navbar main-navbar" id="navigation">

                <div class="navbar-header">
                    <div class="visible-xs btn-mobile-menu"><span class="icons-menu"></span></div>

                    <div class="filter-panel @hideCatalog@">
                        <div class="filter-well">
                            <div class="filter-menu-wrapper">
                                <div class="btn-group filter-menu" data-toggle="buttons">

                                    <label class="btn btn-sm btn-sort @sSetCactive@">
                                        <input type="radio" name="s" value="3"> {����������}
                                    </label>

                                    <label class="btn btn-sm btn-sort">
                                        <input type="radio" name="s" value="2&f=2"> {�������}
                                    </label>
                                    <label class="btn btn-sm btn-sort ">
                                        <input type="radio" name="s" value="2&f=1"> {�������}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <span class="filter-btn"><span class="icons icons-filter"></span>{�������}</span>
                    </div>
                    <form action="/search/" role="search" method="get" class="visible-xs  mobile-search">
                        <div class="input-group">
                            <input name="words" maxlength="50" id="search" class="form-control" placeholder="{������}.." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" data-content="">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><span
                                        class="icons icons-search"></span></button>
                            </span>
                        </div>
                    </form>
                </div>

                <div id="navbar" class="navbar-collapse collapse">
                    <div class=" header-menu-wrapper ">
                        <div class="row d-flex justify-content-between m-0">
                            <ul class="nav  main-navbar-top">

                                <!-- dropdown catalog menu -->
                                <li class="visible-xs @hideSite@">
                                    <ul class="mobile-menu">
                                        @leftCatal@
                                    </ul>
                                </li>
                                @topBrands@ 
                                @topcatMenu@ 
                                @topMenu@


                                <li class="visible-xs"><a href="/news/">{�������}</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
                <!--/.nav-collapse -->

            </nav>
        </div>
        <!-- VisualCart Mod -->

        <!-- Notification -->
        <div id="notification" class="success-notification" style="display:none">
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><i class="fal fa-times" aria-hidden="true"></i><span class="sr-only">Close</span></button>
                <span class="notification-alert"> </span>
            </div>
        </div>
        <!--/ Notification -->
        <script src="@pathTemplate@/js/jquery-2.2.5.min.js">
        </script>
        <script src="@pathMin@java/jqfunc.js"></script>

        <div class="container  main-block-content ">
            <div class="text-center banner banner-top">@banersDispHorizontal@</div>

            <div class="row">
                <div class="head-block "></div>
                <div class="clearfix"></div>
                <div class="d-flex container align-items-start">
                    <div class="left-content ">

                        <!-- �������� ������ -->
                        <div class="hide left-filter" id="faset-filter">
                            <div class="faset-filter-name text-right"><span class="close"><span class="fal fa-times" aria-hidden="true"></span></span></div>
                            <div class="panel-body faset-filter-block-wrapper">

                                <div id="faset-filter-body">{��������}</div>

                                <div id="price-filter-body" class="@hideCatalog@">
                                    <div class="h4">{����}</div>
                                    <form method="get" id="price-filter-form">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-6" id="price-filter-val-min">
                                                {��} <input type="text" class="form-control input-sm" name="min" value="@price_min@">
                                            </div>
                                            <div class="col-md-6 col-xs-6" id="price-filter-val-max">
                                                {��} <input type="text" class="form-control input-sm" name="max" value="@price_max@">
                                            </div>
                                        </div>
                                    </form>

                                    <div id="slider-range"></div>

                                </div>
                                <a href="?" id="faset-filter-reset" class="border-btn">{�������� ������}</a>
                                <span class="filter-close visible-xs">{���������}</span>
                            </div>
                        </div>
                        <!--/ �������� ������ -->
                        <!-- ProductDay Mod -->
                        @productDay@
                        <!--/ ProductDay Mod -->
                        <div class="left-info-block">
                            <div class="block  hidden-xs  @php __hide('pageCatal'); php@">
                                <div class="block-heading">
                                    <div class="block-title">{��� ���������}</div>
                                </div>
                                <ul class="block-body">
                                    @pageCatal@
                                </ul>
                            </div>

                            @leftMenu@
                            <div class="text-center banner">@banersDisp@</div>
                            <div class="panel panel-default  hidden-xs   @php __hide('productlist_list'); php@ @hideSite@">
                                <div class="panel-heading">
                                    <div class="panel-title">{������� ������}</div>
                                </div>
                                <div class="panel-body">
                                    <div id="productlist">
                                        <table>@productlist_list@</table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="bar-padding-top-fix visible-xs visible-sm"></div>
                    <div class="center-block">
                        <div class="mobile-filter-wrapper"></div>
                        @DispShop@ @getPhotos@
                        <div class="spec @php __hide('productlastview'); php@ content-product @hideSite@">
                            <div class="">
                                <div class="">
                                    <div class="inner-nowbuy main">

                                        <div class="product-head page-header not-center">
                                            @productlastview_title@
                                        </div>

                                        <div class="swiper-slider-wrapper">
                                            <div class="swiper-button-prev-block">
                                                <div class="swiper-button-prev btn-prev8">
                                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="swiper-button-next-block">
                                                <div class="swiper-button-next btn-next8">
                                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                            <div class="swiper-container last-slider2">
                                                <div class="swiper-wrapper">
                                                    @productlastview@
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- toTop -->
            <div class="visible-lg visible-md">
                <a href="#" id="toTop"><span id="toTopHover"></span>{������}</a>
            </div>
            <!--/ toTop -->

        </div>
        <section class="specMain @php __hide('productOdnotipList'); php@ @hideSite@">
            <div class="container">
                <div class="">

                    <div class="product-head page-header not-center">
                        @productOdnotip@
                    </div>

                    <div class="swiper-slider-wrapper">
                        <div class="swiper-button-prev-block">
                            <div class="swiper-button-prev btn-prev-odnotip">
                                <i class="fa fa-angle-left" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="swiper-button-next-block">
                            <div class="swiper-button-next btn-next-odnotip">
                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="swiper-container last-slider-odnotip">
                            <div class="swiper-wrapper">
                                @productOdnotipList@
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="specMain @php __hide('productlist_list'); php@ @hideSite@">
            <div class="container">
                <div>
                    <div class="product-head page-header not-center">
                        @productlist_title@
                    </div>
                    <div class="swiper-slider-wrapper">
                        <div class="swiper-button-prev-block">
                            <div class="swiper-button-prev btn-prev6">
                                <i class="fa fa-angle-left" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="swiper-button-next-block">
                            <div class="swiper-button-next btn-next6">
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
        </section>
         <section class="specMain @php __hide('productsimilar_list'); php@ @hideSite@">
            <div class="container">
                <div>
                    <div class="product-head page-header not-center">
                        @productsimilar_title@
                    </div>
                    <div class="swiper-slider-wrapper">
                        <div class="swiper-button-prev-block">
                            <div class="swiper-button-prev btn-prev6">
                                <i class="fa fa-angle-left" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="swiper-button-next-block">
                            <div class="swiper-button-next btn-next6">
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
        </section>
        <section class="specMain @php __hide('productlastview'); php@ last-see-product @hideSite@">
            <div class="container">
                <div >
                    <div class="product-head page-header not-center">
                        @productlastview_title@
                    </div>
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
        </section>

        <footer class="footer">
            <div class="container">
                <div class="col-md-3 col-sm-4 col-xs-12" itemscope itemtype="http://schema.org/Organization">
                    <div class="logo @php __hide('logo'); php@">
                        <a href="/"><img src="@logo@" alt=""></a>
                    </div>
                    <ul>
                        <li>&copy; <span itemprop="name">@company@</span>, @year@</li>
                        <li><span itemprop="email">@adminMail@</span></li>
                        <li><span itemprop="telephone">@telNum@</span></li>
                        <li itemprop="telephone">@telNum2@</li>
                        <li>@workingTime@</li>
                        <li itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"> <span itemprop="streetAddress">@streetAddress@</span></li>
                        <li>@button@</li>
                    </ul>
                </div>

                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="h5">{��� �������}</div>
                    <ul>
                        <li class="@hideCatalog@">
                            @php if($_SESSION['UsersId']) echo
                            '<a href="/users/order.html">{��������� �����}</a>';
                            else echo '
                            <a href="#" data-toggle="modal" data-target="#userModal">{��������� �����}</a>'; php@
                        </li>
                        <li class="@hideCatalog@"><a href="/users/notice.html">{����������� � �������}</a></li>
                        @php if($_SESSION['UsersId']) echo '
                        <li><a href="/users/message.html">{����� � �����������}</a>
                        </li>
                        <li><a href="?logout=true">{�����}</a></li>';else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{�����}</a></li>';php@
                    </ul>
                </div>
                <!-- My Account Links Ends -->

                <!-- Information Links Starts -->
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="h5">{����}</div>
                    <ul>
                        @bottomMenu@
                    </ul>
                </div>
                <!-- Information Links Ends -->
                <div class="col-md-3 col-sm-4 col-xs-12"> 
                    <!-- ���������� ���� -->
                    <ul class="social-menu list-inline">
                        <li class="list-inline-item @php __hide('vk'); php@"><a class="social-button header-top-link" title="���������" href="@vk@" target="_blank"><em class="fa fa-vk" aria-hidden="true">.</em></a></li>
                        <li class="list-inline-item @php __hide('telegram'); php@"><a class="social-button header-top-link" title="Telegram" href="@telegram@" target="_blank"> <em class="fa fa-telegram" aria-hidden="true">.</em></a></li>
                        <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="social-button header-top-link" title="�������������" href="@odnoklassniki@" target="_blank"> <em class="fa fa-odnoklassniki" aria-hidden="true">.</em></a></li>
                        <li class="list-inline-item @php __hide('youtube'); php@"><a class="social-button header-top-link" title="Rutube" href="@youtube@" target="_blank"><em class="fa fa-youtube-play" aria-hidden="true">.</em></a></li>
                        <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="social-button header-top-link" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fa fa-whatsapp" aria-hidden="true">.</em></a></li>
                    </ul>
                    <!-- / ���������� ���� -->

                    <ul>
                        <li class="@hideCatalog@"><a href="/price/" title="�����-����">{�����-����}</a></li>
                        <li><a href="/news/" title="�������">{�������}</a></li>
                        <li><a href="/gbook/" title="������">{������}</a></li>
                        <li class="@hideSite@"><a href="/map/" title="����� �����">{����� �����}</a></li>
                        <li><a href="/forma/" title="����� �����">{����� �����}</a></li>
                    </ul>  
                </div>

            </div>
        </footer>
    </div>

    <!-- ��������� ���� ���������� ������ -->
    <div class="modal fade bs-example-modal-sm" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span
                            class="sr-only">Close</span></button>
                    <div class="modal-title">{�����}</div>
                </div>
                <div class="modal-body">
                    <form action="/search/" role="search" method="get">
                        <div class="input-group">
                            <input name="words" maxlength="50" class="form-control" placeholder="{������}.." required="" type="search">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><span
                                        class="icons icons-search"></span></button>
                            </span>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!--/ ��������� ���� ���������� ������ -->

    <!-- ��������� ���� �����������-->
    <div class="modal fade bs-example-modal-sm" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm auto-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <div class="h4 modal-title">{�����������}</div>
                    <span id="usersError" class="hide">@usersError@</span>
                </div>
                <form method="post" name="user_forma">
                    <div class="modal-body">
                        <div class="form-group">

                            <input type="email" name="login" class="form-control" placeholder="Email" required="" value="@UserLogin@">
                            <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                            <br>

                            <input type="password" name="password" class="form-control" placeholder="{������}" required="" value="@UserPassword@">
                            <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                        </div>
                        <div class="flex-row">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="safe_users" @UserChecked@> {���������}
                                </label>
                            </div>
                            <a href="/users/sms.html" class="pass @sms_login_enabled@">SMS</a>
                            <a href="/users/sendpassword.html" class="pass">{������ ������}</a>
                        </div>

                    </div>
                    <div class="modal-footer flex-row">

                        <input type="hidden" value="1" name="user_enter">
                        <button type="submit" class="btn btn-primary">{�����}</button>
                        <a href="/users/register.html">{������������������}</a>
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
    <!--/ ��������� ���� �����������-->

    @editor@

    <!-- Fixed mobile bar -->
    <div class="bar-padding-fix visible-xs visible-sm"></div>
    <nav class="navbar navbar-fixed-bottom bar bar-tab visible-xs visible-sm">
        <div class="d-flex justify-content-between align-center">

            <a class="@cart_active@ @hideCatalog@" href="/order/" id="bar-cart">
                <span class="icons-cart icons"></span> <span id="sum2" class="">@sum@</span><span class="rubznak">@productValutaName@</span>

            </a>
            <a class="links @hideSite@" href="/users/wishlist.html">
                <span class="wishlistcount">@wishlistCount@</span><span class="icons icons-wishlist  icons-green icons-small"></span>

            </a>
            <a class="links @hideSite@" href="/compare/">
                <span id="mobilnum">@numcompare@</span><span class="icons icons-compare  icons-green icons-small"></span>

            </a>
            <a class=" @user_active@" @user_link@ data-target="#userModal">
                <span class="icons icons-user"></span>

            </a>
        </div>
    </nav>
    <!--/ Fixed mobile bar -->

    <div class="modal fade new-modal" id="noticeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog small-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <div class="h4 modal-title d-flex" id="exampleModalLabel">{��������� ��� ��������� ������ � �������}</div>
                </div>
                <div class="modal-body">
                    <div class="h4">
                        <a href="#" title="" class="notice-product-link"></a>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="image notice-product-image"></div>
                        </div>
                    </div>
                    <form method="post" name="ajax-form" action="phpshop/ajax/notice.php" data-modal="noticeModal">
                        <div class="form-group">
                            <div class=""></div>
                            <div class="">
                                <input placeholder="{���}" type="text" name="name_new" class="form-control" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                            </div>
                            <div class="">
                                <input placeholder="E-mail" type="email" name="mail" class="form-control" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                            </div>
                            <div class="">
                                <input placeholder="{�������}" type="text" name="tel_new" value="" class="form-control" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div></div>
                            <div>
                                <textarea class="form-control" name="message" id="message" placeholder="{�������������� ����������}"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="date">{�� �������}:</label>
                            <div class="input-group">
                                <select class="form-control" name="date" id="date">
                                    <option value="1" SELECTED>1 {������}</option>
                                    <option value="2">2 {�������}</option>
                                    <option value="3">3 {�������}</option>
                                    <option value="4">4 {�������}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                            </div>
                            <div class="">
                                @notice_captcha@
                            </div>
                        </div>
                        <p class="small">
                            <label>
                                <input name="rule" value="1" required="" checked="" type="checkbox">
                                @rule@
                            </label>
                        </p>
                        <div class="form-group ">
                            <div class=""></div>
                            <div class="">
                                <input type="hidden" class="notice-product-id" name="productId">
                                <input type="hidden" name="ajax" value="1">
                                <button type="button" class="btn btn-default" data-dismiss="modal">�������</button>
                                <button type="submit" class="btn btn-default">{���������}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="thanks-box" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- ��������� ���������� ���� -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>

                </div>
                <!-- �������� ���������� ���������� ���� -->
                <div class="modal-body">

                </div>
                <!-- ����� ���������� ���� -->
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <!-- �������� �� ������������� cookie  -->
    <div class="cookie-message hide">
        <p></p><a href="#" class="btn btn-default btn-sm">Ok</a>
    </div>
    <link href="@pathTemplate@css/fontawesome-light.css" rel="stylesheet">
    <link rel="stylesheet" href="@pathTemplate@css/solid-menu.css">
    <link rel="stylesheet" href="@pathTemplate@css/menu.css">
    <link href="@pathTemplate@css/suggestions.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/bootstrap-select.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/jquery-ui.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/jquery.bxslider.css" rel="stylesheet">
    <link href="@pathTemplate@css/swiper.min.css" rel="stylesheet">
    <link href="@pathTemplateMin@css/bar.css" rel="stylesheet">
    <link rel="stylesheet" href="@pathTemplate@css/owl.carousel.min.css">
    <link rel="stylesheet" href="@pathTemplate@css/owl.theme.default.min.css">
    <script src="@pathTemplate@/js/owl.carousel.min.js"></script>
    <link href="@pathTemplate@css/touchnswipe.min.css" rel="stylesheet">
    <link href="@pathTemplate@css/tns_prod.min.css" rel="stylesheet">
    <script src="@pathTemplate@/js/TweenMax.min.js"></script>
    <script src="@pathTemplate@/js/tooltipster.bundle.min.js"></script>
    <script src="@pathTemplate@/js/popper.min.js"></script>
    <script src="@pathTemplate@/js/hammer.min.js"></script>
    <script src="@pathTemplate@/js/jquery.touchnswipe.min.js"></script>
    <script src="@pathTemplate@/js/bootstrap.min.js"></script>
    <script src="@pathTemplate@/js/bootstrap-select.min.js"></script>
    <script src="@pathTemplate@/js/jquery.lazyloadxt.min.js"></script>
    <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>
    <script src="@pathTemplate@/js/swiper.min.js"></script>
    <script src="@pathTemplate@/js/sticky.js"></script>
    <script src="@pathTemplate@/js/phpshop.js"></script>
    <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
    <script src="@pathTemplate@/js/flipclock.min.js"></script>
    <script src="@pathTemplate@/js/jquery.cookie.js"></script>
    <script src="@pathTemplate@/js/jquery.waypoints.min.js"></script>
    <script src="@pathTemplate@/js/inview.min.js"></script>
    <script src="@pathTemplate@/js/jquery-ui.min.js"></script>
    <script src="@pathTemplate@/js/jquery.bxslider.min.js"></script>
    <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
    <script src="@pathTemplate@/js/solid-menu.js"></script>

    @visualcart_lib@
    <div class="visible-lg visible-md">