<h1 class="h2 page-title d-none">{�����-����} @priceCatName@</h1> 

<ul class="nav">
    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{������� ��������}</a>
    <div class="dropdown-menu" style="overflow-y: auto;max-height:400px">
       @searchPageCategoryDrop@
    </div>
  </li>
    <li class="nav-item"><a class="nav-link" href="/shop/CID_@PageCategory@.html" id="price-form" data-uid="@PageCategory@">{����� � ���������}</a></li>
    <li class="nav-item"><a class="nav-link" href="phpshop/forms/priceprint/print.html?catId=@PageCategory@">{�������� �����}</a></li>
    <li class="nav-item"><a class="nav-link" href="/files/priceSave.php?catId=@PageCategory@">Excel {�����}</a></li>
    <li class="nav-link @onlinePrice@"><a class="nav-link" href="/files/onlineprice/">{������������� �����}</a></li>
</ul>

<div class="container space-top-1">
    @productPageDis@
</div>