<table class="table product-table">
    <tr>
        <td  colspan="2">{��� �����}</td>
        <td class=""></td>
        <td  align="center">{����������}</td>
        
        
        <td  align="center">{�����}</td>
        <td  align="center" class=""></td>
    </tr>

    @display_cart@
    <tr class="pad-10-20">
        <td>
            <b>{�����}:</b>
        </td>
        <td class=""></td>
        <td width="55" >
           
        </td>

        <td class=""></td>
        <td align="right" class=""></td>
        <td align="right" class="red">@cart_sum_discount_off@ <span class="rubznak">@currency@</span></td>
    </tr>
    <!--<tr>
        <td colspan="2">
            ��� �������:
        </td>
        <td width="55" ></td>
        <td class="mobHideCol" width="30"></td>
        <td align="right" class="red" class="mobHideCol"></td>
        <td align="right" class="red"><span id="WeightSumma">@cart_weight@</span>{ ��.}</td>
        <td align="right" class="red"></td>

    </tr>-->
   
    <tr class="pad-10">
        <td>{������ � ������}:</td>
        <td class=""></td>
        <td class=""></td>
        <td></td>
        <td class=""></td>
        <td align="right" class="red" id="SkiSummaAll"><span id="SkiSumma" class="text-danger" data-discount="@discount_sum@">- @discount_sum@</span><span class="rubznak text-danger">@currency@</span></td>
    </tr> 
    <tr class="pad-20-10">
        <td>{��������}:</td>
        <td class="" style="display: none;"></td>
        <td class="" style="display: none;"></td>
        <td style="display: none;"></td>
        <td class="" style="display: none;"></td>
        <td align="right" class="red" colspan="5"><span id="DosSumma">@delivery_price@</span>&nbsp; <span class="rubznak">@currency@</span> <span id="deliveryInfo"></span></td>
    </tr>
    <tr>
        <td colspan="2">
           <b> {� ������ � ������ ������}:</b>
        </td>
        <td class=""></td>
        <td class=""></td>
        <td colspan="2" align="right" class="red"><span id="WeightSumma" class="hidden">@cart_weight@</span><b><span id="TotalSumma">@total@</span></b>&nbsp;<span class="rubznak">@currency@</span></td>
    </tr>
    
</table>
<input type="hidden" id="OrderSumma" name="OrderSumma"  value="@cart_sum_discount_off@">
<script>
    $(function() {
       $('#num').html('@cart_num@');
       $('#sum').html('@cart_sum@');
    });
</script>