@Error@
<br>
<div class="pricemail">  <h4 class="page-header"><a href="/shop/UID_@productUid@.html" title="@productName@">@productName@</a></h4>
    <br>

    <div class="col-xs-12">
        <div class="product-col list clearfix">
            <div class="pricemail-img">

                <div class="image" style="text-align:center">
                    <a href="/shop/UID_@productUid@.html" title="@productName@" ><img src="@productImg@" alt="@productName@" style="max-height:250px; margin:0 auto"></a>
                </div>

            </div>
            <div class="pricemail-text">

                <div class="caption">
                    <div class="description">
                        @productDes@
                    </div>


                    <div class="price-block" >

                        <h4 class="new-price">@productPrice@<span class="rubznak">@productValutaName@</span></h4>
                        <h5 class="old-price"><span style="text-decoration: line-through">@productPriceRub@</h5>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <h1 class="page-title hide">{������ ������}</h1>
    </div>

    <form method="post" name="forma_message" class="col-md-6">
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
        <div class="form-group" id="check_pass">


            <span class="glyphicon glyphicon-remove form-control-feedback hide" aria-hidden="true"></span>
            <p class="small"><label><input type="checkbox" value="on" name="rule" class="req" checked="checked">  {� ��������} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a></label></p>
        </div>
        <div>
            @captcha@
        </div>
        <p><br></p>
        <div class="form-group">
            <div class=""></div>
            <div class="">
                <input type="hidden" name="send_price_link" value="ok">
                <button type="submit" class="btn btn-primary">{������������ �� ����}</button>
            </div>
        </div>
    </form>    
</div>