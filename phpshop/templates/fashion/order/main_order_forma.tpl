@checkLabelForOldTemplatesNoDelete@
@order_action_add@

<form method="post" name="forma_order" id="forma_order" action="/done/">

    <div class="space-1">

        <!-- Title -->
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-7">
            <h1 class="h3 mb-0">{������ ������}</h1>
        </div>
        <!-- End Title -->

        <div>
            <input type="hidden" name="ouid" value="@orderNum@" readonly="1">
            <input type="hidden" value="@orderDate@"  readonly="1">
            <div>
                @authData@ @noAuth@                  
                <label><input type="checkbox" value="on" name="rule" class="req" checked="checked">  {� ��������} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a>
                </label>
            </div>
        </div>

    </div>
    <div class="space-1">

        <!-- Title -->
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-7">
            <h1 class="h3 mb-0">{��������, ����� ����������}</h1>
        </div>
        <!-- End Title -->

        <div class="row">
            <div class="col-md-6">
                
                <div class="radio">
                    @orderDelivery@
                </div>  

                @UserAdresList@

            </div>
            <div class="col-md-6">


                @noAuthAdr@
                <div id="userAdresData">
                </div>

                <textarea class="form-control" placeholder="{�������������� ���������� � ������}" name="dop_info" id="dop_info"></textarea>

            </div>
        </div>

    </div>

    <div class="space-1">

        <!-- Title -->
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-7">
            <h1 class="h3 mb-0">{������ ������}</h1>
        </div>
        <!-- End Title -->

        <div class="radio">
            @orderOplata@
        </div>
        <br>
        <div id="showYurDataForPaymentLoad">
        </div>

    </div>

    <div>
        <input type="hidden" name="send_to_order" value="ok">
        <input type="hidden" name="d" id="d" value="@deliveryId@">
        <input type="hidden" name="nav" value="done">
        <button type="reset" class="btn btn-soft-primary transition-3d-hover"> {��������}</button> 
        <button type="submit" class="btn btn-primary transition-3d-hover orderCheckButton"> {�������� �����}</button>
    </div>

</form>
@showYurDataForPayment@