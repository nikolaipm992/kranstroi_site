<div class="big-menu">
    <div class="big-menu-wrap"><i class="fal fa-times no-display menu-close-btn"></i>
        <span class="menu-back-btn"><i class="fa fa-angle-left" aria-hidden="true"></i> &nbsp; &nbsp; {Главное меню}</span>
    </div>
</div>
<header class="header-3">
    <div class="hidden-menu hidden-catalog visible-xs">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fal fa-times" aria-hidden="true"></i>
            <span class="sr-only">Close</span>
        </button>
        <div class="clearfix"></div>
        <a class="back"><i class="fa fa-angle-left" aria-hidden="true"></i>{Назад}</a>
        <div class="solid-menus @hideSite@">
            <ul class="no-border-radius block parent-block">
                @leftCatal@
            </ul>
        </div>
    </div>
    <div class="hidden-menu hidden-top visible-xs">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fal fa-times" aria-hidden="true"></i>
            <span class="sr-only">Close</span>
        </button>
        <div class="clearfix"></div>
        <a class="back"><i class="fa fa-angle-left" aria-hidden="true"></i>{Назад}</a>
        <div class="solid-menus">
            <ul class="no-border-radius block parent-block">
                @topMenu@

            </ul>
            <div class="visible-xs msg">
                @sticker_social@
            </div>

        </div>
    </div>
    <!-- Стикер-полоска -->
    <div class="@php __hide('sticker_top'); php@">
        <div class="top-banner @php __hide('sticker_close','cookie'); php@">
            <div class="sticker-text">@sticker_top@</div>
            <span class="close sticker-close">x</span>
        </div>
    </div>
    <!-- /Стикер-полоска -->
    <div class="top-menu hidden">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6 col-lg-7 col-sm-5 hidden-xs">
                    <ul class="top-menu-list pull-left">
                        <li class="shop-name">@name@</li>

                    </ul>
                </div>

                <div class="col-md-6 col-lg-5 col-sm-7 col-xs-12">
                    <ul class="nav nav-pills pull-right">
                        <li class="visible-xs">|</li>
                        <li class="visible-xs @hideSite@" role="presentation">@wishlist@</li>
                        <li class="visible-xs @hideSite@" role="presentation">
                            <a href="/compare/">
                                <span class="icons-compare"></span>
                                <span class="text">{В сравнении}</span> <span id="numcompare">@numcompare@</span> {шт.}
                            </a>
                        </li>
                        <li class="visible-xs">|</li>
                        @usersDisp@
                        <li role="presentation" class="visible-xs @hideCatalog@">
                            <a href="/order/">
                                <span class="icons-cart"></span>&nbsp;
                                <span class="sum">@sum@</span>&nbsp;
                                <span class="rubznak">@productValutaName@</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="vertical-align header-middle-wrap">
            <div class="header-phone">
                <span class="shop-name"> @name@</span>
                <h4><a href="tel:@telNumMobile@">@telNumMobile@</a></h4>
                @returncall@
            </div>
            <div class="logo">
                <a href="/">
                    <img src="@logo@" alt="">
                </a>
                <!-- Каталоги в главном меню-->

                <ul class="center-menu">
                    @topcatMenu@
                </ul>

                <!--/ Каталоги в главном меню-->
            </div>
            <div class="visible-xs">
                @sticker_social@
            </div>

            <ul class="menu-list hidden-xs">
                @usersDisp@
                <li role="presentation" class="@hideSite@">@wishlist@</li>
                <li role="presentation" class="@hideSite@">
                    <a href="/compare/">

                        <span class="text">{Сравнить}</span> <span id="numcompare">@numcompare@</span> {шт.}
                        <span class="icons-compare"></span>
                    </a>
                </li>             
            </ul>

        </div>
    </div>
    <div class="menu-wrap @hideSite@">
        <div class="container-fluid menu-cont">
            <ul class="dropdown-menu no-border-radius main-menu-block">
                @leftCatal@ 
            </ul>
            <span class="menu-close"><i class="fal fa-times no-display" style="font-size:30px"></i></span>
        </div>
    </div></header>
<!--/ Header -->

