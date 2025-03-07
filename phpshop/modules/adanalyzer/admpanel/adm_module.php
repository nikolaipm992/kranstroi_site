<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.adanalyzer.adanalyzer_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('status_new', 'enabled_new');
    
     // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}


// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1=$PHPShopGUI->setField('��������', $PHPShopGUI->setCheckbox('enabled_new', 1, '��������� �������� ��������� �������� � ����������� ���������', $data['enabled']));
    $Tab1.=$PHPShopGUI->setField('������� ���������', $PHPShopGUI->setCheckbox('status_new', 1, '������� UTM-����� ����� ������ � ������������', $data['status']));
    $Tab2 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true),array("��������� ��������", null,'?path=modules.dir.adanalyzer'),array("������", null,'?path=modules.dir.adanalyzer.stat'), array("� ������", $Tab2));

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