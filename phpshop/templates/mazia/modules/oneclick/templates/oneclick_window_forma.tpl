<div class="modal fade bs-example-modal-sm oneclick-modal" id="oneClickModal@productUid@" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="h4 modal-title">{������� �����}</div>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
            </div>
            <form method="post" name="ajax-form" action="@ShopDir@/oneclick/" data-modal="oneClickModal@productUid@">
                <div class="modal-body">

                    <div class="form-group">
                       
                        <input type="text" name="oneclick_mod_name" class="form-control" placeholder="{���}" required="">
                    </div>
                    <div class="form-group">
                        
                        <input type="text" name="oneclick_mod_tel" class="form-control phone" placeholder="{�������}" required="">
                    </div>
					
                    @oneclick_captcha@
                        <div class="form-group">
                            <p class="small">
                            <input type="checkbox" value="on" name="rule" class="req" checked="checked"> 
                            {� ��������}  <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a>
                            </p>
                        </div>              </div>
                <div class="modal-footer">
                    <input type="hidden" name="oneclick_mod_product_id" value="@productUid@">
                    <input type="hidden" name="oneclick_mod_send" value="1">
                    <input type="hidden" name="ajax" value="1">
                    <button type="button" class="btn btn-soft-primary transition-3d-hover" data-dismiss="modal">{�������}</button>
                    <button type="submit" class="btn btn-primary transition-3d-hover">{������ � 1 ����}</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="mb-4">

            <button data-toggle="modal" data-target="#oneClickModal@productUid@"  type="button" class="list-add-cart-btn light-hover-btn border-0">{������� �����}</button><script>$(document).ready($('#oneClickModal@productUid@').appendTo($('body')));</script>


          </div>
