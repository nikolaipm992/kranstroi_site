<!-- header section start -->
<header class="header header-2 pt-20 pb-10 header-sticky header-static">
    <div class="container-fluid">
        <div class="header-nav header-nav-2 position-relative ">
            <div class="row align-items-center">

                <div class="col-xl-5 col-lg-5 col-sm-6 hidden-md position-static">
                    <div class="header-nav">
                        <nav>
                            <ul>

                                @php

                                if(PHPShopParser::get('banersDispMenu')!="")
                                $GLOBALS['catalogmenu_col'] = 8;
                                else $GLOBALS['catalogmenu_col'] = 12;

                                php@

                                <li class="position-static"><a href="javascript:void(0)"><span>{�������} <i class="fal fa-angle-down"></i></span></a>
                                    <div class="mega-menu">
                                        <div class="row">
                                            <div class="col-xl-@php echo $GLOBALS['catalogmenu_col']; php@">
                                                <div class="row ">
                                                    @topCatal@
                                                </div>
                                            </div>
                                            <div class="col-xl-4 @php __hide('banersDispMenu'); php@">

                                                <!-- Banner Image -->
                                                @banersDispMenu@
                                                <!-- End Banner Image -->

                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="@php __hide('topBrands'); php@">
                                    <a href="javascript:void(0)">
                                        <span>{������} <i class="fal fa-angle-down"></i></span>
                                    </a>
                                    <ul class="submenu brands">
                                        @topBrands@
                                    </ul>
                                </li>

                                <!-- Catalog  Menu-->
                                @topcatMenu@
                                <!-- End Catalog  Menu-->

                                <li class="@php __hide('topMenu'); php@"><a href="javascript:void(0)"><span>{���������} <i
                                                class="fal fa-angle-down"></i></span> </a>
                                    <ul class="submenu">
                                        @topMenu@
                                    </ul>
                                </li>


                            </ul>

                        </nav>
                    </div>
                </div>


                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-3 ">
                    <div class="logo">
                        <a href="/"><img src="@logo@" alt=""></a>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 d-none d-xl-block">
                    @returncall@
                    <a href="tel:@telNumMobile@" class="text-white">@telNum@</a>
                </div>

                <div class="col-xl-3 col-lg-5 col-md-6 col-sm-6 col-9">
                    <div class="header-right">
                        <ul class="text-right">
                            <li><a href="javascript:void(0)"><i class="fal fa-search"></i></a>

                                <!-- search popup -->
                                <div id="search-popup">
                                    <div class="close-search-popup">
                                        <i class="fal fa-times"></i>
                                    </div>
                                    <div class="search-popup-inner mt-50">
                                        <div class="search-title text-center">
                                            <h2>{�����}</h2>
                                        </div>

                                        <div class="search-content pt-25">


                                            <div class="search-form mt-35">
                                                <form action="/search/" method="get">
                                                    <input name="words" type="search" placeholder="{� ���}..." id="searchSlideDownControl" required="">
                                                    <button type="submit"><i class="fal fa-search"></i></button>
                                                </form>
                                            </div>

                                            <div class="search-result-list search-slide-down-suggestions">
                                                <ul class="text-left" id="searchSlideDownContent">

                                                </ul>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </li>
                            <li><a href="/users/wishlist.html" data-toggle="tooltip" data-placement="bottom" title="{���������}"><i class="fal fa-heart"><span class="wishlistcount">@wishlistCount@</span></i></a></li>
                            <li><a class="d-none d-xl-block" href="/compare/" data-toggle="tooltip" data-placement="bottom" title="{��������}"><i class="fal fa-balance-scale-right"><span class="numcompare">@numcompare@</span></i></a></li>
                            <li><a href="/order/"><i class="fal fa-shopping-bag"><span class="cartnum" id="num">@num@</span></i></a>
                                <div class="minicart @php __hide('num'); php@">
                                    <div class="minicart-body">
                                        <div class="minicart-content">
                                            @visualcart@
                                        </div>
                                    </div>
                                    <div class="minicart-checkout">

                                        <div class="minicart-checkout-links" id="order">
                                            <a href="/order/" class="generic-btn black-hover-btn text-uppercase w-100 mb-20">{�������� �����}</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @usersDisp@
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mobile-menu visible-sm mean-container">
            <div id="mobile-menu">
                <ul>

                    <li><a  class="pl-3" href="javascript:void(0)">{�������}</a>

                        <ul class="pl-4">
                            <li>
                                @topCatal@
                            </li>
                        </ul>
                    </li>
                    <li><a  class="pl-3" href="javascript:void(0)">{���������}</a>

                        <ul class="pl-4">

                            @topMenu@

                        </ul>
                    </li>


                </ul>
            </div>
        </div>
        <!-- /. mobile nav -->
    </div>
</header>
<!-- header section end -->

@php
$GLOBALS['mazia_slider']="container";
php@