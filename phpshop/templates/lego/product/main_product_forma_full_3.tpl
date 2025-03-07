<style>
    .sidebar-left-inner,
    .brands {
        display: none
    }

    div.main {
        width: 100%;

        float: none;
        margin: 0 auto;
    }

    .spec {
        border-top: 1px solid #eaedef;
        border-bottom: 1px solid #eaedef
    }

    @media (max-width: 767px) {
        .row {
            margin: 0
        }
    }
</style>
<div itemscope itemtype="http://schema.org/Product" class="main-product-block product-3">
    <div class="product-info-block visible-xs">
        <h1 itemprop="name" class="page-header">@productName@</h1>
        <meta itemprop="image" content="@productImg@">
        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
            <meta itemprop="ratingValue" content="@productRatingValue@">
            <meta itemprop="ratingCount" content="@productRatingCount@">
        </div>
        <span class="sale-icon-content rel-icon">
            @specIcon@
            @newtipIcon@
            @giftIcon@
            @hitIcon@
            @promotionsIcon@
        </span>
    </div>
    <div class="row">

        <div class="col-md-7 col-lg-6">
            <div id="fotoload" class="main-slider">
                <div class="row-fluid text-center">
                    <div class="span6 offset3">
                        <div class="row justify-content-center">
                            <div class="">
                                <div class="prodRatioHolder">
                                    <div id="productSlider" class="slider" data-elem="touchnswipe" data-options="appendControls:false; appendControlHolder:false">
                                        <div class="slideHolder" data-elem="slides" data-options="slideOptions:{ scaleMode:smart };preloaderUrl:@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@/images/zoomloader.gif;">
                                            @productSliderSlides@
                                        </div>
                                        <div data-elem="thumbs" class="thumbs" data-options="initShow:true; onCss:{top:0%; position:absolute; display:block; autoAlpha:1}; offCss:{top:100%; position:absolute; display:block; autoAlpha:1.0; }; visibility:fullscreen; preloaderUrl:@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@/images/zoomloader.gif;"> </div>
                                        <div class="fullscreenToggle" data-elem="fullscreenToggle" data-options="onCss:{className:'fullscreenToggle on'}; offCss:{className:'fullscreenToggle off'}"> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bigThumbs">
                                @productSliderThumbs@
                            </div>
                            <div class="col-12 buttonThumbs">
                                @productSliderControls@
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid thumbPad">
                    <div data-elem="thumbHolder" class="span8 offset2 thumbHolder">
                        <div data-elem="thumbScroller" class="thumbScroller" data-options="thumbWidth:60; thumbHeight:60;  defaultBorderColor:#EEE; borderColor:#EB6F4B; borderRadius:0; space:10; padding:5; borderThickness:2; defaultAlpha:0.8; alpha:1; preloaderUrl:@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@/images/zoomloader.gif;"> </div>
                    </div>
                </div>
            </div>
            <div class="panel-group product-panel " id="product-info">
                <div class="panel " id="descTab" >
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#product-info"
                               href="#collapseOne" aria-expanded="true">
                                {Описание товара}
                            </a>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body" id="desc">@productDes@</div>
                    </div>
                </div>
                <div class="panel" id="settingsTab">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#product-info"
                               href="#collapseTwo" aria-expanded="false">
                                {Характеристики}
                            </a>
                        </div>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body" id="vendorenabled">@vendorDisp@</div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="accordion-toggle collapsed" id="commentLoad"  data-uid="@productUid@" data-toggle="collapse" data-parent="#product-info"
                               href="#collapseFour" aria-expanded="false">
                                {Отзывы}
                            </a>
                        </div>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id="commentList" ></div>

                            <div id="addComment" class="well-sm" >
                                <div class="comment-head">{Оставьте свой отзыв}</div>
                                <textarea id="message" class="commentTextarea form-control"></textarea>
                                <input type="hidden" id="commentAuthFlag" name="commentAuthFlag"
                                       value="@php if($_SESSION['UsersId']) echo 1; else echo 0; php@" />
                                <br />
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-success btn-sm">
                                        <input type="radio" name="rate" value="1" /> +1
                                    </label>
                                    <label class="btn btn-success btn-sm">
                                        <input type="radio" name="rate" value="2" /> +2
                                    </label>
                                    <label class="btn btn-success btn-sm">
                                        <input type="radio" name="rate" value="3" /> +3
                                    </label>
                                    <label class="btn btn-success btn-sm">
                                        <input type="radio" name="rate" value="4" /> +4
                                    </label>
                                    <label class="btn btn-success btn-sm active">
                                        <input type="radio" name="rate" value="5" checked /> +5
                                    </label>
                                    <button class="btn btn-info btn-sm pull-right"
                                            onclick="commentList('@productUid@', 'add', 1);">
                                        {Проголосовать}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel" id="filesTab">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#product-file"
                               href="#collapseFive" aria-expanded="false">
                                {Файлы}
                            </a>
                        </div>
                    </div>
                    <div id="collapseFive" class="panel-collapse collapse files-block">
                        <div class="panel-body" id="files">@productFiles@</div>
                    </div>
                </div>
                
                 <div class="panel @php __hide('pagetemaDisp'); php@" id="pagesTab">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#product-page"
                               href="#collapseSix" aria-expanded="false">
                                {Статьи}
                            </a>
                        </div>
                    </div>
                    <div id="collapseSix" class="panel-collapse collapse">
                        <div class="panel-body" id="pages">@pagetemaDisp@</div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-md-5 col-lg-6" >
            <div class="product-info-block">
                <h1 class="page-header  hidden-xs">@productName@</h1>
                <div class="flex-block">
                    <span class="sale-icon-content rel-icon">
                        @specIcon@
                        @newtipIcon@
                        @promotionsIcon@
                    </span>
                    <div class="product-block-btn">
                        @ComStartNotice@

                        <a class="btn btn-circle @hideCatalog@" href="/users/notice.html?productId=@productUid@"
                           title="@productNotice@" style="font-size:18px;"><span class="icons-mail"></span></a>

                        @ComEndNotice@

                        <button class="btn btn-circle addToCompareList" data-uid="@productUid@"><span
                                class="icons-compare"></span></button>


                        <button class="btn btn-circle addToWishList" data-uid="@productUid@"><span
                                class="icons-like"></span></button>
                    </div>
                </div>
                <div class="product-page-price @hideCatalog@" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <span class="new-price  priceService" itemprop="price" content="@productSchemaPrice@">@productPrice@</span>
                    <span class="new-price rubznak" itemprop="priceCurrency" content="RUB">@productValutaName@</span>
                    <div class="old-price">@productPriceOld@ </div>
                    @ComStartNotice@
                    <div class="outStock">@productOutStock@</div>
                    @ComEndNotice@
                </div>

                <p></p>
                <div class="flex-block"></div>
                <div class="flex-block">
                    <div class="flex-block">
                        <div class="rating">
                            @rateUid@
                        </div>
                        <div class="rating-amount">{Отзывы}: @avgRateNum@ </div>
                    </div>
                    <div class="small">@productArt@</div>
                </div>
                <p></p>

                @optionsDisp@

                <div class="odnotip odnotip-@productUid@"> @productParentList@</div>
                @productservices_list@
                <div class="input-group addToCart @hideCatalog@">
                    <div class="quant-main @legoPurchaseDisabled@">
                        <div class="quant input-group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn btn-default btn-default_l btn-number"
                                        data-type="minus" data-field="quant[2]">
                                    -
                                </button>
                            </span>
                            <input type="text" name="quant[2]" class="form-control form-control_gr input-number" value="1" min="1" max="100">
                            <span class="input-group-btn">
                                <button type="button" class=" btn btn-default btn-default_r btn-number" data-type="plus"
                                        data-field="quant[2]">
                                    +
                                </button>
                            </span>
                        </div>
                    </div>
                    <button class="btn btn-primary addToCartFull @legoPurchaseDisabled@" data-num="1" data-uid="@productUid@">@productSale@</button>
                    @ComStartNotice@
                    <a href="/users/notice.html?productId=@productUid@" title="@productNotice@" class="btn btn-primary noticeBtn one" >{Товар под заказ}</a>
                    @ComEndNotice@
                </div>

                @oneclick@

                <!-- Модуль Vkseller -->
                <div class="@php __hide('vkseller_link'); php@">
                    <a class="oneclick-btn" href="@vkseller_link@" target="_blank">{Купить в ВКонтакте}</a>
                </div>

                <!-- Модуль Ozonseller -->
                <div class="@php __hide('ozonseller_link'); php@">
                    <a class="oneclick-btn" href="@ozonseller_link@" target="_blank">{Купить в} OZON</a>
                </div>

                <!-- Модуль Wbseller -->
                <div class="@php __hide('wbseller_link'); php@">
                    <a class="oneclick-btn" href="@wbseller_link@" target="_blank">{Купить в} Wildberries</a>
                </div>

                <div class="odnotipListWrapper">

                </div>
                <div class="clearfix"></div>

                <div class="flex-block option-block">
                    @sticker_size@ @sticker_shipping@
                    <a class="question" href="/forma/">{Задать вопрос по продукту}</a>
                </div>

                <div class="flex-block @hideCatalog@">
                    <div class="product-sklad" id="items">@productSklad@</div>
                    <a class="best-price" href="/pricemail/UID_@productUid@.html">@productBestPrice@</a>
                </div>
            </div>

            <div class="promo-info">@promotionInfo@</div>

        </div>
    </div>
    @productsgroup_list@
    <div class="inner-nowbuy border-row @php __hide('nowBuy'); php@">
        <h2 class="product-head page-header">{Сейчас покупают}</h2>
        <div class="swiper-slider-wrapper">
            <div class="swiper-button-prev-block">
                <div class="swiper-button-prev btn-prev4">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                </div>
            </div>
            <div class="swiper-button-next-block">
                <div class="swiper-button-next btn-next4">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </div>
            </div>
            <div class="swiper-container nowBuy-slider">
                <div class="swiper-wrapper">
                    @nowBuy@
                </div>
            </div>
        </div>
    </div>

</div>

<!--Модальное окно таблица размеров-->
<div class="modal fade bs-example-modal-sm size-modal" id="sizeModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{Таблица размеров}</h4>
            </div>
            <form method="post" name="user_forma_size_delivery" action="@ShopDir@/returncall/">
                <div class="modal-body">

                    @productOption1@
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>

                </div>
            </form>
        </div>
    </div>
</div>
<!--Модальное окно таблица размеров-->
<!--Модальное окно информация о доставке-->
<div class="modal fade bs-example-modal-sm size-modal" id="shipModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{Информация о доставке}</h4>
            </div>
            <form method="post" name="user_forma_size_delivery" action="@ShopDir@/returncall/">
                <div class="modal-body">

                    @productOption2@

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{Закрыть}</button>

                </div>
            </form>
        </div>
    </div>
</div>
<!--Модальное окно информация о доставке-->