<!DOCTYPE html>
<html lang="@lang@">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta charset="@charset@">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@pageTitl@</title>
        <meta name="description" content="@pageDesc@">
        <meta name="keywords" content="@pageKeyw@">
        <meta name="copyright" content="@pageReg@">

        <link rel="apple-touch-icon" href="@icon@">
        <link rel="icon" href="@icon@" type="image/x-icon">
        <link rel="mask-icon" href="@icon@">
        <link rel="shortcut icon" href="@icon@">

        <!-- OpenGraph -->
        <meta property="og:title" content="@ogTitle@">
        <meta property="og:image" content="http://@serverName@@ogImage@">
        <meta property="og:url" content="http://@ogUrl@">
        <meta property="og:type" content="website">
        <meta property="og:description" content="@ogDescription@">

        <!-- Preload -->
        <link rel="preload" href="@pathTemplate@/fonts/rouble.woff" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" as="style">
        <link rel="preload" href="@pathTemplateMin@/style.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/fontawesome/css/all.min.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/hs-mega-menu/dist/hs-mega-menu.min.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/slick-carousel/slick/slick.css" as="style">
        <link rel="preload" href="@pathTemplate@/assets/vendor/select2/dist/css/select2.min.css" as="style">
        <link rel="preload" href="@pathTemplate@css/suggestions.min.css" as="style">

        <!-- CSS Front Template -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplate@css/@auto_theme@.css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Font -->
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

        <!-- CSS Implementing Plugins -->
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/fontawesome/css/all.min.css">
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/hs-mega-menu/dist/hs-mega-menu.min.css">
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/slick-carousel/slick/slick.css">
        <link rel="stylesheet" href="@pathTemplate@/assets/vendor/select2/dist/css/select2.min.css">
        <link rel="stylesheet" href="@pathTemplate@css/suggestions.min.css">
        <link rel="stylesheet" href="@pathTemplate@/css/jquery-ui.min.css">

        <!-- Core CSS -->
        <link href="@pathTemplateMin@/style.css" type="text/css" rel="stylesheet">

        <!-- Search Content -->
        <div id="searchSlideDown" class="hs-unfold-content dropdown-unfold search-slide-down">
            <form action="/search/" method="get">
                <!-- Input Group -->
                <div class="input-group input-group-borderless search-slide-down-input bg-white rounded mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <input id="searchSlideDownControl" name="words" type="search" class="form-control" placeholder="{� ���...}" required="" aria-label="Search Front">
                    <div class="input-group-append">
                        <a class="js-hs-search-unfold-invoker input-group-text" href="javascript:;"
                           data-hs-unfold-options='{
                           "target": "#searchSlideDown",
                           "type": "css-animation",
                           "animationIn": "fadeIn search-slide-down-show",
                           "animationOut": "fadeOutUp",
                           "cssAnimatedClass": null,
                           "hasOverlay": true,
                           "overlayClass": ".search-slide-down-bg-overlay",
                           "overlayStyles": {
                           "background": "rgba(55, 125, 255, .1)"
                           },
                           "duration": 800,
                           "hideOnScroll": false
                           }'>
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <!-- End Input Group -->

                <!-- Suggestions Content -->
                <div class="rounded bg-white search-slide-down-suggestions py-3 px-5">
                    <div class="container mb-0" id="searchSlideDownContent">
                    </div>
                </div>
                <!-- End Suggestions Content -->
            </form>
        </div>
        <!-- End Search Content -->

        <!-- ========== HEADER ========== -->
        <header id="header" class="header left-aligned-navbar">

            <!-- ������-������� -->
            <div class="alert alert-soft-primary text-center alert-dismissible font-size-1 fade show @php __hide('sticker_top');__hide('sticker_close','cookie'); php@" role="alert">@sticker_top@<button type="button" class="close sticker-close" data-dismiss="alert" aria-label="Close"><svg aria-hidden="true" class="mb-0" width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/></svg></button>
            </div>
            <!-- ����� ������-������� -->


            <div class="header-section">

                <!-- Topbar -->
                <div class="container header-hide-content pt-2">
                    <div class="d-flex align-items-center">
                        <p class="small text-muted mb-0 d-none d-sm-flex">@name@</p>
                        <!-- Search -->
                        <li class="list-inline-item d-sm-block d-md-block d-lg-none ">
                            <div class="hs-unfold">

                                <a class="js-hs-search-unfold-invoker btn btn-xs btn-icon btn-ghost-secondary search-slide-down-trigger" href="javascript:;" role="button"
                                   data-hs-unfold-options='{
                                   "target": "#searchSlideDown",
                                   "type": "css-animation",
                                   "animationIn": "fadeIn search-slide-down-show",
                                   "animationOut": "fadeOutUp",
                                   "cssAnimatedClass": null,
                                   "hasOverlay": true,
                                   "overlayClass": ".search-slide-down-bg-overlay",
                                   "overlayStyles": {
                                   "background": "rgba(55, 125, 255, .1)"
                                   },
                                   "duration": 800,
                                   "hideOnScroll": false
                                   }'>
                                    <i class="fas fa-search search-slide-down-trigger-icon"></i>
                                </a>

                            </div>
                        </li>
                        <!-- End Search -->

                        @usersDisp@

                        </li>
                        <!-- End Button -->

                        <div class="ml-auto">

                            <!-- Links -->
                            <div class="nav nav-sm nav-y-0  ml-sm-auto">
                                @returncall@
                                <p class="nav-link small text-muted mb-0"><a href="tel:@telNumMobile@">@telNum@</a></p>
                                <p class="nav-link small text-muted mb-0 d-none d-sm-flex"><a href="tel:@telNum2@">@telNum2@</a></p>
                            </div>
                            <!-- End Links -->
                        </div>

                        <ul class="list-inline ml-2 mb-0">


                            <!-- Button -->
                            <!-- Shopping Cart -->
                            <li class="navbar-nav-last-item d-none d-lg-block d-sm-none d-md-none">
                                <div class="hs-unfold @hideCatalog@">
                                    <a class="js-hs-unfold-invoker btn btn-icon " href="/order/"
                                       data-hs-unfold-options='{
                                       "target": "#shoppingCartDropdown",
                                       "type": "css-animation",
                                       "event": "hover",
                                       "hideOnScroll": "true"
                                       }'>
                                        <i class="fas fa-shopping-cart"></i>
                                        <sup class="avatar-status avatar-primary cartnum @php __hide('num'); php@">@num@</sup>
                                    </a>

                                    <div id="shoppingCartDropdown" class="hs-unfold-content dropdown-menu dropdown-menu-right dropdown-card" style="min-width: 275px;">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <span class="cartnum">@num@</span>
                                                <span class="visible-lg-inline">{�������} {��} </span><span id="sum" class="">@sum@</span> <span
                                                    class="rubznak">@productValutaName@</span></a>
                                            </div>

                                            @visualcart@

                                            <div class="card-footer text-center @php __hide('num'); php@" id="order">
                                                <a class="btn btn-primary btn-pill transition-3d-hover px-5"  href="/order/">{�������� �����}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Shopping Cart -->

                                <a class="btn btn-icon @hideSite@" href="/users/wishlist.html" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{���������}">
                                    <i class="fas fa-heart"></i>
                                    <sup class="avatar-status avatar-primary wishlistcount @php __hide('wishlistCount'); php@">@wishlistCount@</sup>
                                </a>
                                <a class="btn btn-icon @hideSite@" href="/compare/" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{��������}">
                                    <i class="fas fa-balance-scale"></i></i>
                                    <sup class="avatar-status avatar-primary numcompare @php __hide('numcompare'); php@">@numcompare@</sup>
                                </a>


                        </ul>
                    </div>
                </div>
                <!-- End Topbar -->

                <div id="logoAndNav" class="container">
                    <!-- Nav -->
                    <nav class="js-mega-menu navbar navbar-expand-lg">

                        <!-- Logo -->
                        <a class="navbar-brand @php __hide('logo'); php@" href="/" aria-label="" title="{�����}">
                            <img src="@logo@" alt="">
                        </a>
                        <!-- End Logo -->

                        <!-- Shopping Cart -->
                        <a class="navbar-toggler btn btn-icon btn-sm rounded-circle @hideCatalog@" href="/order/">
                            <i class="fas fa-shopping-cart"></i>
                            <sup class="avatar-status avatar-primary cartnum @php __hide('num'); php@">@num@</sup>
                        </a>
                        <!-- End Shopping Cart -->

                        <a class="navbar-toggler btn btn-icon btn-sm rounded-circle @hideSite@" href="/users/wishlist.html" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{���������}">
                            <i class="fas fa-heart"></i>
                            <sup class="avatar-status avatar-primary wishlistcount @php __hide('wishlistCount'); php@">@wishlistCount@</sup>
                        </a>
                        <a class="navbar-toggler btn btn-icon btn-sm rounded-circle @hideSite@" href="/compare/" data-toggle="tooltip" data-placement="top" title="" tabindex="0" data-original-title="{��������}">
                            <i class="fas fa-balance-scale"></i></i>
                            <sup class="avatar-status avatar-primary numcompare @php __hide('numcompare'); php@">@numcompare@</sup>
                        </a>

                        <!-- Responsive Toggle Button -->
                        <button type="button" class="navbar-toggler btn btn-icon btn-sm "
                                aria-label="Toggle navigation"
                                aria-expanded="false"
                                aria-controls="navBar"
                                data-toggle="collapse"
                                data-target="#navBar">
                            <span class="navbar-toggler-default">
                                <svg width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M17.4,6.2H0.6C0.3,6.2,0,5.9,0,5.5V4.1c0-0.4,0.3-0.7,0.6-0.7h16.9c0.3,0,0.6,0.3,0.6,0.7v1.4C18,5.9,17.7,6.2,17.4,6.2z M17.4,14.1H0.6c-0.3,0-0.6-0.3-0.6-0.7V12c0-0.4,0.3-0.7,0.6-0.7h16.9c0.3,0,0.6,0.3,0.6,0.7v1.4C18,13.7,17.7,14.1,17.4,14.1z"/>
                                </svg>
                            </span>
                            <span class="navbar-toggler-toggled">
                                <svg width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                                </svg>
                            </span>
                        </button>
                        <!-- End Responsive Toggle Button -->

                        <!-- Navigation -->
                        <div id="navBar" class="collapse navbar-collapse">
                            <div class="navbar-body header-abs-top-inner">
                                <ul class="navbar-nav">
                                    @php

                                    if(PHPShopParser::get('banersDispMenu')!="")
                                    $GLOBALS['catalogmenu_col'] = 6;
                                    else $GLOBALS['catalogmenu_col'] = 12;

                                    php@

                                    <!-- Catalog -->
                                    <li class="hs-has-mega-menu navbar-nav-item @hideSite@">

                                        <a id="basicMegaMenu" class="hs-mega-menu-invoker nav-link nav-link-toggle" aria-haspopup="true" aria-expanded="false">{�������}</a>

                                        <!-- Nav Item - Mega Menu -->
                                        <div class="hs-mega-menu w-100 dropdown-menu" aria-labelledby="basicMegaMenu">
                                            <div class="row no-gutters">
                                                <div class="col-lg-6 @php __hide('banersDispMenu'); php@">

                                                    <!-- Banner Image -->
                                                    @banersDispMenu@
                                                    <!-- End Banner Image -->

                                                </div>
                                                <div class="col-lg-@php echo $GLOBALS['catalogmenu_col']; php@">
                                                    <div class="row mega-menu-body">
                                                        @topCatal@
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Nav Item - Mega Menu -->
                                    </li>
                                    <!-- End Catalog -->

                                    <!-- Brand -->
                                    <li class="hs-has-sub-menu navbar-nav-item @php __hide('topBrands'); php@ @hideSite@">
                                        <a id="blogMegaMenu" class="hs-mega-menu-invoker nav-link nav-link-toggle " href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="blogSubMenu">{������}</a>

                                        <div id="blogSubMenu" class="hs-sub-menu dropdown-menu" aria-labelledby="blogMegaMenu" style="min-width: 230px;">
                                            @topBrands@
                                        </div>

                                    </li>
                                    <!-- End Brand -->

                                    <!-- Catalog  Menu-->
                                    @topcatMenu@
                                    <!-- End Catalog  Menu-->

                                    <!-- Menu -->
                                    @php
                                    if(empty(PHPShopParser::get('hideSite')) and !empty(PHPShopParser::get('topMenu'))){
                                    $GLOBALS['search_placeholder']='������� ��� ������������';
                                    echo '<li class="hs-has-sub-menu navbar-nav-item">
                                        <a id="blogMegaMenu" class="hs-mega-menu-invoker nav-link nav-link-toggle " href="javascript:;" aria-haspopup="true" aria-expanded="false" aria-labelledby="blogSubMenu">{���������}</a>

                                        <div id="blogSubMenu" class="hs-sub-menu dropdown-menu" aria-labelledby="blogMegaMenu" style="min-width: 230px;">
                                            @topMenu@
                                        </div>

                                    </li>';
                                    }
                                    else {
                                    $GLOBALS['search_placeholder']='� ���...';
                                    echo '<li class="navbar-nav-item">@topMenu@</li>';
                                    }
                                    php@
                                    <!-- End Menu-->

                                    <div class="col-md-6 navbar-nav-last-item d-none d-lg-block d-sm-none d-md-none">
                                        <form class="input-group" action="/search/" method="get">
                                            <input type="search" name="words" class="form-control form-control-sm" placeholder="{@php echo $GLOBALS['search_placeholder'];  php@}" aria-label="{������� ��� ������������}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search search-slide-down-trigger-icon"></i></button>
                                            </div>
                                        </form>
                                    </div>

                                </ul>
                            </div>
                        </div>
                        <!-- End Navigation -->
                    </nav>
                    <!-- End Nav -->
                </div>
            </div>
        </header>
        <!-- ========== END HEADER ========== -->

        <!-- ========== MAIN CONTENT ========== -->
        <main id="content" role="main">

            <!-- ��������� � shop.tpl -->
            <div class="bg-light">
                <div class="container py-5">
                    <div class="row align-items-sm-center">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <b class="h4 mb-0 shop-page-main-title">&nbsp;</b>
                        </div>

                        <div class="col-sm-6">
                            <!-- Breadcrumb -->
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-no-gutter justify-content-sm-end mb-0" itemscope itemtype="http://schema.org/BreadcrumbList">
                                    @breadCrumbs@
                                </ol>
                            </nav>
                            <!-- End Breadcrumb -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- ����� ��������� -->

            <script src="@pathTemplate@/assets/vendor/jquery/dist/jquery.min.js"></script>
            <script src="@pathMin@java/jqfunc.js"></script>

            @php
            if($GLOBALS['PHPShopNav']->objNav['name'] == 'UID' or  $GLOBALS['PHPShopNav']->objNav['path'] == 'order' or  $GLOBALS['PHPShopNav']->objNav['path'] == 'done' or $GLOBALS['PHPShopNav']->objNav['path'] == 'news' or $GLOBALS['PHPShopNav']->objNav['path'] == 'users' or $GLOBALS['PHPShopNav']->objNav['path'] == 'page'){
            $GLOBALS['container_style'] = null;
            $GLOBALS['container_col'] = 12;
            $GLOBALS['SysValue']['other']['leftCatal'] = null;
            $GLOBALS['SysValue']['other']['leftMenu'] = null;
            $GLOBALS['SysValue']['other']['banersDisp'] = null;
            }
            else {
            $GLOBALS['container_style'] = 'd-lg-block d-md-block';
            $GLOBALS['container_col'] = 9;
            }
            php@

            <div class="container space-top-1 space-top-md-1 space-bottom-1" style="min-height: 43vh;">
                <div class="row">
                    <div class="col-3 d-none @php echo $GLOBALS['container_style']; php@">


                        <!-- �������� ������ -->
                        <div class="d-none space-1" id="faset-filter">

                            <div class="panel-body faset-filter-block-wrapper">

                                <div id="faset-filter-body">{��������}</div>

                                <div id="price-filter-body" class="border-bottom pb-4 mb-4 @hideCatalog@">
                                    <div class="h4">{����}</div>
                                    <form method="get" id="price-filter-form">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-6" id="price-filter-val-min">
                                                <input type="text" class="form-control form-control-sm" name="min" value="@price_min@">
                                            </div>
                                            <div class="col-md-6 col-xs-6" id="price-filter-val-max">
                                                <input type="text" class="form-control form-control-sm" name="max" value="@price_max@">
                                            </div>
                                        </div>
                                    </form>
                                    <br>

                                    <div id="slider-range"></div>

                                </div>
                                <a href="?" id="faset-filter-reset" class="btn btn-sm btn-block btn-soft-secondary transition-3d-hover">{�������� ������}</a>
                            </div>
                        </div>

                        <!--/ �������� ������ -->
                        <div class="mb-2 @hideSite@">
                            @leftCatal@ 
                        </div>
                        @leftMenu@

                        <div class="mt-5">@banersDisp@</div>
                    </div>
                    <div class="col-md-@php echo $GLOBALS['container_col']; php@ mb-5 mb-lg-0">
                        <!-- ����� ����������� @DispShop -->
                        @DispShop@
                        <!-- ����� @DispShop -->

                        @getPhotos@
                    </div>
                </div>
            </div>

            <!-- News Section -->
            <div class="container space-1 border-top  d-none @php echo $GLOBALS['container_style']; php@">
                <div class="row mb-3">
                    @miniNews@
                </div>
            </div>
            <!-- End News Section -->

            <!-- ������ -->
            <div class="@php __hide('banersDispHorizontal'); php@">
                @banersDispHorizontal@
            </div>
            <!-- / ������ -->
        </main>
        <!-- ========== END MAIN CONTENT ========== -->

        <!-- ========== FOOTER ========== -->
        <footer class="bg-dark">
            <div class="container">
                <div class="row justify-content-lg-between space-top-2 space-bottom-lg-1">
                    <div class="col-lg-3 mb-5">
                        <div class="d-flex align-items-start flex-column">
                            <a class="w-100 mb-3 mb-lg-auto" href="/" title="@name@">
                                <img class="brand" src="@logo@" alt="@name@">
                            </a>
                            <ul class="nav nav-sm nav-x-0 flex-column" itemscope itemtype="http://schema.org/Organization">
                                <li class="nav-item small text-muted mb-0">&copy; <span itemprop="name">@company@</span>, @year@</li>
                                <a href="tel:@telNum@"><li class="nav-item small text-muted mb-0"><span itemprop="telephone">@telNum@</span></li></a>
                                <a href="tel:@telNum2@"><li class="nav-item small text-muted mb-0"><span itemprop="telephone">@telNum2@</span></li></a>
                                <li class="nav-item small text-muted mb-0">@workingTime@</li>
                                <li class="nav-item small text-muted mb-0" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">@streetAddress@</span></li>
                                <li class="nav-item">
                                    <!-- ���������� ���� -->
                                    <ul class="list-inline">
                                        <li class="list-inline-item @php __hide('vk'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="���������" href="@vk@" target="_blank"><em class="fab fa-vk" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item @php __hide('telegram'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Telegram" href="@telegram@" target="_blank"> <em class="fab fa-telegram" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="�������������" href="@odnoklassniki@" target="_blank"> <em class="fab fa-odnoklassniki" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item @php __hide('youtube'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Youtube" href="@youtube@" target="_blank"><em class="fab fa-youtube" aria-hidden="true"></em></a></li>
                                        <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fab fa-whatsapp" aria-hidden="true"></em></a></li>
                                    </ul>
                                    <!-- / ���������� ���� -->
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 ml-lg-auto mb-5 mb-lg-0">

                        <!-- Nav Link -->
                        <ul class="nav nav-sm nav-x-0 flex-column">
                            <li class="nav-item @hideCatalog@"><a class="text-muted nav-link" href="/price/">{�����-����}</a></li>
                            <li class="nav-item  @php __hide('miniNews'); php@"><a class="text-muted nav-link" href="/news/">{�������}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/gbook/">{������}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/forma/">{����� �����}</a></li>
                        </ul>
                        <!-- End Nav Link -->
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 ml-lg-auto mb-5 mb-lg-0 d-none d-sm-block">

                        <!-- Nav Link -->
                        <ul class="nav nav-sm nav-x-0 flex-column">
                            @php if($_SESSION['UsersId']) echo '
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/">{���������}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/order.html">{��������� �����}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/message.html">{����� � �����������}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="?logout=true">{�����}</a></li>
                            ';
                            else echo '
                            <li class="nav-item"><a class="text-muted js-hs-unfold-invoker nav-link" href="javascript:;" data-toggle="modal" data-target="#signupModal">{�����}</a></li>
                            <li class="nav-item"><a class="text-muted nav-link" href="/users/">{������������������}</a></li>
                            ';
                            php@

                        </ul>
                        <!-- End Nav Link -->
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 mb-5 mb-lg-0 @php __hide('bottomMenu'); php@">

                        <!-- Nav Link -->
                        <ul class="nav nav-sm nav-x-0 flex-column">
                            <span class="text-muted">@bottomMenu@</span>
                        </ul>
                        <!-- End Nav Link -->
                    </div>
                </div>
            </div>
        </footer>
        <!-- ========== END FOOTER ========== -->

        <!-- ========== SECONDARY CONTENTS ========== -->
        <!-- ReturnCall Modal -->
        <div class="modal fade bs-example-modal-sm return-call" id="returnCallModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">@returnCallName@</h4>
                    </div>
                    <form method="post" name="ajax-form" action="@ShopDir@/returncall/" data-modal="returnCallModal">
                        <div class="modal-body">

                            <div class="form-group">

                                <input type="text" name="returncall_mod_name" class="form-control" placeholder="{���}" required="">
                            </div>
                            <div class="form-group">

                                <input type="text" name="returncall_mod_tel" class="form-control phone" placeholder="{�������}" required="">
                            </div>
                            <div class="form-group">

                                <input class="form-control" type="text" placeholder="{����� ������}" name="returncall_mod_time_start">
                            </div>
                            <div class="form-group">

                                <textarea class="form-control" name="returncall_mod_message" placeholder="{���������}"></textarea>
                            </div>
                            @returncall_captcha@
                            <div class="form-group">
                                <p class="small">
                                    <input type="checkbox" value="on" name="rule" class="req" checked="checked"> 
                                    {� ��������}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a>
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="returncall_mod_send" value="1">
                            <input type="hidden" name="ajax" value="1">
                            <button type="button" class="btn btn-soft-primary" data-dismiss="modal">{�������}</button>
                            <button type="submit" class="btn btn-primary">{�������� ������}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ReturnCall Modal -->

        <!-- Sign Up Modal -->
        <div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                            <svg width="10" height="10" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="modal-body p-sm-5">

                        <!-- Sign in -->
                        <div id="signinModalForm">
                            <div class="text-center mb-5">
                                <h2>{�����������}</h2>
                                <p id="usersError" class="text-danger">@usersError@</p>
                                <p>{� ��� ��� ��� ������� ������}?
                                    <a href="/users/register.html">{������������������}</a>
                                </p>
                            </div>
                            
                             <a class="js-animation-link btn btn-block btn-primary mb-2" href="#"
                               data-hs-show-animation-options='{
                               "targetSelector": "#signinWithEmailModalForm",
                               "groupName": "idForm"
                               }'>{����� �� }Email</a>
                            <a class="btn btn-block btn-soft-primary mb-2 @sms_login_enabled@" href="/users/sms.html">{����� �� ������ ��������}</a>

                            <!-- Yandex ID -->
                            @yandexid@
                            <!-- End Yandex ID -->

                            <!-- VK ID -->
                            @vkid@
                            <!-- End VK ID -->

                        </div>
                        <!-- End Sign in -->

                        <!-- Sign in with Modal -->
                        <div id="signinWithEmailModalForm" style="display: none; opacity: 0;">
                            <div class="text-center mb-5">
                                <h2>{�����������}</h2>
                                <p>{� ��� ��� ��� ������� ������}?
                                    <a href="/users/register.html">{������������������}</a>
                                </p>
                            </div>

                            <form class="js-validate" method="post" name="user_forma">

                                <!-- Form Group -->
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="signinModalFormSrEmail">Email</label>
                                    <input type="email" class="form-control" name="login" id="signinModalFormSrEmail" placeholder="email@address.ru" aria-label="email@address.ru" required data-msg="{����������, ������� �������������� ����� ����������� �����}">
                                </div>
                                <!-- End Form Group -->


                                <!-- Form Group -->
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="signinModalFormSrPassword">
                                        <span class="d-flex justify-content-between align-items-center">
                                            {������}
                                            <a class="js-animation-link link-underline text-muted" href="javascript:;"
                                               data-hs-show-animation-options='{
                                               "targetSelector": "#forgotPasswordModalForm",
                                               "groupName": "idForm"
                                               }'>{������ ������}?</a>
                                        </span>
                                    </label>
                                    <input type="password" class="form-control" name="password" id="signinModalFormSrPassword" placeholder="{�� ����� 4 ��������}" aria-label="{�� ����� 4 ��������}" required data-msg="{����������, ������� �������������� ����� ����������� �����}">
                                </div>
                                <!-- End Form Group -->

                                <div class="js-form-message form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" value="1" name="safe_users" @UserChecked@ id="safe_users">
                                        <label class="custom-control-label text-lh-lg" for="safe_users">{���������}</label>
                                    </div>

                                </div>

                                <input type="hidden" value="1" name="user_enter">
                                <button type="submit" class="btn btn-block btn-primary">{�����}</button>
                            </form>

                        </div>
                        <!-- End Sign in with Modal -->


                        <form method="post" name="userpas_forma" class="js-validate" action="/users/sendpassword.html">
                            <!-- Forgot Password -->
                            <div id="forgotPasswordModalForm" style="display: none; opacity: 0;">
                                <div class="text-center mb-5">
                                    <h2>{������ ������}?</h2>
                                    <p>{������� ����� ����������� �����, ������� �� ������������ ��� �����������, � �� ������ ��� ���������� �� ������ ������}.</p>
                                </div>

                                <!-- Form Group -->

                                <div class="js-form-message form-group">
                                    <label class="input-label" for="resetPasswordSrEmail" tabindex="0">
                                        <span class="d-flex justify-content-between align-items-center">
                                            Email
                                            <a class="js-animation-link d-flex align-items-center link-underline text-muted" href="javascript:;"
                                               data-hs-show-animation-options='{
                                               "targetSelector": "#signinModalForm",
                                               "groupName": "idForm"
                                               }'>
                                                <i class="fas fa-angle-left mr-2"></i>{��������� �����}
                                            </a>
                                        </span>
                                    </label>
                                    <input type="email" class="form-control" name="login" id="resetPasswordSrEmail" tabindex="1" placeholder="email@address.ru" aria-label="{����������, ������� �������������� ����� ����������� �����}" required data-msg="{����������, ������� �������������� ����� ����������� �����}">
                                </div>
                                <!-- End Form Group -->

                                <input type="hidden" value="1" name="passw_send">
                                <button type="submit" class="btn btn-block btn-primary">{������� ������}</button>
                            </div>
                            <!-- End Forgot Password -->
                        </form>
                    </div>
                    <!-- End Body -->


                </div>
            </div>
        </div>
        <!-- End Sign Up Modal -->
        <!-- ========== END SECONDARY CONTENTS ========== -->

        <!-- Go to Top -->
        <a class="js-go-to go-to position-fixed" href="javascript:;" style="visibility: hidden;"
           data-hs-go-to-options='{
           "offsetTop": 700,
           "position": {
           "init": {
           "left": 15
           },
           "show": {
           "bottom": 15
           },
           "hide": {
           "bottom": -15
           }
           }
           }'>
            <i class="fas fa-angle-up"></i>
        </a>
        <!-- End Go to Top -->

        <!-- Notification -->
        <div id="notification" class="success-notification" style="display:none">
            <div class="alert alert-primary alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
                <span class="notification-alert"> </span>
            </div>
        </div>
        <!--/ Notification -->

        <!-- Cookie Alert -->
        <div class="container position-fixed bottom-0 right-0 left-0 z-index-4 cookie-message d-none">
            <div class="alert bg-white w-lg-80 border shadow-sm mx-auto" role="alert">
                <h5 class="text-dark">{������������������}</h5>
                <p class="small"></p>

                <div class="row align-items-sm-center">
                    <div class="col-sm-12 text-sm-right">
                        <a type="button" class="btn btn-sm btn-primary transition-3d-hover" data-dismiss="alert" aria-label="Close">{�������}</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Cookie Alert -->

        <!-- ��������� ���� �������-->
        <div id="thanks-box" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- ��������� ���������� ���� -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <div class="h4 modal-title">{���������}</div>
                    </div>
                    <!-- �������� ���������� ���������� ���� -->
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>
        <!--/ ��������� ���� �������-->

        <!-- JS Global Compulsory  -->
        <script src="@pathTemplate@/assets/vendor/jquery-migrate/dist/jquery-migrate.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        @editor@

        <!-- JS Implementing Plugins -->
        <script src="@pathTemplate@/assets/vendor/hs-header/dist/hs-header.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-go-to/dist/hs-go-to.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-unfold/dist/hs-unfold.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-mega-menu/dist/hs-mega-menu.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-show-animation/dist/hs-show-animation.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/slick-carousel/slick/slick.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/hs-quantity-counter/dist/hs-quantity-counter.min.js"></script>
        <script src="@pathTemplate@/assets/vendor/select2/dist/js/select2.full.min.js"></script>

        <!-- JS Front -->
        <script src="@pathTemplate@/assets/js/theme.min.js"></script>

        <!-- Core JS -->
        <script src="@pathTemplateMin@/js/phpshop.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
        <script src="@pathTemplate@/js/jquery.cookie.min.js"></script>
        <script src="@pathTemplate@/js/jquery-ui.min.js"></script>
        <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
        <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>
        <script src="@pathTemplate@/js/jquery.waypoints.min.js"></script>
        <script src="@pathTemplate@/js/inview.min.js"></script>


        <!-- JS Plugins Init. -->
        <script src="@pathTemplateMin@/js/flow.js"></script>

        @visualcart_lib@
        <div class="d-none d-sm-block">