<?php

function addMoysklad($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField("������� ���", $PHPShopGUI->setInputText(null, 'moysklad_product_id_new', @$data['moysklad_product_id']));
    $PHPShopGUI->addTab(array("��������", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addMoysklad',
    'actionDelete' => false,
    'actionUpdate' => false,
    'actionOptionEdit' => 'addMoysklad',
);
?>