<!-- Product Filter Starts -->
<div class="mobile-filter">Фильтр</div>
<div class="big-filter-wrapper filter-row">
    <span class="close filter-close visible-xs"><i class="fal fa-times" aria-hidden="true"></i></span>
    <div class="clearfix"></div>
    <!-- Фасетный фильтр -->
    <div class="hide" id="faset-filter" >
        <div id="price-filter-body">
            <div class="faset-filter-block-wrapper @hideCatalog@">
                <h4>{Цена}</h4>
                <div>
                    <form method="get" id="price-filter-form">
                        <div class="row">
                            <div class="col-md-6 col-xs-6" id="price-filter-val-min">

                                <input type="text" class="form-control-price form-control input-sm" name="min" value="@price_min@" > 
                            </div>
                            <div class="col-md-6 col-xs-6 " id="price-filter-val-max">

                                <input type="text" class="form-control-price form-control input-sm pull-right" name="max" value="@price_max@"> 
                            </div>
                        </div>
                        <div id="slider-range" ></div>
                    </form>
                </div>
            </div>
        </div>
        <a href="?" class="faset-filter-reset" id="faset-filter-reset" data-toggle="tooltip" data-placement="top" title="{Сбросить фильтр}">{Сбросить фильтр}</a>
        <div class="clearfix"></div>
        <div id="faset-filter-body" class="grid">{Загрузка}...
            <div class="clearfix"></div>
        </div>


    </div>
    <div class="filter-btn-block">			
        <a href="?" class="faset-filter-reset filter-btn visible-xs  btn-default btn"  data-toggle="tooltip" data-placement="top" title="{Сбросить фильтр}">{Сбросить фильтр}</a>
        <span class="filter-close oneclick-btn filter-btn visible-xs"  >{Применить}</span>
    </div>
    <!--/ Фасетный фильтр -->
</div>
<style type="text/css">.main-container  .row, section .row{margin:0}</style>
<div class="product-filter @hideSort@" id="filter-well">
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="display d-flex @hideCatalog@">
                {Сначала}: <div class="filter-menu-wrapper">
                    <div class="btn-group filter-menu" data-toggle="buttons">

                        <label class="btn btn-sm btn-sort @sSetCactive@" checked="checked">
                            <input type="radio" name="s" value="3"> {Популярные} 
                        </label>

                        <label class="btn btn-sm btn-sort" >
                            <input type="radio" name="s" value="2&f=2"> {Дорогие}
                        </label>
                        <label class="btn btn-sm btn-sort " >
                            <input type="radio" name="s" value="2&f=1"> {Дешевые}
                        </label>
                        <label class="btn btn-sm btn-sort " >
                            <input type="radio" name="s" value="4&f=2"> {По размеру скидки}
                        </label>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col-md-7 col-sm-7 col-xs-12 text-right">
            <div class="btn-group" role="group" aria-label="...">
                @warehouse_sort@
            </div>
        </div> 
        <div class="col-md- col-sm-2 hidden-xs text-right">

            <div class="display" data-toggle="buttons">
                <label class="btn btn-sm fal fa-bars btn-sort @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{Товары списком}">
                    <input type="radio" name="gridChange" value="1">
                </label>
                <label class="btn btn-sm fal fa-th btn-sort @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{Товары сеткой}">
                    <input type="radio" name="gridChange" value="2">
                </label>
            </div>
        </div>

    </div> 
    <a ></a>
    <form method="post" action="/shop/CID_@productId@@nameLat@.html" name="sort" id="sorttable" class="hide">
        <table><tr>@vendorDisp@<td>@vendorSelectDisp@</td></tr></table>
    </form>                      
</div>
<!-- Product Filter Ends -->
