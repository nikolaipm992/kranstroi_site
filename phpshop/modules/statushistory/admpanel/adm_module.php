<?php


// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $TitlePage, $select_name;
    
    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('�������'));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("� ������", $Tab3));

    return true;
}

// ��������� �������
$PHPShopGUI->getAction();


// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>