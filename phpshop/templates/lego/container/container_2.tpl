<div class="container-fluid main-container">
    <div class="row">


        <div class="col-md-9 col-xs-12 main">
            @banersDispHorizontal@
            @DispShop@
            @getPhotos@
        </div>

        <div class="col-md-3 col-lg-3 hidden-sm hidden-xs  sidebar-left-inner right-inner">

            <!-- ћеню дублирующих категорий -->
            <ul class="list-group @hideSite@" id="catalog-menu">
                @leftCatal@
            </ul>

            <div class="pageCatal">
                <div class="left-header"><a href="/page/">{Блог}</a></div>
                <ul class="pageCatalContent">@pageCatal@</ul></div>
            @leftMenu@
            <!--/ ћеню дублирующих категорий -->
            <div class="@hitMainHidden@ @php __hide('hit'); php@ @hideCatalog@">
                <div class="left-header">{Хиты продаж}</div>
                <div class="inner-nowbuy @hitMainHidden@ @php __hide('hit'); php@">@hit@</div>
            </div>
            @banersDisp@ 
        </div>

    </div>

</div>