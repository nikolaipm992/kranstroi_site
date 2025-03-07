<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortproduct.sortproduct_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();


    $e_value[] = array('�� ��������', 0, $data['enabled']);
    $e_value[] = array('�����', 1, $data['enabled']);
    $e_value[] = array('������', 2, $data['enabled']);


    $Tab1 = $PHPShopGUI->setField('���������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300));
    $Tab1.=$PHPShopGUI->setField('����� ������', $PHPShopGUI->setSelect('enabled_new', $e_value, 150,true));

    $info = '��� ������������ ������� �������� ������� ������� ������� ������ "�� ��������" � � ������ ������ �������� ����������
        <kbd>@brandproduct@</kbd> � ���� ������.
        <p>��� �������������� ����� ������ �������������� ������� <code>phpshop/modules/sortproduct/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("����������", $Tab2), array("� ������", $Tab3),array("��������", null,'?path=modules.dir.sortproduct'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();


// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>