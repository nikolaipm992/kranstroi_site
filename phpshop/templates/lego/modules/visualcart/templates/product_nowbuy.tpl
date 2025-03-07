<div class="modal-nowBuy  @php __hide('nowbuy_close','cookie'); php@">
    <div class="nowBuy-img"><img src="@product_nowBuy_img@" alt="@product_nowBuy_name@" title="@product_nowBuy_name@"></div>
    <div class="nowBuy-info">
        <p class="">{Кто-то купил}:<br />
            <a href="/shop/UID_@product_nowBuy_id@.html" title="@product_nowBuy_name@">@product_nowBuy_name@</a><br>
           @product_nowBuy_price@ <span class="rubznak">@productValutaName@</span>
        </p>
        <p class="nowBuy-sklad">{Осталось в наличии}: @product_nowBuy_items@ {шт}.</p>
    </div>
    <span class="close nowbuy-close"><i class="fal fa-times" aria-hidden="true"></i></span>
</div>