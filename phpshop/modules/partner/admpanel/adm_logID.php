<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_log"));

// ������� ��������
function actionDelete() {
    global $PHPShopOrm;

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');

// ��������� �������
$PHPShopGUI->getAction();
?>