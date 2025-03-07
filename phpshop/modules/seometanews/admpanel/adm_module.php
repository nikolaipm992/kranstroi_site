<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seometanews.seometanews_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $TitlePage, $select_name, $PHPShopOrm;

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('��������� � �������'));
    
    // �������
    $data = $PHPShopOrm->select();
    
    
    $Tab1 = '<hr>'.$PHPShopGUI->setField("Title �������:", $PHPShopGUI->setTextArea("title_new", $data['title']),1,'��� '.$_SERVER['SERVER_NAME'].'/news/');
    $Tab1.=$PHPShopGUI->setField("Description �������:", $PHPShopGUI->setTextArea("description_new", $data['description']),1,'��� '.$_SERVER['SERVER_NAME'].'/news/');
    
        $Tab1.=$PHPShopGUI->setField("Keywords �������:", $PHPShopGUI->setTextArea("keywords_new", $data['keywords']),1,'��� '.$_SERVER['SERVER_NAME'].'/news/');

    // ����� �����������
    $Tab2 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1),array("� ������", $Tab2));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", 1) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>