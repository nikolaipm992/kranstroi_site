@Error@
<form method="post" name="user_forma" action="@ShopDir@/partner/" class="template-sm">
    <div class="form-group">
        <input type="email"  placeholder="E-mail" name="login" value="@php echo $_POST['login']; php@" class="form-control" required="" >
    </div>
    <div class="form-group">
        <input placeholder="{������}" type="password" name="password"  class="form-control"  required="" >
    </div>
    <div class="form-group">
        <p>
            <input type="submit" name="send" class="btn btn-primary" value="{�����}">
            <input type="hidden" value="1" name="enter_user">
        </p>
        <p>
            <a href="@ShopDir@/partner/register_user.html" title="�����������">�����������</a><br>
            <a href="@ShopDir@/partner/sendpassword_user.html"  title="������ ������?">������ ������?</a><br>
            <a href="@ShopDir@/rulepartner/"  title="������� � ������� ���������� ���������">������� � ������� ���������� ���������</a><br>
        </p>
    </div>
</form>

