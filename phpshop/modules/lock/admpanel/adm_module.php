<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.lock.lock_system"));

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

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
    $PHPShopOrm->clean();
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();


    $e_value[] = array('����', 1, $data['flag']);
    $e_value[] = array('���', 2, $data['flag']);

    $e_adm_value[] = array('����', 1, $data['flag_admin']);
    $e_adm_value[] = array('���', 2, $data['flag_admin']);

    $Tab1 = $PHPShopGUI->setField('����������� �� �����', $PHPShopGUI->setSelect('flag_new', $e_value, 200, true));
    $Tab1 .= $PHPShopGUI->setField('����������� � �������', $PHPShopGUI->setSelect('flag_admin_new', $e_adm_value, 200, true));
    $Tab1 .= $PHPShopGUI->setField('����������', $PHPShopGUI->setInput('text.required', "login_new", $data['login'], false, 200));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setInput("password.required", "password_new", $data['password'], null,200, false, false, false, false, '<a href="#" class="password-view"  data-toggle="tooltip" data-placement="top" title="' . __('�������� ������') . '"><span class="glyphicon glyphicon-eye-open"></span></a>'));
    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("� ������", $Tab3,));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>