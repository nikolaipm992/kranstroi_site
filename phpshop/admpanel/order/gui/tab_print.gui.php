<?php

/**
 * ������ �������� ����
 * @param array $data ������ ������
 * @return string 
 */
function tab_print($data) {
    global $PHPShopGUI;

    $disp = null;

    // ����� ������
    $disp.=$PHPShopGUI->setButton('����� ������', 'print', 'btn-print-order','./order/forms/order.php?orderID=' . $data['id']);

    // �������� ���
    $disp.=$PHPShopGUI->setButton('�������� ���', 'bookmark', 'btn-print-order','./order/forms/receipt.php?orderID=' . $data['id']);

    // ����
    $disp.=$PHPShopGUI->setButton('���� � ����', 'credit-card', 'btn-print-order','../../../phpshop/forms/account/forma.html?orderId='.$data['id'].'&tip=2&datas='.$data['datas']);

    // ���� � ��������
    $disp.=$PHPShopGUI->setButton('��������', 'list-alt', 'btn-print-order','../../../phpshop/forms/receipt/forma.html?orderId='.$data['id'].'&tip=2&datas='.$data['datas']);
    
    // ����-�������
    $disp.=$PHPShopGUI->setButton('����-�������', 'barcode', 'btn-print-order','./order/forms/invoice.php?orderID=' . $data['id'] );
    
    // ����-12
    if($_SESSION['lang'] == 'russian')
    $disp.=$PHPShopGUI->setButton('����-12', 'qrcode', 'btn-print-order','./order/forms/torg-12.php?orderID=' . $data['id']);
    
     // ��������
    $disp.=$PHPShopGUI->setButton('��������', 'briefcase', 'btn-print-order','./order/forms/warranty.php?orderID=' . $data['id']);
    
    // ���
    $disp.=$PHPShopGUI->setButton('���', 'file', 'btn-print-order','./order/forms/act.php?orderID=' . $data['id']);

    return $disp;
}

?>
