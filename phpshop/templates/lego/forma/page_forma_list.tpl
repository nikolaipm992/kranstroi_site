<style>.col-md-12 form[name="forma_message"] {margin:0 auto; float:none;}
    .col-md-12 .page-header:not(.product-head ) {text-align:center;}
</style>
@Error@
<div class="row">
    <form class="col-lg-5 col-md-6 col-sm-7 col-xs-12" method="post" name="forma_message">
        <div class="form-group">
            <input type="text" name="tema" placeholder="{���������}" value="@php  echo $_POST['tema']; php@" class="form-control" id="exampleInputEmail1"  required="">
        </div>
        <div class="form-group">
            <input type="text" name="name" placeholder="{���}" value="@php  echo $_POST['name']; php@" class="form-control" id="exampleInputEmail1"  required="">
        </div>
        <div class="form-group">
            <input type="email" name="mail" placeholder="E-mail" value="@php  echo $_POST['mail']; php@" class="form-control" id="exampleInputEmail1">
        </div>
        <div class="form-group">
            <input type="text" name="tel" placeholder="{�������}" value="@php  echo $_POST['tel']; php@" class="form-control" id="exampleInputEmail1">
        </div>
        <div class="form-group">
            <textarea name="content" placeholder="{���������}" class="form-control" required="">@php  echo $_POST['content']; php@</textarea>
        </div>
        <p class="small"><label><input type="checkbox" value="on" name="rule" class="req" checked="checked" required>  {� ��������} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a></label></p>

        <div class="form-group">
            @captcha@
            <br/>
            <span class="pull-right">
                <input type="hidden" name="send" value="1">
                <button type="submit" class="btn oneclick-btn">{��������� ���������}</button>
            </span>

        </div>

    </form>  
</div>  