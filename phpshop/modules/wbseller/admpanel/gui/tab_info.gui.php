<?php

function tab_info($data) {
    global $PHPShopGUI;
    
    $Info = '<p>
      <h4>��������� ������</h4>
    <ol>
        <li>������������������ � <a href="https://seller.wildberries.ru" target="_blank">WB Partners</a>.</li>
        <li>� ������ �������� WB Seller ������� <a href="https://seller.wildberries.ru/supplier-settings/access-to-api" target="_blank">��������� - ������ �  API</a>, ������� ����� ����� (��� ������ <kbd>�����������</kbd>, ��� ������ �����). ���������� ����������� �������� ������ � ���� <kbd>API key</kbd> � ���������� ������.
        </li>
        <li>� ���������� ������ ������� ������ �������, ����������� � WB.</li>
        <li>� ���������� ������ ������� ����� WB. ���� ����� �� ������, �� ��� ����� �������������� ������� � <a href="https://seller.wildberries.ru/marketplace-pass/warehouses" target="_blank">��� ������ � ��������</a>.</li>
        <li>��� ����������� � ��������� �������� ������ ������ �� ����� � Wildberries ������������ ���������� <code>@wbseller_link@</code></li>
    </ol>

   <h4>�������� ������� � WB</h4>
   WB ��������� ������ �� ������� � ����� ���������� ������� �� ���������� � ��������������� �� ���� WB.
   <ol>
    <li>� �������� �������������� ��������� � �������� ����������� ��������� ���� ��������� � ���������� WB � �������� <kbd>WB</kbd>, ���� <kbd>���������� � WB</kbd>. ��� ������ ����� ��������� ����� �������� ����������� ���� ��������� WB, ��������� �� ������ �������� ������. ��������� ����� � ����������� ��������, ����� ���� �������� ���� "������������� ������������� � WB".</li>
    <li>����������� ��� ������� ����������� �������������� � ���������� ����������.</li>
    <li>� �������� �������������� ������ � �������� ����� �������� "������ - WB" �������� ����� <kbd>�������� ������� � WB</kbd> � ��������� ������. ������ ������� ��� �������� � WB �������� � ������� "������ - WB Partners - ������ ��� WB".</li>
    <li>����� �������� �������� ������ �������� � ������� <a href="https://seller.wildberries.ru/new-goods" target="_blank">������ - �������� ������� - ���������</a> � WB.</li>
    <li>��� �������������� �������� ��� � �������� ������� � WB �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/wbseller/cron/products.php</code>. ������� � ���� ����������� ��� �� ��� �������������� �������� ������ � ��������.</li>
  </ol>
  
  <h4>�������� ������� � WB</h4>
   <ol>
    <li>������ ������� ��� �������� �� WB �������� � ������� "������ - WB Partners - ������ �� WB". �� ����� �� ����� ������ ��������� �������� � ��������� ������ �� ������ � WB. ��� �������� ������ ������������ ������ <kbd>��������� �����</kbd>. ����������� ����� ����� ����� ������, ��������� � ���������� ������. � ���� "���������� ��������������" ������������ ������ ����� ���������� � �������� � WB � ��� �����. ��� ��������� �������� ������ ������� ������� ��� �� ���� ������� � ��������.</li>
    <li>� �������� "�������������" ������������� ������ � WB ��������� ������ ���������� �� ������ � ���� ������� ������.</li>
    <li>��� �������������� �������� ������� �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/wbseller/cron/orders.php</code>. �������� ����� ������ ��� �������, ������ ������� ��� ������ � ���������� ������. </li>
    <li>�������������� �������������� �������� ������ �� �������� ������� ������ � �������� �� �������� ������.</li>
  </ol>
  
 <h4>�������� ������� � WB</h4>
   <ol>
    <li>������ ������� ��� �������� �� WB �������� � ������� "������ - WB Partners- ������ �� WB". �� ����� �� �������� ������ ��������� �������� � ��������� ������ �� ������ � WB. ��� �������� ������ ������������ ������ <kbd>��������� �����</kbd>. ��� ��������� �������� ������ ������� ������� ��� �� ���� ������� � ��������. �� WB ���������� ������ �� ������, � ��� ����� ����������� � ��������������.</li>
  </ol>
        </p>';
    
    return $PHPShopGUI->setInfo($Info, 280, '98%');
}
?>
