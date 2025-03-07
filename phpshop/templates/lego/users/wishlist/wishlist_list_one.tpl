<tr>
    <td class="visible-lg">            
        <a href="/shop/UID_@prodUid@.html"><img class="template-wishlist-list" src="@prodPic@" alt="@prodName@"  title="@prodName@"></a>
    </td>
    <td ><a href="/shop/UID_@prodUid@.html" class="wishlist-item">@prodName@</a></td>
    <td >            
        <div class="price">
            @prodPrice@ <span class="rubznak">@productValutaName@</span>
        </div>
    </td>
    <td >
        <a class="btn btn-main btn-sm @wishlistCartHide@" title="{В корзину}" onclick="addToCartList('@prodId@');" @prodDisabled@><span class="icons-cart"></span> В корзину</a>
        <a href="?delete=@prodId@" title="{Удалить}" class="btn btn-info btn-sm hidden-xs">X</a>
    </td>
</tr>