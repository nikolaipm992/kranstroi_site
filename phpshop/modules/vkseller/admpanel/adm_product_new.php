<?php

function addVksellerProductTab($data) {
    global $PHPShopGUI;

    // ������� ��� ����������� ������
    $data['export_vk'] = 0;

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_vk_new', 1, '�������� ������� � VK', $data['export_vk']));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }
    $tab .= $PHPShopGUI->setField('���� VK', $PHPShopGUI->setInputText(null, 'price_vk_new', $data['price_vk'], 150, $valuta_def_name), 2);

    $PHPShopGUI->addTab(array("���������", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addVksellerProductTab',
);
?>