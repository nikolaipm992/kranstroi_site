<table class="table product-table">
    <tr>
        <td  colspan="2">{��� �����}</td>
        <td  align="center">{����������}</td>


        <td  align="center">{�����}</td>
        <td></td>
        <td></td>
    </tr>

    @display_cart@
    <tr class="pad-10-20">
        <td>
            <b>{�����}:</b>
        </td>
        <td class=""></td>
        <td width="55" >
        </td>

        <td align="right" class="red">@cart_sum_discount_off@ <span class="rubznak">@currency@</span></td>
        <td></td>
        <td></td>
    </tr>

    <tr class="pad-10">
        <td>{������ � ������}:</td>
        <td class=""></td>
        <td class=""></td>
        <td align="right" class="red" id="SkiSummaAll"><span id="SkiSumma" class="text-danger" data-discount="@discount_sum@">- @discount_sum@</span> <span class="rubznak text-danger">@currency@</span></td>
        <td></td>
        <td></td>
    </tr> 
    <tr class="pad-20-10">
        <td>{��������}: <span id="deliveryInfo"></span></td>
        <td align="right" class="red" colspan="3"><span id="DosSumma">@delivery_price@</span>&nbsp; <span class="rubznak">@currency@</span></td>
        <td></td>
        <td></td>
    </tr>

    <tr >
        <td colspan="2">
            <b> {� ������ � ������ ������}:</b>
        </td>
        
        <td colspan="2" align="right" class="red"><span id="WeightSumma" class="hidden">@cart_weight@</span><b><span id="TotalSumma">@total@</span></b>&nbsp;<span class="rubznak">@currency@</span></td>
        <td></td>
        <td></td>
    </tr>

</table>
<input type="hidden" id="OrderSumma" name="OrderSumma"  value="@cart_sum_discount_off@">
<script>
    $(function() {
        $('#num').html('@cart_num@');
        $('#sum').html('@cart_sum@');
    });
</script>