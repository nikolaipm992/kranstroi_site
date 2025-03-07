<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pechka54.pechka54_log"));

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    if ($data['status'] == 1) {
        $status = '<span class=\'glyphicon glyphicon-ok\'></span> �������';
    } else {
        $status = '<span class=\'glyphicon glyphicon-remove\'></span> �������';
    }


    if (empty($data['fiscal']))
        $data['fiscal'] = $data['id'].' / ������';

    // ������ ���������
    $PHPShopGUI->setActionPanel('����� �' . $data['order_id'] . '/ ' . PHPShopDate::get($data['date'], true) . ' / ��� �' . $data['fiscal'], null, array('�������'));

    // ��������� � �������� ���
    ob_start();
    print_r(unserialize($data['message']));
    $log = ob_get_clean();

    $Tab1 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), "none", false, '500');

    // ����� ����� ��������
    $PHPShopGUI->setTab(array($status . ' �' . $data['fiscal'], $Tab1));


    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


