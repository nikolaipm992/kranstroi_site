<div id="usersError" class="hide">@usersError@</div>
<form method="post" name="user_forma_register" class="template-sm">
    <span id="user_error">@user_error@</span>
    <div class="form-group">
        <input type="text"  placeholder="{���}" name="name_new" value="@php echo $_POST['name_new']; php@"  class="form-control" required="" >
    </div>
    <div class="form-group">
        <input type="email"  placeholder="E-mail" name="login_new" value="@php echo $_POST['login_new']; php@" class="form-control" required="" >
    </div>
    <div class="form-group">
        <input type="tel" name="tel_new" placeholder="{�������}"  value="@php echo $_POST['tel_new']; php@" class="form-control" @sms_login_control@>
    </div>
    <div class="form-group">
        <input placeholder="{������}" type="password" name="password_new"  class="form-control"  required="" >
    </div>
    <div class="form-group" id="check_pass">
        <input type="password" placeholder="{��������� ������}" name="password_new2"  class="form-control" required="">
        <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
    </div>

    <div>
        @captcha@
    </div>
    <br>
    <div class="form-group">
        <p class="small">
            <input type="checkbox" value="on" name="rule" class="req" checked="checked" required> 
            {� ��������}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a>
        </p>
    </div>
    <p>
        <input type="hidden" value="1" name="add_user">
        <button type="reset" class="btn btn-default">{��������}</button>
        <button type="submit" class="btn btn-primary">{����������� ������������}</button>
    </p>

</form>