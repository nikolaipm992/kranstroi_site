<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("�����", "40%"), array("����� ������", "15%"), array("����", "15%"), array("������", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pochta.pochta_log"));

    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->getList(array('*'), false, array('order' => 'id DESC'));

    foreach ($data as $row) {
        $PHPShopInterface->setRow(
            array('name' => $row['method'], 'link' => '?path=modules.dir.pochta&id=' . $row['id']),
            $row['order_uid'],
            PHPShopDate::get($row['date'], true),
            $row['status']
        );
    }

    $PHPShopInterface->Compile();
}