<?php

function addYandexkassa($data) {
    global $PHPShopGUI;

    $vat[] = array(__('�����'), 0, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���'), 1, $data['yandex_vat_code']);
    $vat[] = array(__('��� �� ������ 0%'), 2, $data['yandex_vat_code']);
    $vat[] = array(__('��� �� ������ 10%'), 3, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���� �� ������ 20%'), 4, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���� �� ��������� ������ 10/110'), 5, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���� �� ��������� ������ 20/120'), 6, $data['yandex_vat_code']);

    $Tab3 .= $PHPShopGUI->setField('���', $PHPShopGUI->setSelect('yandex_vat_code_new', $vat,'100%'));

    $PHPShopGUI->addTab(array("�Kassa", $Tab3, true));
}

function addYandexkassaOptions($data) {
    global $PHPShopGUI;

    $PHPShopGUI->field_col = 5;
    
    $vat[] = array(__('�����'), 0, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���'), 1, $data['yandex_vat_code']);
    $vat[] = array(__('��� �� ������ 0%'), 2, $data['yandex_vat_code']);
    $vat[] = array(__('��� �� ������ 10%'), 3, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���� �� ������ 20%'), 4, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���� �� ��������� ������ 10/110'), 5, $data['yandex_vat_code']);
    $vat[] = array(__('��� ���� �� ��������� ������ 20/120'), 6, $data['yandex_vat_code']);

    $Tab3 .= $PHPShopGUI->setField('���', $PHPShopGUI->setSelect('yandex_vat_code_new', $vat,'100%',false, false, false, false, 1, false, false,  'form-control hidden-edit'));

    $PHPShopGUI->addTab(array("�Kassa", $Tab3, true));
}

$addHandler = array(
    'actionStart' => 'addYandexkassa',
    'actionDelete' => false,
    'actionUpdate' => false,
    'actionOptionEdit' => 'addYandexkassaOptions'
);
?>