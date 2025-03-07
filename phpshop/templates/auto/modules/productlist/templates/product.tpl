<!-- ����� ProductList -->
<div class="js-slide mt-1 mb-3">
    <!-- Product -->
    <div class="card shadow-soft transition-3d-hover text-center" style="min-height:400px;">
        <div class="position-relative">
            <img class="card-img-top" src="@productlist_product_pic_small@" alt="@productlist_product_name@" loading="lazy">
        </div>

        <div class="card-body pt-4 px-3 pb-0">
            <div class="mb-2">
                <span class="d-block font-size-1">
                    <a class="text-inherit" title="@productlist_product_name@" href="@shopDir@@productlist_product_url@.html">@productlist_product_name@ </a>
                </span>
            </div>
        </div>


        <div class="card-footer border-0 pt-0 pb-1 px-1">
            <div class="d-block">
                <span class="text-dark font-weight-bold">@productlist_product_price@<span class="rubznak">@productValutaName@</span></span>
                <span class="text-body ml-1 @php __hide('productlist_product_price_old'); php@" ><del>@productlist_product_price_old@</del></span>
            </div>
            <div class="mb-3">
                <div class="d-inline-flex align-items-center small">
                    <div class="rating text-warning mr-0">
                        <div class="rating_blank"></div>
                        <div class="rating_votes" style="width:@productlist_product_rating@%"></div>
                    </div>
                </div>
            </div>
        </div>  

    </div>
    <!-- End Product -->
</div>
<!-- ����� ProductList -->