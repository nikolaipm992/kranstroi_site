
<ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList">
    @breadCrumbs@
</ol>
<div class="page-header">
    <h2>@catalogCategory@</h2>
</div>

<div class="well hidden-xs" id="filter-selection-well">
    <div class="row">
        <div class="col-md-6">
            ����� �������:  

            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-default glyphicon glyphicon-th-list @gridSetAactive@" data-toggle="tooltip" data-placement="top" title="{������ �������}">
                    <input type="radio" name="gridChange" value="1"  autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=1">
                </label>
                <label class="btn btn-sm btn-default glyphicon glyphicon-th @gridSetBactive@" data-toggle="tooltip" data-placement="top" title="{������ ������}">
                    <input type="radio" name="gridChange" value="2" autocomplete="off" data-url="?@productVendor@@php if(isset($_GET['f'])) echo '&f='.$_GET['f']; if(isset($_GET['s'])) echo  '&s='.$_GET['s']; php@&gridChange=2">
                </label>
            </div> 

        </div>
        <div class="col-md-6 text-right">


            ����������: 
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-default glyphicon glyphicon-signal @sSetCactive@" data-toggle="tooltip" data-placement="top" title="{�������}">
                    <input type="radio" name="s" value="3" autocomplete="off" data-url="?@productVendor@&s=3@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
                <label class="btn btn-sm btn-default glyphicon glyphicon-sort-by-alphabet @sSetBactive@" data-toggle="tooltip" data-placement="top" title="{������������}">
                    <input type="radio" name="s" value="1"  autocomplete="off" data-url="?@productVendor@&s=1@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
                <label class="btn btn-sm btn-default glyphicon glyphicon glyphicon-sort-by-order @sSetAactive@" data-toggle="tooltip" data-placement="top" title="{����}">
                    <input type="radio" name="s" value="2" autocomplete="off" data-url="?@productVendor@&s=2@php if(isset($_GET['f'])) echo  '&f='.$_GET['f']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>

            </div>    

            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-default glyphicon glyphicon-sort-by-attributes @fSetBactive@" data-toggle="tooltip" data-placement="top" title="{�� �����������}">
                    <input type="radio" name="f" value="1"  autocomplete="off" data-url="?@productVendor@&f=1@php if(isset($_GET['s'])) echo  '&s='.$_GET['s']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
                <label class="btn btn-sm btn-default glyphicon glyphicon-sort-by-attributes-alt @fSetAactive@" data-toggle="tooltip" data-placement="top" title="{�� ��������}">
                    <input type="radio" name="f" value="2" autocomplete="off" data-url="?@productVendor@&f=2@php if(isset($_GET['s'])) echo  '&s='.$_GET['s']; if(isset($_GET['gridChange'])) echo '&gridChange='.$_GET['gridChange']; php@">
                </label>
            </div>  


        </div>
    </div> 
    <a name="sort"></a>  
</div>
<div class="template-product-list">@productPageDis@</div>
<div class="clearfix"></div>
@productPageNav@