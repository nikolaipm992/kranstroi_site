<?php
/**
 * ������� ������ ������� ��������
 */
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("�������", "50%"), array("����� ������", "10%"), array("����", "10%"), array("������", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.fondy.fondy_log"));
    $PHPShopOrm->debug = false;


    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.fondy&id=' . $row['id']), array('name' => $row['order_id'], 'link' => '?path=order&id=' . $row['order_id']), PHPShopDate::get($row['date'], true), $row['status']);
        }
    $PHPShopInterface->Compile();
}