<tr>
    <td class="image">            
        <a href="@ShopDir@/shop/UID_@prodId@.html"><img src="@prodPic@" alt="@prodName@" style="max-width: 40px; max-height: 40px;"  title="@prodName@" ></a>
    </td>
    <td class="name"><a href="@ShopDir@/shop/UID_@prodId@.html">@prodName@</a></td>
    <td class="price">            
        <div class="price">
            @prodPrice@ @productValutaName@
        </div>
    </td>
    <td class="action">
        <img src="images/cart-add.png" alt="{� �������}" title="{� �������}" class="@wishlistCartHide@" onclick="AddToCart('@prodId@');">&nbsp;&nbsp;
        <a href="?delete=@prodId@"><img src="images/remove.png" alt="{������� �� ����������}" title="{������� �� ����������}"></a>
    </td>
</tr>