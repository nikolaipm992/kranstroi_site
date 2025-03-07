<?php

function addOzonsellerProductTab($data) {
    global $PHPShopGUI;

    // ������� ��� ����������� ������
    $data['price_ozon'] = $data['barcode_ozon'] = $data['sku_ozon'] = $data['export_ozon_id'] = 0;


    // ������ �������� ����
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_ozon_new', 1, '�������� ������� � OZON', $data['export_ozon']));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab .= $PHPShopGUI->setField('���� OZON', $PHPShopGUI->setInputText(null, 'price_ozon_new', $data['price_ozon'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, 'barcode_ozon_new', $data['barcode_ozon'], 150));
    $tab .= $PHPShopGUI->setField("SKU OZON", $PHPShopGUI->setInputText(null, 'sku_ozon_new', $data['sku_ozon'], 150), 1, '������������ ��� ������ �� ����� � OZON');


    if (!empty($data['export_ozon']))
        $tab .= $PHPShopGUI->setField('OZON ID', $PHPShopGUI->setInputText(null, 'export_ozon_id_new', $data['export_ozon_id'], 150), 1, '������������ ��� ���������� ������ � OZON');

    $PHPShopGUI->addTab(array("OZON", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addOzonsellerProductTab',
);
?>