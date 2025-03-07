<div class="product-day-panel">
    <div class="product-day-heading">{Товар дня}</div>
    <div class="product-day-body">

        <a href="/shop/UID_@productDayId@.html" class="product-day-img">
            <img class="media-object" src="@productDayPicSmall@" alt="@productDayName@">
        </a>
        <h3 class="new-price">@productDayPrice@ <span class="rubznak">@productValutaName@</span> <span
                class="old-price">@productDayPriceN@ <span class="rubznak">@productDayCurrency@</span></span></h3>
        <div class="rating">
            @rateCid@
        </div>
        <h4 class="product-name"><a href="/shop/UID_@productDayId@.html">@productDayName@</a></h4>
        <br> 
        <div class="clock" data-hour="@productDayTimeGood@"></div>
    </div>
</div>
<link rel="stylesheet" href="@php echo $GLOBALS['SysValue']['dir']['templates'].chr(47).$_SESSION['skin']; php@css/flipclock.css">