<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avito.avito_log"));

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage,$select_name;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $_GET['id']));
    
    // ������ ���������
    $PHPShopGUI->setActionPanel($TitlePage. ' &#8470;'.$data['id'], $select_name, array('�������'));

    // ��������� � �������� ���
    ob_start();
    print_r(unserialize($data['message']));
    $log = ob_get_clean();
    
    

    $Tab1 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), "none", false, '450');

    // ����� ����� ��������
    $PHPShopGUI->setTab(array($data['path'], $Tab1, 370));


    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>


