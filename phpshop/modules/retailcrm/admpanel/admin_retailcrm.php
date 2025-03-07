<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("�������", "50%"), array("ID ������", "10%"), array("����", "10%"), array("������", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.retailcrm.retailcrm_log"));
    $PHPShopOrm->debug = false;


    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {
            if($row['order_id'] == 0)
                $PHPShopInterface->setRow(
                    array('name' => $row['type'], 'link' => '?path=modules.dir.retailcrm&id=' . $row['id']),
                    '���',
                    PHPShopDate::get($row['date'], true),
                    $row['status']
                );
            else
                $PHPShopInterface->setRow(
                    array('name' => $row['type'], 'link' => '?path=modules.dir.retailcrm&id=' . $row['id']),
                    array('name' => $row['order_id'], 'link' => '?path=order&id=' . $row['order_id']),
                    PHPShopDate::get($row['date'], true),
                    $row['status']
                );
        }
    $PHPShopInterface->Compile();
}

?>