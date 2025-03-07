<?php

function addOptionProdDay($data) {
    global $PHPShopGUI;

    // ����� ������
    $Tab10 = $PHPShopGUI->setCheckbox('productday_new', 1, '����� ������ ��� �� �����', $data['productday'], $data['sklad']);
    if(!empty($data['sklad']))
       $Tab10 .=  $PHPShopGUI->setHelp('������ �������� - ����� ��� �����');

    $PHPShopGUI->addTab(array("����� ���", $Tab10, true));
}

function updateOptionProdDay($data) {

    if (empty($_POST['ajax'])) {
        if (empty($_POST['productday_new'])) {
            $_POST['productday_new'] = 0;
            $_POST['productday_time_new'] = 0;
        }

        $_POST['datas_new'] = time();
    }
}

$addHandler = array(
    'actionStart' => 'addOptionProdDay',
    'actionDelete' => false,
    'actionUpdate' => 'updateOptionProdDay'
);
?>