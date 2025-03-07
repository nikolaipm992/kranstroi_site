<?php
/**
 * Функция вывода истории платежей
 */
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Функция", "50%"), array("Номер заказа", "10%"), array("Дата", "10%"), array("Статус", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sberbankrf.sberbankrf_log"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->getList(array('*'), $where = false, array('order' => 'id DESC'));

    foreach ($data as $row) {
        $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.sberbankrf&id=' . $row['id']), array('name' => $row['order_id'], 'link' => '?path=order&id=' . $row['order_id']), PHPShopDate::get($row['date'], true), $row['status']);
    }
    $PHPShopInterface->Compile();
}