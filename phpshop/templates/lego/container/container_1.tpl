<div class="container-fluid main-container">
    <div class="row">
        <div class="col-md-3 col-lg-3 hidden-sm hidden-xs  sidebar-left-inner">

            <!-- ���� ����������� ��������� -->
            <ul class="list-group @hideSite@" id="catalog-menu">@leftCatal@</ul>

            <div class="pageCatal pages">
                <div class="left-header"><a href="/page/">{����}</a></div>
                <ul class="pageCatalContent">@pageCatal@</ul></div>
            @leftMenu@
            <!--/ ���� ����������� ��������� -->
            <div class="@hitMainHidden@ @php __hide('hit'); php@ @hideCatalog@">
                <div class="left-header">{���� ������}</div>
                <div class="inner-nowbuy @hitMainHidden@ @php __hide('hit'); php@">
                    @hit@
                </div>
            </div>
            @banersDisp@
        </div>

        <div class="col-md-9 col-xs-12 main">
            @banersDispHorizontal@
            @DispShop@
            @getPhotos@
        </div>
    </div>

</div>