// Paypal add form
$(document).ready(function() {
    var add_paypal_form = '<div class="paypal_add_form"><br><div id="citylist"><span class="required">*</span> ������<br><select name="country_new" class="citylist req"><option value="RU" >������</option><option value="BY">����������</option><option value="UA">�������</option><option value="KZ">���������</option></select></div><br><span class="required">*</span> �����<br><input type="text" value="" name="city_new" class="req"><br><br><span class="required">*</span> ������<br><input type="text" value="" name="index_new" class="req"><br><br><span class="required">*</span> �����<br><input type="text" value="" name="street_new" class="req"><br><br><span class="required">*</span> ���<br><input type="text" value="" name="house_new" class="req"><br><br><span class="required">*</span> ��������<br><input type="text" value="" name="flat_new" class="req"><br><br></div>';

    // ����� �������������� ����� ��� paypal ��� ����� ������
    $("input#order_metod").change(function() {
        if($(this).val() == 10003)
        $(add_paypal_form).insertAfter("#dop_info");    
        else  $('.paypal_add_form').html('');

    });
    
    // ���� ������ paypal �� ���������
    if($("input#order_metod:checked").val() == 10003){
        $(add_paypal_form).insertAfter("#dop_info");
    }
});     