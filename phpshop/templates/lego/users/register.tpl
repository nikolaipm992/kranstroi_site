<style>.col-md-12 form[name="user_forma_register"] {margin:0 auto; float:none;}
    .col-md-12 .page-header {text-align:center;}
</style>
<div id="usersError" class="hide">@usersError@</div>

<form method="post" name="user_forma_register" class="register-form">
    <span id="user_error">@user_error@</span>
    <div class="form-group">
        <label>{Имя}</label>
        <input type="text"  name="name_new" value="@php echo $_POST['name_new']; php@"  class="form-control" required="" >
    </div>
    <div class="form-group">
        <label>E-mail</label>
        <input type="email" name="login_new" value="@php echo $_POST['login_new']; php@" class="form-control" required="" >
    </div>
    <div class="form-group">
        <label>{Телефон}</label>
        <input type="tel" name="tel_new" value="@php echo $_POST['tel_new']; php@" class="form-control" @sms_login_control@>
    </div>
    <div class="form-group">
        <label>{Пароль}</label>
        <input type="password" name="password_new"  class="form-control"  required="" >
    </div>
    <div class="form-group" id="check_pass">
        <label>{Повторите пароль}</label>
        <input type="password" name="password_new2"  class="form-control" required="">
        <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span
        <p class="small"><label><input type="checkbox" value="on" name="rule" class="req" checked="checked" required>  {Я согласен} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{на обработку моих персональных данных}</a></label></p>
    </div>
    <div>
        @captcha@
    </div>
    <p><br></p>
    <p>
        <input type="hidden" value="1" name="add_user">
        <button type="reset" class="btn btn-default">{Очистить}</button>
        <button type="submit" class="btn btn-primary">{Регистрация пользователя}</button>
    </p>
</form>
