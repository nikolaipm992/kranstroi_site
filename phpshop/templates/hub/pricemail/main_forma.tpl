@Error@

<div class="col-xs-12">
    <div class="product-col list clearfix">
        <div class="col-md-6">
            <div class="row">
                <div class="image">
                    <a href="/shop/UID_@productUid@.html" title="@productName@"><img src="@productImg@" alt="@productName@"></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="caption">
                    <h4><a href="/shop/UID_@productUid@.html" title="@productName@">@productName@</a></h4>
                    <div class="description">
                        @productDes@
                    </div>
                    <div class="price">
                        <span class="price-new">@productPrice@ <span class="rubznak">@productValutaName@</span></span> 
                        <span class="price-old">@productPriceRub@</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12">
        <h1 class="page-title hide">{������ ������}</h1>
</div>

<form method="post" name="forma_message" class="template-sm">
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            <input placeholder="{������ �� ����� � ������� �����}" type="text" name="link_to_page" value="@php  echo $_POST[link_to_page]; php@" class="form-control"  required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            <input placeholder="{���}" type="text" name="name_person" value="@php  echo $_POST[name_person]; php@" class="form-control"  required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            <input placeholder="E-mail" type="email" name="mail" value="@php  echo $_POST[mail]; php@" class="form-control" required="">
        </div>
    </div>
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            <input placeholder="{�������}" type="text" name="tel_name" value="@php  echo $_POST[tel_name]; php@" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            <input placeholder="{��������}" type="text" name="org_name" value="@php  echo $_POST[org_name]; php@" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            <textarea placeholder="{�������������� ����������}" name="adr_name" class="form-control">@php  echo $_POST[adr_name]; php@</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="">
        </div>
        <div class="">
            @captcha@
        </div>
    </div>
	<p class="small"><label><input name="rule" value="1" required="" checked="" type="checkbox"> @rule@</label></p>

    <div class="form-group">
        <div class=""></div>
        <div class="">
            <input type="hidden" name="send_price_link" value="ok">
            <button type="submit" class="btn btn-main">{������������ �� ����}</button>
        </div>
    </div>
</form>    