<!-- Fixed navbar -->
<nav class="navbar top-navbar  menu-3" id="navigation">
    <div class="container-fluid">
        <div class="row">
            <div class="navbar-header"></div>
            <div id="navbar" class="navbar-collapse collapse">
                <label class="btn-menu visible-xs btn-menu-left">
                    <span class="f-block">
                        <span class="f-block-wrapper">
                            <span class="first"></span>
                            <span class="second"></span>
                            <span class="third"></span>
                        </span>
                        {Каталог}
                    </span>
                </label>
                <ul class="nav navbar-nav main-menu">
                    <!-- dropdown catalog menu -->
                    <li>
                        <div class="solid-menus @hideSite@">
                            <nav class="navbar no-border-radius no-margin">
                                <div id="navbar-inner-container">

                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggle navbar-toggle-left"
                                                data-toggle="collapse" data-target="#solidMenu">
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    </div>

                                    <div class="collapse navbar-collapse" id="solidMenu">
                                        <ul class="nav navbar-nav">
                                            <li class="dropdown">
                                                <a class="dropdown-toggle open-menu" data-toggle="dropdown" href="javascript:void(0);" data-title="{Каталог}"><span><i class="icons-line"></i> {Каталог}</span> </a>
                                            </li>
                                            <li class="visible-xs"><a href="/users/wishlist.html">{Отложенные товары}</a></li>
                                            <li class="visible-xs @hideCatalog@"><a href="/price/">{Прайс-лист}</a></li>
                                        </ul> 
                                    </div>

                                </div>
                            </nav>
                        </div>
                    </li>
                    <li class="visible-xs"><a href="/news/">{Новости}</a></li>

                </ul>

                <ul class="catalog-menu-list @hideSite@">

                    <li class="menu-item @php __hide('specMainIcon'); php@"><a href="/newtip/">{Новинки}</a></li>
                    <li class="menu-item brands-link @php __hide('brandsList'); php@"><a href="/brand/">{Бренды}</a></li>
                    <li class="menu-item menu-item-flag @php __hide('specMain'); php@ @hideCatalog@"><a href="/spec/">{Распродажа}</a></li>

                </ul>
                <div class="catalog-menu-wrap visible-xs">
                    <ul class="catalog-menu-list">
                        @topcatMenu@
                    </ul>
                    <form action="/search/" role="search" method="get">
                        <div class="input-group search-block">
                            <input name="words" maxlength="50" class="form-control search-input" placeholder="{Искать}.." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" data-content="">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><span class="icons-search"></span></button>
                            </span>
                        </div>
                    </form>

                    <ul class="nav nav-pills pull-right @hideSite@">
                        <li role="presentation">@wishlist@</li>
                        <li role="presentation">
                            <a href="/compare/">
                                <span class="icons-compare"></span>
                                <span id="numcompare2">@numcompare@</span> {шт.}
                            </a>
                        </li>
                    </ul>
                </div>
                <form action="/search/" role="search" method="get" class="hidden-xs">
                    <div class="input-group search-block">
                        <input name="words" maxlength="50" class="form-control search-input" placeholder="{Искать}.." required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" data-content="">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><span class="icons-search"></span></button>
                        </span>
                    </div>
                </form>	
                <a href="/order/" class="btn-menu visible-xs"><span class="icons-cart"></span></a>
                <label class="btn-menu btn-menu-right visible-xs" for="hmt">
                    <span class="f-block-wrapper">
                        <span class="first"></span>
                        <span class="second"></span>
                        <span class="third"></span>
                    </span>
                </label>
                <ul class="nav nav-pills pull-right @hideSite@">
                    <li role="presentation">@wishlist@</li>
                    <li role="presentation">
                        <a href="/compare/">
                            <span class="icons-compare"></span>
                            <span id="numcompare2">@numcompare@</span> {шт.}
                        </a>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right visible-lg visible-md header-3-cart @hideCatalog@">

                    <li>
                        <a class="header-cart" id="cartlink" data-trigger="click" data-container="body"
                           data-toggle="popover" data-placement="bottom" data-html="true" data-url="/order/"
                           data-content='@visualcart@'>
                            <span class="icons-cart"></span>

                            <span class="visible-lg-inline"><span id="num">@num@</span> {тов.}  {на сумму} </span>
                            <span id="sum" class="sum">@sum@</span>
                            <span class="rubznak">@productValutaName@</span>
                        </a>
                    </li>
                    <li>

                    </li>
                </ul>

            </div><!--/.nav-collapse -->
        </div>
    </div>
</nav>
<!--/ Fixed navbar -->

<!-- VisualCart Mod -->
<div id="visualcart_tmp" class="hide">@visualcart@</div>

<!-- Notification -->
<div id="notification" class="success-notification" style="display:none">
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"<i class="fal fa-times" aria-hidden="true"></i><span class="sr-only">Close</span>
        </button>
        <span class="notification-alert"> </span>
    </div>
</div>
<!--/ Notification -->