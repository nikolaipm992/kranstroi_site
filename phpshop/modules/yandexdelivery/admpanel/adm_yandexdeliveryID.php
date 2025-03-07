<?php

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI,$PHPShopModules;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexdelivery.yandexdelivery_log"));

    // �������
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . intval($_GET['id'])]);
    $PHPShopGUI->setActionPanel('������ �� ' . PHPShopDate::get($data['date']), false, ['�������']);

    // ��������� � �������� ���
    ob_start();
    print_r(unserialize($data['message']));
    $log = ob_get_clean();

    $Tab1 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '99%', $height = '550');

    // ����� ����� ��������
    $PHPShopGUI->setTab(["��������� ����������", $Tab1]);

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


