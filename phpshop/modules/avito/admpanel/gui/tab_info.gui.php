<?php

function tab_info($data) {
    global $PHPShopGUI, $Avito;

    $Info = '<p>
      <h4>�������� ������� � ����� ����� YML-����:</h4>
        <ol>
            <li>
                ��� ���������: <code>' . $Avito->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/modules/avito/xml/all.php</code>   
            </li>     
            <li>
                ������� �����������: <code>' . $Avito->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/modules/avito/xml/appliances.php</code>   
            </li> 
            <li>
                ��� ���� � ����: <code>' . $Avito->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/modules/avito/xml/home.php</code>   
            </li> 
            <li>
                �������� � ����������: <code>' . $Avito->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/modules/avito/xml/spare.php</code>   
            </li> 
        </ol>

    <h4>��������� ������</h4>
    <ol>
        <li>������������������ � <a href="https://www.avito.ru/business" target="_blank">Avito</a>.</li>
        <li>� ������ �������� Avito ������� <a href="https://www.avito.ru/professionals/api" target="_blank">��������� - API �����</a>, ������� ����� API ����. ���������� ����������� �������� ����� � ���� <kbd>Client Secret</kbd> �  �������� Client Id � ���� <kbd>Client ID</kbd> � ���������� ������.
        </li>
        <li>���� ������ <b>��������</b> �� ������������������� ����� ��������. ��� ������������� ������ ������ �� ���� XML ������ ��� <code>' . $Avito->ssl . $_SERVER['SERVER_NAME'] . '/phpshop/modules/avito/xml/all.php?pas=' . $data['password'] . '</code>.</li>
        <li>��� ����������� � ��������� �������� ������ ������ �� ����� � Avito ������������ ���������� <code>@avito_link@</code></li>
    </ol>

       <h4>���������� ��� � ��������</h4>
        <ol>
        <li>���������� �������� � ����� ��� ������������� API ����� YML-����: <code>' . $Avito->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/modules/avito/xml/stock.php</code></li>
        <li>��� �������������� �������� ��� � �������� ������� � ����� �� ���������� ����� API ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/avito/cron/products.php</code>. ������� � ���� ����������� ��� �� ��� �������������� �������� ������ � ��������.</li>
        <li>��� ���������� ��� � �������� �� API ������ ���� ��������� � ������ ���� <kbd>����� ID</kbd>.</li>
        </ol>


      <h4>��������� ���������</h4>
        <ol>
        <li>� ��������� ������� ������� <kbd>�����</kbd> ��������� ��������� ������ ��������� � �����.</li>
        <li>"��������� ������" - �������, ����� ��������� � ����� ������������� ��������� � ��������-��������.</li>
        <li>��������� ����� "��� ������".</li>
        </ol>
      <h4>��������� ������</h4>
        <ol>
        <li>� �������� �������������� ������ � �������� <kbd>�����</kbd> ��������� �������������� ��������� ������ ��� �����.</li>
        <li>�������� ����� "�������� ������� � �����".</li>
        <li>��������� ���� "�������� ������", ���� �� ��������� - ����� �������������� �������� ������.</li>
        <li>��������� ����� "��������� ������".</li>
        <li>��������� ����� "������� �������� ����������".</li>
        <li>��������� ����� "������� ������".</li>
        </ol>
        

   <h4>�������� ������� � �����</h4>
   <ol>
     <li>API �������� ������ � ����� ��������� ��� �������. ����� � ��������, ����� ������� ��������� �������� � <a href="https://www.avito.ru/general/dostavka/" target="_blank">��������� �����</a></li>
    <li>������ ������� ��� �������� �� ����� �������� � ������� "������ - Avito - ������ �� Avito". �� ����� �� ����� ������ ��������� �������� � ��������� ������ �� ������ � Avito. ��� �������� ������ ������������ ������ <kbd>��������� �����</kbd>. ����������� ����� ����� ����� ������, ��������� � ���������� ������. � ���� "���������� ��������������" ������������ ������ ����� ���������� � �������� � Avito � ��� �����. ��� ��������� �������� ������ ������� ������� ��� �� ���� ������� � ��������.</li>
    <li>� �������� "�������������" ������������� ������ � Avito ��������� ������ ���������� �� ������ � ���� ������� ������.</li>
    <li>��� �������������� �������� ������� �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/avito/cron/orders.php</code>. �������� ����� ������ ��� �������, ������ ������� ��� ������ � ���������� ������. </li>
    <li>�������������� �������������� �������� ������ �� �������� ������� ������ � �������� �� �������� ������.</li>
  </ol>  
  
 <h4>�������� ������� � �����</h4>
   <ol>
    <li>������ ������� ��� �������� �� ����� �������� � ������� "������ - Avito - ������ �� Avito". �� ����� �� �������� ������ ��������� �������� � ��������� ������ �� ������ � Avito. ��� �������� ������ ������������ ������ <kbd>��������� �����</kbd>. ��� ��������� �������� ������ ������� ������� ��� �� ���� ������� � ��������.</li>
  </ol>


        </p>';

    return $PHPShopGUI->setInfo($Info, 280, '98%');
}

?>
