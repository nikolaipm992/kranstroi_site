
<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    @breadCrumbs@
</ol>
<div class="page-header">
    
    <h1>@catalogCategory@</h1>
</div>

<div class="product-filter hidden-xs" id="filter-selection-well">
    <div class="row">
        <div class="col-md-6">
           

           <div class="display" data-toggle="buttons">
                <label class="btn btn-sm fal fa-bars btn-sort @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{Товары списком}">
                    <input type="radio" name="gridChange" value="1"  autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=1">
                </label>
                <label class="btn btn-sm fal fa-th btn-sort glyphicon glyphicon-th @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{Товары сеткой}">
                    <input type="radio" name="gridChange" value="2" autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=2">
                </label>
            </div> 

        </div>


           <div class="col-md-6 filter-well-right-block col-xs-12">
                <div class="display">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-sort fal fa-signal-4  @sSetCactive@" data-toggle="tooltip" data-placement="top" title="{Рейтинг}">
                    <input type="radio" name="s" value="3" autocomplete="off" data-url="?s=3@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
                <label class="btn btn-sm btn-sort fal fa-sort-alpha-down @sSetBactive@" data-toggle="tooltip" data-placement="top" title="{Наименование}">
                    <input type="radio" name="s" value="1"  autocomplete="off" data-url="?s=1@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
                <label class="btn btn-sm btn-sort fal fa-sort-numeric-down @sSetAactive@" data-toggle="tooltip" data-placement="top" title="{Цена}">
                    <input type="radio" name="s" value="2" autocomplete="off" data-url="?s=2@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>

            </div>    

            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-xs btn-sort fal fa-sort-amount-up @fSetAactive@" data-toggle="tooltip" data-placement="top" title="{По возрастанию}">
                    <input type="radio" name="f" value="1"  autocomplete="off" data-url="?f=1@php if(isset($_GET['s'])) echo  '&s='.$_GET['s']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
					
                </label>
                <label class="btn btn-xs btn-sort fal fa-sort-amount-down  @fSetBactive@" data-toggle="tooltip" data-placement="top" title="{По убыванию}">
                    <input type="radio" name="f" value="2" autocomplete="off" data-url="?f=2@php if(isset($_GET['s'])) echo  '&s='.$_GET['s']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
            </div>  

    </div> 

        </div>
    </div> 
    <a name="sort"></a>  
</div>
<div class="template-product-list">@productPageDis@</div>
@productPageNav@