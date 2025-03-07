<?php
/**
 * ������� ������ ������� ��������
 */
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("�������", "50%"), array("����� ������", "10%"), array("����", "10%"), array("������", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.payonline.payonline_log"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    $logArray = array();
    if(!empty($data['id']))
        $logArray[] = $data;
    else
        $logArray = $data;

    foreach ($logArray as $row) {
        $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.payonline&id=' . $row['id']), $row['order_id'], PHPShopDate::get($row['date'], true), $row['status']);
    }
    $PHPShopInterface->Compile();
}