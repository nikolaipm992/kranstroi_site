<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.admlog.admlog_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
}


// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1=$PHPShopGUI->setField("����� ���������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled']));

    // ���������� �������� 2
    $Tab2 = $PHPShopGUI->setPay(false, true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("� ������", $Tab2),array("������ �������", 0,'?path=modules.dir.admlog'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}
// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');


?>