<h1 class="h2 page-title d-none">{����� ������}</h1> 
@Error@

<form method="post" name="forma_gbook">
    <div class="form-group">
        <input type="text" name="name_new" class="form-control" id="exampleInputEmail1" placeholder="{���}" required="">
    </div>
    <div class="form-group">
        <input type="email" name="mail_new"  class="form-control" id="exampleInputEmail1" placeholder="Email">
    </div>
    <div class="form-group">
        <input type="text"  name="tema_new"  class="form-control" id="exampleInputEmail1" placeholder="{���������}" required="">
    </div>
    <div class="form-group">
        <textarea name="otsiv_new" class="form-control" maxlength="500" placeholder="{���������}" required="" style="height:200px"></textarea>
    </div>
    @captcha@
    <div class="form-group">
        <p class="small">
            <input type="checkbox" value="on" name="rule" class="req" checked="checked"> 
            {� ��������}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a>
        </p>
    </div>    
    <div class="form-group">
        <span class="pull-right">
            <input type="hidden" name="send_gb" value="1">
            <button type="submit" class="btn btn-primary">{��������� �����}</button>
        </span>


    </div>
</form>
<p><br><br></p>
<div class="clearfix"></div>