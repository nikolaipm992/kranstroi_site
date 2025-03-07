<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("�������", "50%"), array("ID", "15%"), array("����", "10%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_log"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 300));

    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['order_id']))
                $row['order_id']=null;
        
            $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.vkseller&id=' . $row['id']), array('name' => $row['order_id'], ), PHPShopDate::get($row['date'], true));
        }
    $PHPShopInterface->Compile();
}