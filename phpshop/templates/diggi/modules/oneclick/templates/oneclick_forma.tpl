<form method="post" name="user_forma" action="@ShopDir@/oneclick/">
    <div class="form-group">
        <label>{���}</label>
        <input type="text" name="oneclick_mod_name" class="form-control" placeholder="{���}" required="">
    </div>
    <div class="form-group">
        <label>{�������}</label>
        <input type="text" name="oneclick_mod_tel" class="form-control" placeholder="{�������}" required="">
    </div>
    <p>@oneclick_captcha@</p>
	<p class="small"><label><input type="checkbox" value="on" name="rule" class="req" checked="checked">  {� ��������} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a></label></p>
    <div class="text-center">
        <input type="hidden" name="oneclick_mod_product_id" value="@productUid@">
        <input type="hidden" name="oneclick_mod_send" value="1">
        <button type="submit" class="btn btn-primary">{�������� ������}</button>
    </div>
</form>