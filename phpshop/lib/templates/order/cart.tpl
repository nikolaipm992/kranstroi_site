<table border=0 width=99% cellpadding=0 cellspacing=3>
    <tr>
        <td width=50 class="mobHideCol"></td>
        <td width="40%"><strong>{������������}</strong></td>
        <td width="10%" align="left"><strong>{���-��}</strong></td>
        <td width="10%" align="center"  class="mobHideCol"><strong>{��������}</strong></td>
        <td width="15%" align="right"><strong>{���� 1 ��.}</strong></td>
        <td width="15%" align="right"><strong>{���������}</strong></td>
        <td align="right">&nbsp;</td>
    </tr>
    <tr>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td colspan="5">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
    </tr>
    @display_cart@
    <tr>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td colspan="5">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
    </tr>
    <tr>
        <td>
            <b>{�����}:</b>
        </td>
        <td class="mobHideCol"></td>
        <td width="55" >
            <strong>@cart_num@</strong> ({��.})
        </td>

        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red"></td>
        <td align="right" class="red">@cart_sum@ @currency@</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td  class="mobHideCol">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
        <td colspan="5">
            <img src="images/shop/break.gif" width="100%" height="1" border="0">
        </td>
    </tr>

    <tr style="visibility:hidden;display:none;">
        <td colspan="2">
            {��� �������}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><span id="WeightSumma">@cart_weight@</span> {��.}</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td colspan="2">
            {������}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red" id="SkiSummaAll"><span id="SkiSumma">@discount@</span>&nbsp;%</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td colspan="2">
            {��������}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><span id="DosSumma">@delivery_price@</span>&nbsp; @currency@</td>
        <td align="right" class="red"></td>

    </tr>
    <tr>
        <td colspan="2">
            {� ������ � ������ ������}:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><b><span id="TotalSumma">@total@</span></b> @currency@</td>
        <td align="right" class="red"></td>

    </tr>
</table>
<input type="hidden" id="OrderSumma" name="OrderSumma"  value="@cart_sum@">
<script>
    if (window.document.getElementById('num')) {
        window.document.getElementById('num').innerHTML = '@cart_num@';
        window.document.getElementById('sum').innerHTML = '@cart_sum@';
    }
</script>