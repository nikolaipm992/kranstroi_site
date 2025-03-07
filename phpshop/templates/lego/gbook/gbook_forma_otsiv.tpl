<ol class="breadcrumb visible-lg" itemscope itemtype="http://schema.org/BreadcrumbList">
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/" itemprop="item">
            <span itemprop="name">{Главная}</span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <li itemscope itemtype="http://schema.org/ListItem">
        <a href="/gbook/" itemprop="item">
            <span itemprop="name">{Отзывы}</span>
        </a>
        <meta itemprop="position" content="2" />
    </li>
    <li class="active"{Форма отзыва}</li>
</ol>

<div class="page-header">
    <h2>{Форма отзыва}</h2>
</div>


@Error@
<div class="row">
<form method="post" name="forma_gbook">
    <div class="form-group">
       
        <input type="text" name="name_new" class="form-control" id="exampleInputEmail1" placeholder="{Имя}" required="">
    </div>
    <div class="form-group">
        
        <input type="email" name="mail_new"  class="form-control" id="exampleInputEmail1" placeholder="Email">
    </div>
    <div class="form-group">
      
        <input type="text"  name="tema_new"  class="form-control" id="exampleInputEmail1" placeholder="{Заголовок}." required="">
    </div>
    <div class="form-group">
        
        <textarea name="otsiv_new" class="form-control" maxlength="500" placeholder="{Сообщение}" required=""></textarea>
    </div>
    <p class="small"><label><input name="rule" value="1" required="" checked="" type="checkbox"> @rule@</label></p>
	@captcha@
	<br>
    <div class="form-group">
        <span class="pull-right">
            <input type="hidden" name="send_gb" value="1">
            <button type="submit" class="btn btn-primary">{Отправить отзыв}</button>
        </span>
      

    </div>
</form>
</div>