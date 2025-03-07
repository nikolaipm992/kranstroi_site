<?php

function tab_info($data) {
    global $PHPShopGUI;

    $Info = '<p>
        
   <h4>��������� ������</h4>
   <ol>
   <li>����������� "��������������� �����" �� ������� �������� "���������� ���" <a href="https://partner.megamarket.ru/settings/merchants" target="_blank">��������� - ���������� �� API</a> � ����������� ���� �������� ������.</li>
   <li>������� �� ������� "������ YML-�����".</li>
   <li>������� ��������� ���������� ������ (���� � �����).</li>
   <li><kbd>���������</kbd> ��������� ������.</li>
   </ol>


      <h4>��������� ����������</h4>
          <ol>
        <li>� ������ �������� "���������� ���" <a href="https://partner.megamarket.ru/settings/merchants" target="_blank">��������� - ��������</a>, ������� ������ �� �������� ���: <code>https://' . $_SERVER['SERVER_NAME'] . '/yml/?marketplace=megamarket&pas=' . $data['password'] . '</code></li>
        <li>� ������ �������� "���������� ���" <a href="https://partner.megamarket.ru/settings/merchants" target="_blank">��������� - ���������� �� API</a>, �������:
        
      <ul>
         <li>URL ��� ������ �������� ����������� (order/new): <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/megamarket/api.php/' . md5($data['token']) . '/new</code></li>
          <li>URL ��� ������ ������ ����� (order/cancel): <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/megamarket/api.php/' . md5($data['token']) . '/cancel</code></li>
       </ul>
      </li>
        <li>��������� � �������������� ����������� ����� ���/����� � ��������� ������������ ���������� �� API ��� �������� �������, ���������� ��� � ��������.</li>
    </ol>
    


   <h4>�������� ������� � ����������</h4>
   <ol>
    <li>� �������� �������������� ������ � �������� ����� �������� "������ - ����������" �������� ����� <kbd>�������� ������� � ����������</kbd> � ��������� ������.</li>
    <li>����� �������� �������� ��������� ���� ������������ ������ �������� � ������� <a href="https://partner.megamarket.ru/main/catalog/matched" target="_blank">����������� - ������� ������</a> � ������ �������� "���������� ���".</li>
     <li>��� ������ �������� ��� � �������� ������� � ���������� ����������� ������ <kbd>��������� ����</kbd> � ���������� ������. ���� � ������� ��� �� ���������� � ���������� ��� �������������� ������ ������ � �����������.</li>
    <li>��� �������������� �������� ��� � �������� ������� � ���������� �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/megamarket/cron/products.php</code>.</li>
  </ol>
  
  <h4>�������� ������� � ����������</h4>
   <ol>
    <li>����� ������ � ���������� ����� ������������� �������� � ������ �� ����� �� �������� � ���������, ���������� � ��������� ������. 
    <li>�������������� �������������� �������� ������ �� �������� ������� ������ � �������� �� �������� ������.</li>
  </ol>
        </p>';

    return $PHPShopGUI->setInfo($Info, 280, '98%');
}

?>
