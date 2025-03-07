<?php

function addOptionKVKPromo($data) {
    global $PHPShopGUI;

    // ����� ������
    $Tab3 = $PHPShopGUI->setField("����� ������", $PHPShopGUI->setCheckbox('kvk_enabled_new', 1, '������ ��������', $data['kvk_enabled']));
    $Tab3 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "kvk_promo_new", $data['kvk_promo'], "left"), 1, '����������� � ������, ���� �� ����� ���������������� ����� (��������, ���������).');


    $PHPShopGUI->addTab(array("������", $Tab3, true));
}

function updateOptionKVKPromo($data) {
    if (empty($_POST['ajax']) and empty($_POST['kvk_enabled_new'])) {
        $_POST['kvk_enabled_new'] = 0;
    }
}

$addHandler = array(
    'actionStart' => 'addOptionKVKPromo',
    'actionDelete' => false,
    'actionUpdate' => 'updateOptionKVKPromo'
);
?>