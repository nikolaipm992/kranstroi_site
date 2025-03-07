<?php

$TitlePage = __("������ ��������");

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("����", "20%"),  array("� ������", "20%"),array("��������", "50%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexcart.yandexcart_log"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->getList(array('*'), $where = false, array('order' => 'id DESC'),array('limit'=>300));

    foreach ($data as $row) {
        $PHPShopInterface->setRow(array('name' => PHPShopDate::get($row['date'], true), 'link' => '?path=modules.dir.yandexcart&id=' . $row['id']), array('name' => $row['order_id'], 'link' => '?path=order&id=' . $row['order_id']), $row['path']);
    }
    $PHPShopInterface->Compile();
}
?>