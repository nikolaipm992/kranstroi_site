<div class="product-page-button">
    <form action="https://anketa.alfabank.ru/alfaform-pos/endpoint" method="post" name="acreditForm" enctype="application/x-www-form-urlencoded">
        <textarea name="InXML" style="display:none;" >
@acredit_xml@
        </textarea>
        <div class="alert alert-warning" role="alert">
            �������� �������� �� ���� �����-�����, ���� ������� ������&nbsp;&nbsp;
            <input type="submit" value="������� �� ���� �����-�����"  class="btn btn-cart" />
        </div>
    </form>
</div>
<script>
    setTimeout(function(){
        document.acreditForm.submit();
    }, 3000);    
</script>