<p>@usersError@ @mesageText@</p>
<form method="post" name="user_forma" action="@ShopDir@/partner/">
    <div class="form-group">
        <input type="email"  placeholder="E-mail" name="login" value="@php echo $_POST['login']; php@" class="form-control" required="" >
    </div>
    <div class="form-group">
        <input type="text"  placeholder="{���}" name="name" value="@php echo $_POST['name']; php@" class="form-control" required="" >
    </div>
    <div class="form-group">
        <input placeholder="{������}" type="password" name="password"  class="form-control"  required="" >
    </div>
    <div>
        @captcha@
    </div>
    <br>
    <div class="form-group">
        <p class="small">
            <input type="checkbox" value="on" name="rule" class="req" checked="checked"> 
            {� ��������}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html" alt="{�������� �� ��������� ������������ ������}">{�� ��������� ���� ������������ ������}</a> 
        </p>
    </div>
    <input  type="submit" name="add_user" class="btn btn-primary" value="{�����������}">
</form>