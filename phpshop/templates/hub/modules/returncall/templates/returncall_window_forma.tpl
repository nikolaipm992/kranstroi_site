<a href="#" data-toggle="modal" data-target="#returnCallModal" class="header-link-color "><i class="iconz-telephone"></i>{�������� ������}</a>
        <!-- ��������� ���� returncall-->
        <div class="modal fade bs-example-modal-sm return-call" id="returnCallModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">@leftMenuName@</h4>
                    </div>
                     <form method="post" name="user_forma" action="@ShopDir@/returncall/">
                    <div class="modal-body">
                       
                            <div class="form-group">
                                <input type="text" name="returncall_mod_name" class="form-control" placeholder="{���}" required="">
                            </div>
                            <div class="form-group">
                                <input type="text" name="returncall_mod_tel" class="form-control" placeholder="{�������}" required="">
                            </div>
                            <div class="form-group">
                                <input placeholder="{����� ������}" class="form-control" type="text" name="returncall_mod_time_start">
                            </div>
                            <div class="form-group">
                                <textarea placeholder="{���������}" class="form-control" name="returncall_mod_message"></textarea>
                            </div>
                            @returncall_captcha@
<p class="small"><label><input type="checkbox" value="on" name="rule" class="req" checked="checked">  {� ��������} <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html">{�� ��������� ���� ������������ ������}</a></label></p>
                       
                    </div>
                     <div class="modal-footer">
                                <input type="hidden" name="returncall_mod_send" value="1">
                                <button type="submit" class="btn btn-main">{�������� ������}</button>
                     </div>
                     </form>
                </div>
            </div>
        </div>