<div class="col-md-3 col-sm-3 product-block-wrapper product-block-wrapper-fix col-5" style="width:20%">
    <div class="product-block-top">
        <a class="product-img" href="/shop/UID_@productUid@.html" title="@productNameClean@">
            <img data-src="@productImg@" alt="@productNameClean@" class="swiper-lazy" src="@productImg@">
        </a>
        <span class="sale-icon-content ">
            @specIcon@
            @newtipIcon@
            <span class="label-block"> @hitIcon@
                @promotionsIcon@</span>
        </span>
        <div class="product-block-button">
            <a class="wrap-link" href="/shop/UID_@productUid@.html" title="@productNameClean@"></a>
            <a href="#" data-role="/shop/UID_@productUid@.html" class="btn btn-cart fastView btn-circle" data-toggle="modal" data-target="#modalProductView"><span class="icons-search"></span></a>
            <div class="btn-block"> 
                <a class="btn addToCartList @elementCartOptionHide@ @hideCatalog@" data-title="{Выбрать}" data-placement="top" data-toggle="tooltip" href="/shop/UID_@productUid@.html"><span class="icons-cart"></span></a>
                <button class="btn  addToCartList @elementCartHide@ @hideCatalog@" data-uid="@productUid@" data-num="1" data-title="{Купить}" data-placement="top" data-toggle="tooltip"><span class="icons-cart"></span></button>

                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><span class="icons-like"></span></button>
                <button class="btn btn-wishlist addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><span class="icons-compare"></span></button>

                <a class="btn btn-cart @elementNoticeHide@ @hideCatalog@" href="/users/notice.html?productId=@productUid@" title="@productNotice@"  data-title="@productNotice@" data-placement="top" data-toggle="tooltip">
                    <span class="icons-mail"></span>
                </a>
            </div>

        </div></div>

    <a href="/shop/UID_@productUid@.html" class="caption">
        <div class="price-block">

            <h4 class="new-price @hideCatalog@">@parentLangFrom@ @productPrice@ <span class="rubznak">@productValutaName@</span></h4>
            <div class="h5 old-price">@productPriceOld@</div>
        </div>
        <div class="rating">
            @rateCid@
        </div>
        @previewSorts@
        <h5 class="product-name">@productName@</h5>
    </a>
</div>