<?php

/**
 * ������ ���������� �������� ������
 * @param array $row ������ ������
 * @return string 
 */
function tab_pbrf_new($data, $option = false) {
    global $PHPShopGUI, $PHPShopSystem;
    $PHPShopGUI->addJSFiles('/phpshop/modules/pbrf/js/pbrf.js');

    // ���������� ������
    $PHPShopOrder = new PHPShopOrderFunction($data['id']);

    $order = unserialize($data['orders']);

    // ���������� ��������
    $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

    $blank.=$PHPShopGUI->setButton(__('����� �.7'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('�.113/�.117 ������� � ���������� ��������'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma2.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('�������� ������� �.112��'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma3.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('�112�� ��� ����������� �������� ����� ������'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma4.php?orderID=".$data['id']."&datas=".$data['datas']."'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('����������� ������� �.113\��'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma5.php?orderID=".$data['id']."&datas=".$data['datas']."'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('����� �.107'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma6.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('������� �.116'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma7.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");
    $blank.=$PHPShopGUI->setLine(false, 10);
    $blank.=$PHPShopGUI->setButton(__('���������� ���������� CN23'), false, $onclick = "\" onclick=\"DoPrintBig('/phpshop/modules/pbrf/admpanel/forms/forma8.php?orderID=" . $data['id'] ."&datas=".$data['datas']. "'); return false;");


    $disp = '<div id="blank-pbrf">'.$blank.'</div>';

    return $disp;
}

?>
