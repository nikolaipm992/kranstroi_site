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
        <link rel="preload" href="//fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" as="style">
        <link rel="preload" href="@pathTemplate@css/owl.carousel.min.css" as="style">
        <link rel="preload" href="@pathTemplate@css/fontawesome-all.min.css" as="style">
        <link rel="preload" href="@pathTemplate@css/meanmenu.css" as="style">
        <link rel="preload" href="@pathTemplate@css/slick.css" as="style">
        <link rel="preload" href="@pathTemplate@css/default.css" as="style">
        <link rel="preload" href="@pathTemplate@css/suggestions.min.css" as="style">
        <link rel="preload" href="@pathTemplateMin@/style.css" as="style">
        <link rel="preload" href="@pathTemplate@css/responsive.css" as="style">

        <!-- Bootstrap -->
        <link rel="stylesheet" href="@pathTemplate@css/bootstrap.min.css">

        <!-- Theme -->
        <link id="bootstrap_theme" data-name="@php echo $_SESSION['skin']; php@" href="@pathTemplate@css/@mazia_theme@.css" rel="stylesheet">

        <!-- Core CSS -->
        <link href="@pathTemplateMin@/style.css" type="text/css" rel="stylesheet">
    </head>
    <body id="body" data-dir="@ShopDir@" data-path="@php echo $GLOBALS['PHPShopNav']->objNav['path']; php@" data-id="@php echo $GLOBALS['PHPShopNav']->objNav['id']; php@" data-subpath="@php echo $GLOBALS['PHPShopNav']->objNav['name']; php@" data-token="@dadataToken@">

        <!-- Font -->
        <link href="//fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- CSS Implementing Plugins -->
        <link rel="stylesheet" href="@pathTemplate@css/owl.carousel.min.css">
        <link rel="stylesheet" href="@pathTemplate@css/fontawesome-all.min.css">
        <link rel="stylesheet" href="@pathTemplate@css/meanmenu.css">
        <link rel="stylesheet" href="@pathTemplate@css/slick.css">
        <link rel="stylesheet" href="@pathTemplate@css/default.css">
        <link rel="stylesheet" href="@pathTemplate@css/suggestions.min.css">
        <link rel="stylesheet" href="@pathTemplate@css/responsive.css">

        <!-- ������-������� -->
        <div class="alert alert-primary text-center alert-dismissible font-size-1 fade show @php __hide('sticker_top');__hide('sticker_close','cookie'); php@" role="alert">@sticker_top@<button type="button" class="close sticker-close" data-dismiss="alert" aria-label="Close"><svg aria-hidden="true" class="mb-0" width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/></svg></button>
        </div>
        <!-- ����� ������-������� -->

        <!-- Header -->
        @header@
        <!--/ Header -->

        <!-- slider section start -->
        <section class="slider ">
            <div class="@php echo $GLOBALS['mazia_slider']; php@ pl-0 pr-0">
                <div class="slider-active center-dots number-dots white-dot">
                    @imageSlider@
                </div>
            </div>

        </section>
        <!-- slider section end -->

        <!-- ========== MAIN CONTENT ========== -->
        <main id="content" role="main">

            <section class="banner-2 mt-30 two-item">
                <div class="container container-1430">
                    <div class="row">
                        @leftCatalTable@
                    </div>
                </div>
            </section>

            <section class="service mt-30 @php __hide('sticker_slogan1'); php@">
                <div class="container container-1430">
                    <div class="row justify-content-center">
                        <div class="col-md-4 service-item">
                            @sticker_slogan1@
                        </div>

                        <div class="col-md-4 service-item">
                            @sticker_slogan2@
                        </div>

                        <div class="col-md-4  service-item">
                            @sticker_slogan3@
                        </div>
                    </div>
                </div>
                <div class="container gray-border-bottom pb-35"></div>
            </section>

            <!-- main product sections tart -->
            <section class="main-product mt-50 mb-50">
                <div class="container container-1430">
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item">

                            <h2 class="mb-20">{�������}</h2>
                        </li>
                    </ul>
                    <div class="tab-content mt-25">
                        <div class="tab-pane fade show active" id="main-tab-1">
                            <div class="main-product-carousel owl-carousel red-nav">
                                @specMainIcon@
                            </div>
                            <div class="text-center load-btn">
                                <a href="/newtip/" class="load-more">{�������� ���}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- main product sections end -->

            <!-- Promo Section -->
            <div class="container container-1430 space-2 space-lg-2 @php __hide('productDay'); php@">
                <div class="container gray-border-top pb-30 pt-35"></div>
                <div class="row">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        @productDay@
                    </div>

                    <div class="col-lg-6">
                        <!-- ������ -->
                        @banersDisp@
                        <!-- ����� ������� -->
                    </div>
                </div>
            </div>
            <!-- End Promo Section -->


            <div class="container gray-border-bottom pb-45"></div>
            <section class="shipping-price-section shipping-2">
                <div class="container mt-50">
                    <div class="shipping-desc ">
                        <h2 class="mb-20">@mainContentTitle@</h2>
                        <p class="mb-0">@mainContent@</p>
                    </div>
                </div>

                <div class="container container-1430">
                    <div class="row pt-55" style="justify-content: center;">
                        <div class="col-sm-4 col-lg-3 ">
                            <div class="contact text-center mb-30">
                                <i class="fas fa-envelope"></i>
                                <h3 class="active">{�� � ��������}</h3>
                                <p>
                                    <!-- ���������� ���� -->
                                <ul class="list-inline">
                                    <li class="list-inline-item @php __hide('vk'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="���������" href="@vk@" target="_blank"><em class="fab fa-vk" aria-hidden="true"></em></a></li>
                                    <li class="list-inline-item @php __hide('telegram'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Telegram" href="@telegram@" target="_blank"> <em class="fab fa-telegram" aria-hidden="true"></em></a></li>
                                    <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="�������������" href="@odnoklassniki@" target="_blank"> <em class="fab fa-odnoklassniki" aria-hidden="true"></em></a></li>
                                    <li class="list-inline-item @php __hide('youtube'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="Youtube" href="@youtube@" target="_blank"><em class="fab fa-youtube" aria-hidden="true"></em></a></li>
                                    <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="btn btn-xs btn-icon btn-soft-primary" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fab fa-whatsapp" aria-hidden="true"></em></a></li>
                                </ul>
                                </p>
                                <!-- / ���������� ���� -->
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg-3">
                            <div class="contact text-center mb-30">
                                <i class="fas fa-map-marker-alt"></i>
                                <h3>{��� �����}</h3>
                                <p>@streetAddress@
                                    @workingTime@</p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg-3">
                            <div class="contact text-center mb-30">
                                <i class="fas fa-phone"></i>
                                <h3>{��� �������}</h3>
                                <p><a href="tel:@telNum@">@telNum@</a><br>
                                    <a href="tel:@telNum2@">@telNum2@</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="@php __hide('miniNews'); php@">
                <div class="container container-1430 pt-85 pb-85">
                    <div class="row" style="justify-content: center;">
                        @miniNews@
                    </div>
                    <div class="text-center mt-10 load-btn">
                        <a href="/news/" class="load-more">{�������� ���}</a>
                    </div>
                </div>
            </section>

            <!-- ������ -->
            <div class="container container-1430 pb-30 d-none d-sm-block">
                @banersDispHorizontal@
            </div>
            <!-- / ������ -->

        </main>
        <!-- ========== END MAIN CONTENT ========== -->

        <!-- ========== FOOTER ========== -->

        <!-- footer section start -->
        <section class="footer pt-30" id="footer">
            <div class="footer footer-bottom ">
                <div class="container container-1180  pt-30" >
                    <div class="footer-bottom-wrapper">
                        <div class="footer-bottom-primary pb-30">
                            <div class="row">
                                <div class="col-xl-5 col-lg-5 col-md-9 ">
                                    <div class="footer-item has-desc">
                                        <div class="footer-logo mb-25 d-none d-sm-block">
                                            <a class="w-100 mb-3 mb-lg-auto @php __hide('logo'); php@" href="/" title="@name@">
                                                <img class="brand" src="@logo@" alt="">
                                            </a>
                                        </div>
                                        <div class="footer-desc">
                                            <ul class="nav nav-sm nav-x-0 flex-column" itemscope itemtype="http://schema.org/Organization">
                                                <li class="nav-item small text-muted mb-0">&copy; <span itemprop="name">@company@</span>, @year@</li>
                                                <a href="tel:@telNum@"><li class="nav-item small text-muted mb-0"><span itemprop="telephone">@telNum@</span></li></a>
                                                <a href="tel:@telNum2@"><li class="nav-item small text-muted mb-0"><span itemprop="telephone">@telNum2@</span></li></a>
                                                <li class="nav-item small text-muted mb-0">@workingTime@</li>
                                                <li class="nav-item small text-muted mb-0" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">@streetAddress@</span></li>
                                                <li class="nav-item small text-muted mb-0">                            
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
                                                <li class="nav-item">@sticker_footer_icon@</li>
                                                <li>@button@</li>
                                            </ul>
                                        </div>
                                        <div class="footer-img mt-65 @php __hide('sticker_pay'); php@">
                                            @sticker_pay@
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-7 col-lg-7 col-md-12">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">
                                            <div class="footer-menu">
                                                <h5>{������ �������}</h5>

                                                <!-- Nav Link -->
                                                <ul class="nav nav-sm nav-x-0 flex-column">
                                                    @php if($_SESSION['UsersId']) echo '
                                                    <li class="nav-item"><a class="nav-link" href="/users/">{���������}</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="/users/order.html">{��������� �����}</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="/users/message.html">{����� � �����������}</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="?logout=true">{�����}</a></li>
                                                    ';
                                                    else echo '
                                                    <li class="nav-item"><a class="js-hs-unfold-invoker nav-link" href="javascript:;" data-toggle="modal" data-target="#userModal">{��������� �����}</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="/users/">{������������������}</a></li>
                                                    ';
                                                    php@

                                                </ul>
                                                <!-- End Nav Link -->
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">
                                            <div class="footer-menu">
                                                <h5>{����������}</h5>

                                                <!-- Nav Link -->
                                                <ul class="nav nav-sm nav-x-0 flex-column">
                                                    @bottomMenu@
                                                </ul>
                                                <!-- End Nav Link -->
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 hidden-sm">
                                            <div class="footer-menu">
                                                <h5>{���������}</h5>

                                                <!-- Nav Link -->
                                                <ul class="nav nav-sm nav-x-0 flex-column">
                                                    <li class="nav-item"><a class="nav-link" href="/price/">{�����-����}</a></li>
                                                    <li class="nav-item  @php __hide('miniNews'); php@"><a class="nav-link" href="/news/">{�������}</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="/gbook/">{������}</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="/forma/">{����� �����}</a></li>
                                                </ul>
                                                <!-- End Nav Link -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <!-- footer section end -->
        <!-- ========== END FOOTER ========== -->

        <!-- ========== SECONDARY CONTENTS ========== -->

        <!-- ReturnCall Modal -->
        <div class="modal fade bs-example-modal-sm return-call" id="returnCallModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@returnCallName@</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
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

                                <textarea class="form-control" name="returncall_mod_message" placeholder="���������"></textarea>
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
                            <button type="button" class="generic-btn black-hover-btn" data-dismiss="modal">{�������}</button>
                            <button type="submit" class="generic-btn red-hover-btn">{�������� ������}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ReturnCall Modal -->



        <!-- Notification -->
        <div id="notification" class="success-notification" style="display:none">
            <div class="alert alert-secondary alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
                <span class="notification-alert"> </span>
            </div>
        </div>
        <!--/ Notification -->


        <!-- �������� �� ������������� cookie  -->
        <div class="cookie-message d-none">
            <h5 class="text-dark">{������������������}</h5>
            <p class=""></p><a href="#" class="generic-btn black-hover-btn text-uppercase">Ok</a>
        </div>

        <!-- ��������� ���� �����������-->
        <div class="modal" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="h4 modal-title">{�����������}</div>
                        <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">x</span><span class="sr-only">Close</span>
                        </button>
                    </div>
                    <form method="post" name="user_forma">
                        <div class="modal-body">


                            <span id="usersError" class="hide text-danger">@usersError@</span>

                            <div class="form-group">

                                <input type="email" name="login" class="form-control" placeholder="Email" required="" value="@UserLogin@">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                                <br>

                                <input type="password" name="password" class="form-control" placeholder="{������}" required="" value="@UserPassword@">
                                <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
                            </div>
                            <div class="d-flex">
                                <div class="checkbox mr-auto p-2">
                                    <label>
                                        <input type="checkbox" value="1" name="safe_users" @UserChecked@> {���������}
                                    </label>
                                </div>
                                <div class="p-2">
                                    <a href="/users/sms.html" class="pass @sms_login_enabled@">SMS</a>
                                    <a href="/users/sendpassword.html" class="pass">{������ ������}</a>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer flex-row">

                            <input type="hidden" value="1" name="user_enter">
                            <button type="submit" class="generic-btn black-hover-btn text-uppercase mb-20">{�����}</button>
                            <a href="/users/register.html" class="generic-btn black-hover-btn text-uppercase mb-20">{������������������}</a>

                        </div>

                        <!-- Yandex ID -->
                        @yandexid@
                        <!-- End Yandex ID -->

                        <!-- VK ID -->
                        <div style="padding:5px">@vkid@</div>
                        <!-- End VK ID -->
                    </form>
                </div>
            </div>
        </div>
        <!--/ ��������� ���� �����������-->

        <!-- ������� ��������-->
        <div class="modal product-number-fix fade bs-example-modal-sm" id="modalProductView" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg fastViewContent"></div>
        </div>
        <!--/ ������� ��������-->

        <!-- ��������� ���� �������-->
        <div id="thanks-box" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- ��������� ���������� ���� -->
                    <div class="modal-header">
                        <div class="h4 modal-title">{���������}</div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <!-- �������� ���������� ���������� ���� -->
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>
        <!--/ ��������� ���� �������-->

        <!-- JS here -->
        <script src="@pathTemplate@/js/jquery-1.12.4.min.js"></script>
        <script src="@pathTemplate@/js/popper.min.js"></script>
        <script src="@pathTemplate@/js/bootstrap.min.js"></script>
        <script src="@pathTemplate@/js/owl.carousel.min.js"></script>
        <script src="@pathTemplate@/js/slick.min.js"></script>
        <script src="@pathTemplate@/js/jquery.meanmenu.min.js"></script>
        <script src="@pathTemplate@/js/fontawesome.min.js"></script>
        <script src="@pathTemplate@/js/jquery.scrollUp.min.js"></script>
        <script src="@pathTemplate@/js/main.js"></script>

        <!-- Core JS -->
        <script src="@pathTemplate@/js/phpshop.js"></script>
        <script src="java/jqfunc.js"></script>
        <script src="phpshop/locale/@php echo $_SESSION['lang']; php@/template.js"></script>
        <script src="@pathTemplate@/js/jquery.cookie.js"></script>
        <script src="@pathTemplate@/js/jquery.suggestions.min.js"></script>
        <script src="@pathTemplate@/js/jquery.maskedinput.min.js"></script>

        @editor@

        @visualcart_lib@

        <div class="d-none d-sm-block">
