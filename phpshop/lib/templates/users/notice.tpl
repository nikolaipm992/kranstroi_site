
<p><br></p>
<div class="row">
    <div class="col-xs-12 col-md-5 text-center">
        <a href="/shop/UID_@productId@.html"><img src="@pic_big@" alt="@name@" border="0" class="notice-img"></a>
    </div>
    <div class="col-xs-12 col-md-7">
        <h4>@productName@</h4>
        <div>
            <a href="javascript:history.back(1)" class="btn btn-info">{���������}</a>
            <a href="./notice.html" class="btn btn-info">{����� ������}</a>
        </div>
        <form method="post" name="forma_message" action="./notice.html" class="form-horizontal">
            <div class="form-group">
                <div class="col-xs-12">
                    <label class="control-label">{�������������� ����������}:</label>
                </div>
                <div class="col-xs-12">
                    <textarea class="form-control" name="message" id="message"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <label class="control-label">{�� �������}:</label>
                </div>
                <div class="col-xs-6">
                    <select class="form-control" name="date">
                        <option value="1" SELECTED>1 {������}</option>
                        <option value="2">2 {�������}</option>
                        <option value="3">3 {�������}</option>
                        <option value="4">4 {�������}</option>
                    </select>
                </div>
                <div class="col-xs-6">
                    <input class="btn btn-info" type="submit" value="{��������� ������}" name="add_notice">
                    <input type="hidden" value="@productId@" name="productId">
                </div>
            </div>
        </form>
    </div>
    <div class="col-xs-12">
        <div id="allspecwhite">
        </div>
        @user_message@
    </div>
</div>