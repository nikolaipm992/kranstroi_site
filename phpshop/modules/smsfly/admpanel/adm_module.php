<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.smsfly.smsfly_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();


    $Tab1 = $PHPShopGUI->setField('� �������� ��� SMS', $PHPShopGUI->setInputArg(array('type' => 'text.required', 'value' => $data['phone'], 'name' => 'phone_new', 'placeholder' => '380631234567', 'size' => 300)));
    $Tab1 .= $PHPShopGUI->setField('������������', $PHPShopGUI->setInputText(false, 'merchant_user_new', $data['merchant_user'], 300));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setInput('password', 'merchant_pwd_new', $data['merchant_pwd'], false, 300));
    $Tab1 .= $PHPShopGUI->setField('����������� (Alfaname)', $PHPShopGUI->setInputText(false, 'alfaname_new', $data['alfaname'], 300));

    // Sandbox
    $sandbox_value[] = array('�������', 1, $data['sandbox']);
    $sandbox_value[] = array('��������', 2, $data['sandbox']);
    $Tab1 .= $PHPShopGUI->setField('�������� �����', $PHPShopGUI->setSelect('sandbox_new', $sandbox_value, 300, true));


    $Tab2 = $PHPShopGUI->setPay();


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("�����������", $Tab1, true), array("� ������", $Tab2));

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