<SCRIPT language="JavaScript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript" src="java/jqfunc.js"></SCRIPT>
<link href="phpshop/lib/templates/order/style.css" type="text/css" rel="stylesheet">
@order_action_add@
<form method="post" name="forma_order" action="/done/">
    <div id="checkout">
        <div id="checkout">
            <b>����� �</b>
            <input type="text" name=ouid style="width:50px; height:18px; font-family:tahoma; font-size:11px ; color:#9e0b0e; background-color:#f2f2f2;" value="@orderNum@"  readonly="1">
            <b>/</b>
            <input type="text" style="width:50px; height:18px; font-family:tahoma; font-size:11px ; color:#9e0b0e; background-color:#f2f2f2;" value="@orderDate@"  readonly="1"><BR>

            <BR><BR>
            <h2>������ ������</h2>

            <div class="checkout-heading">��� 1: ������ ������@authData@</div>
            @noAuth@
        </div>
        <div id="checkout">
            <div class="checkout-heading">��� 2: ��������, ����� ����������</div>
            <div class="checkout-content" style="display: block;">
                <div class="left">
                    <h2>������ ��������</h2>

                    @orderDelivery@ 
                    <BR><BR>
                    @UserAdresList@
                </div>
                <div id="login" class="right">
                    <h2>����� ��������</h2>
                    @noAuthAdr@
                    <div id="userAdresData1">

                        <div id="citylist">
                            <span class="required">*</span>
                            ������
                            <br>
                            <select name="country_new" class="citylist req">
                                <option value="" for="0">-----------</option>
                                <option value="RU">������</option>
                                <option value="BY">����������</option>
                                <option value="UA">�������</option>
                                <option value="KZ">���������</option>
                            </select>
                        </div>

                        <span class="required">*</span>
                        �����
                        <br>
                        <input type="text" value="" name="city_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        ������
                        <br>
                        <input type="text" value="" name="index_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        �����
                        <br>
                        <input type="text" value="" name="street_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        ���
                        <br>
                        <input type="text" value="" name="house_new" class="req">
                        <br><br> 
                        
                        <span class="required">*</span>
                        ��������
                        <br>
                        <input type="text" value="" name="flat_new" class="req">
                        <br><br>

                    </div>
                    <br>
                    �������������� ���������� � ������: 
                    <textarea style="width:300px; height:100px; font-family:tahoma; font-size:11px ; color:#4F4F4F " name="dop_info" ></textarea>
                </div>
            </div>
            <BR>
        </div>
        <div id="checkout">
            <div class="checkout-heading">��� 3: ������ ������</div>
            <BR>
            <div class="checkout-content" style="display: block;">
                @orderOplata@
                <br>
                <br>
                <div id="showYurDataForPaymentLoad">
                </div>
            </div>
        </div>
        <div id="checkout">
            <div class="checkout-heading">��� 4: �������� �����</div>
            <BR>
            <div class="checkout-content" style="display: block;">
                <div class="left">
                    <img src="images/shop/brick_error.gif" border="0" align="absmiddle"> <a href="javascript:forma_order.reset();" class=link>�������� �����</a>
                    <input type="hidden" name="send_to_order" value="ok" >
                    <input type="hidden" name="d" id="d" value="@deliveryId@">
                    <input type="hidden" name="nav" value="done">
                </div>
                <div id="login" class="right">
                    <img src="images/shop/brick_go.gif"  border="0" align="absmiddle"> <a href="javascript:OrderChekJq();" class=link>�������� �����</a>
                </div>
            </div>
        </div>
    </div>
</form>
@showYurDataForPayment@