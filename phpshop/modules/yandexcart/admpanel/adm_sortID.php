<?php

function addYandexcartSort($data) {
    global $PHPShopGUI;

    $Tab3=$PHPShopGUI->setField('������.������', $PHPShopGUI->setRadio('yandex_param_new', 1, '���������', $data['yandex_param'], false, 'text-warning') .
            $PHPShopGUI->setRadio('yandex_param_new', 2, '��������', $data['yandex_param']),1,'��������� �������������� ��� ������.������');
    
    
    $Tab3.= $PHPShopGUI->setField("������� ���������", $PHPShopGUI->setInputText(null, 'yandex_param_unit_new', $data['yandex_param_unit'], 100));

    $PHPShopGUI->addTab(array("������.������", $Tab3, true));
}

$addHandler = array(
    'actionStart' => 'addYandexcartSort',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>