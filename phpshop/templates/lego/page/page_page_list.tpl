<ol class="breadcrumb hidden-xs" itemscope itemtype="http://schema.org/BreadcrumbList">
    @breadCrumbs@
</ol>
<div class="page-header ">
    <h1>@pageTitle@</h1>
</div>
@catContent@
<div class="@php if(!isset($GLOBALS['SysValue']['other']['isPage'])) echo 'grid';  php@">
    @pageContent@
</div>
<div class="clearfix"></div>
<h3 class="@php __hide('pageLast'); php@  page-header last-header">{Интересно почитать}</h3>
<div class="@php if(!empty($GLOBALS['SysValue']['other']['pageLast'])) echo 'grid'; php@">@pageLast@</div>
<p>@odnotipDisp@</p>
