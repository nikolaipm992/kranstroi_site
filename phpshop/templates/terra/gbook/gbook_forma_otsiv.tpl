<ol class="breadcrumb visible-lg" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/" itemprop="item">
            <span itemprop="name">{�������}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/gbook/" itemprop="item">
            <span itemprop="name">{������}</span>
        </a>
        <meta itemprop="position" content="2" />
    </li>
    <li class="active">{����� ������}</li>
</ol>
<h1 class="main-heading2">{����� ������}</h1>
@Error@
<form method="post" name="forma_gbook">
    <div class="form-group">
        <div class="">
            <input type="text" name="name_new" class="form-control" id="exampleInputEmail1" placeholder="{���}" required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <input type="email" name="mail_new"  class="form-control" id="exampleInputEmail1" placeholder="Email">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <input type="text"  name="tema_new"  class="form-control" id="exampleInputEmail1" placeholder="{���������}" required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <textarea name="otsiv_new" class="form-control" maxlength="500" placeholder="{���������}" required=""></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="">
            <p class="small">
                <label>
                    <input type="checkbox" value="on" name="rule" class="req" checked="checked">  {� ��������} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a>
                </label>
            </p>
        </div>
        <div class="form-group">
            <div class="">
                @captcha@
            </div>
        </div>
        <div class="form-group">
            <div class="">
                    <span class="pull-right">
                        <input type="hidden" name="send_gb" value="1">
                        <button type="submit" class="btn btn-primary">{��������� �����}</button>
                    </span>
            </div>
        </div>
    </div>
</form>