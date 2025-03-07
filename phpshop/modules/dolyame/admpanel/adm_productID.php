<?php

function addOptionDolyame($data) {
    global $PHPShopGUI;

    // ����� ������
    $Tab3 = $PHPShopGUI->setField("����� ������", $PHPShopGUI->setCheckbox('dolyame_enabled_new', 1, '��������� ��������', $data['dolyame_enabled']));


    $PHPShopGUI->addTab(array("������", $Tab3, true));
}

function updateOptionDolyame() {
    if (empty($_POST['ajax']) and empty($_POST['dolyame_enabled_new'])) {
        $_POST['dolyame_enabled_new'] = 0;
    }
}

$addHandler = array(
    'actionStart' => 'addOptionDolyame',
    'actionDelete' => false,
    'actionUpdate' => 'updateOptionDolyame'
);
?>