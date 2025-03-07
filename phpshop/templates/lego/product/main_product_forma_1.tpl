<div class="product-block-list">
    <div class="media">
        <div class="product-line-img">
            <a href="/shop/UID_@productUid@.html" title="@productNameClean@">
                <img src="@productImg@" alt="@productNameClean@"></a>
        </div>
        <div class="product-block-body">
            <div class="rating">@rateCid@</div> 

            <h3 class="media-heading"><a href="/shop/UID_@productUid@.html" title="@productNameClean@">@productName@</a></h3>
            <span class="sale-icon-content rel-icon">
                @specIcon@
                @newtipIcon@
                @hitIcon@
                @promotionsIcon@
            </span>
            @productDes@
            @previewSorts@
        </div>
        <div class="product-btn">

            <div class="price-block @hideCatalog@">

                <h4 class="new-price">@parentLangFrom@ @productPrice@ <span class="rubznak">@productValutaName@</span></h4>
                <div class="h5 old-price">@productPriceOld@</div>
            </div>
            @ComStartNotice@
            <div class="outStock @hideCatalog@">@productOutStock@</div>
            <br>
            @ComEndNotice@
            <a class="btn addToCartList @elementCartOptionHide@ @hideCatalog@" href="/shop/UID_@productUid@.html" data-title="{Выбрать}" data-placement="top" data-toggle="tooltip"><span class="icons-cart"></span>@productSale@</a>
            <button class="btn  addToCartList listBtn @elementCartHide@ @hideCatalog@" data-uid="@productUid@" data-num="1" data-title="{Купить}" data-placement="top" data-toggle="tooltip"><span class="icons-cart"></span> <span class="btn-text">@productSale@</span></button>

            <div class="btn-block">
                <a href="#" data-role="/shop/UID_@productUid@.html" class="btn btn-cart fastView "  data-toggle="modal" data-target="#modalProductView"><span class="icons-search"></span></a>
                <button class="btn btn-wishlist addToWishList" data-uid="@productUid@" data-title="{Отложить}" data-placement="top" data-toggle="tooltip"><span class="icons-like"></span></button>
                <button class="btn btn-wishlist addToCompareList" data-uid="@productUid@" data-title="{Сравнить}" data-placement="top" data-toggle="tooltip"><span class="icons-compare"></span></button>
                <a class="btn btn-cart @elementNoticeHide@ @hideCatalog@" href="/users/notice.html?productId=@productUid@" title="@productNotice@"  data-title="@productNotice@" data-placement="top" data-toggle="tooltip">
                    <span class="icons-mail"></span>
                </a>
            </div>
        </div>
    </div>

</div>