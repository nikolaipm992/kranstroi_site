<?php

$TitlePage = __("����������� �������");

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, null, null);
    $PHPShopInterface->checkbox_action = false;
    $format = $PHPShopSystem->getSerilizeParam("admoption.price_znak");
    $PHPShopInterface->setCaption(array("� ������", "20%"), array("�����������", "20%"), array("��������� �������", "20%"), array(__("�����")." " . $PHPShopSystem->getDefaultValutaCode(), "20%", array('align' => 'right','locale'=>false)));

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'uid DESC'), array('limit' => 1000));
    $order_prefix_format = $GLOBALS['SysValue']['my']['order_prefix_format'];
    if (is_array($data))
        foreach ($data as $row) {

            $last_num = substr($row['uid'], -$order_prefix_format);
            $total = strlen($row['uid']);
            $first_num = substr($row['uid'], 0, ($total - $order_prefix_format));
            $uid =  $first_num . "-" . $last_num;

            $PHPShopInterface->setRow(array('link' => '?path=order&uid=' . $uid,'name' => $uid, 'align' => 'left'), PHPShopDate::get($row['datas'], true), $row['name'], array('name' => number_format($row['sum'], $format, '.', ' '), 'align' => 'right'));
        }
    $PHPShopInterface->Compile();
}

?>