<?php

function addPbkreditTab($data) {
    global $PHPShopGUI;

    $Tab1 = $PHPShopGUI->setField('��������� ������� � ������', $PHPShopGUI->setCheckbox('pbkredit_disabled_new', 1, '��������� ����� ������ "������ � ������"', $data['pbkredit_disabled']));

    $PHPShopGUI->addTab(array("������ ����� ����", $Tab1, true));
}

function savePbkreditTab($data) {
    if (empty($_POST['ajax']))
        if (!isset($data['pbkredit_disabled_new'])) {
            $_POST['pbkredit_disabled_new'] = '0';
        }
}

$addHandler = array('actionStart' => 'addPbkreditTab', 'actionDelete' => false, 'actionUpdate' => 'savePbkreditTab');
?>