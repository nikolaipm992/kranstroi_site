<?php

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

/**
 * ����� ������
 */
function actionInsert() {
    global $PHPShopModules, $PHPShopOrm;
    
    $_POST['name_value'] = html_entity_decode($_POST['name_value']);
    
    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST,'_value');

    return array('success'=>$action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>