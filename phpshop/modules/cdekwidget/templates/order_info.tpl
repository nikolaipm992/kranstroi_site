<table class="table table-bordered">
    <tbody>
    <tr>
        <td>{������ ������}</td>
        <td>@cdek_status@</td>
    </tr>
    @cdek_errors@
    <tr>
        <td>{������ ������}</td>
        <td>@cdek_payment_status@</td>
    </tr>
    <tr>
        <td>{������ ��������}</td>
        <td>@cdek_delivery_info_type@</td>
    </tr>
    <tr>
        <td>{���������� � ��������}</td>
        <td>@cdek_delivery_info@</td>
    </tr>
    </tbody>
</table>
<div class="row" style="padding-bottom: 20px;">
    <div class="col-sm-12" style="@cdek_hide_actions@">
        <button type="button" class="btn btn-sm btn-primary cdek-change-address">{��������}</button>
        <button type="button" class="btn btn-sm btn-success cdek-send">{��������� ����� � ������� ����}</button>
    </div>
</div>

<table class="table table-bordered" style="@cdek_statuses_hidden@">
    <thead>
    <tr>
        <th>{������ ������}</th>
        <th>{����}</th>
    </tr>
    </thead>
    <tbody>
    @cdek_statuses@
    </tbody>
</table>

<script src="../modules/cdekwidget/js/cdekwidget.js"></script>
@cdek_popup@
<input type="hidden" name="cdek_order_id" value="@cdek_order_id@">
<input type="hidden" id="dop_info" name="fakefield">