<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.mobile.mobile_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    if ($action)
        header('Location: ?path=modules&id=' . $_GET['id']);
}

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(['version_new' => $new_version]);
}

// ����� ������� �������
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('message_new', $data['message']), 1, '������ ���� �� ���������� ���� ������� �� �������');

    // ������
    $Tab1 .= $PHPShopGUI->setField('����������� � �����', $PHPShopGUI->setInputText(false, "logo_new", $data['logo']));

    // ���������
    $returncall_value[] = array(__('�������'), 1, $data['returncall']);
    $returncall_value[] = array(__('�������� ������'), 2, $data['returncall']);
    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setSelect('returncall_new', $returncall_value));
    $Tab1 .= $PHPShopGUI->setField("������", GetSkinList($data['skin']));

    $Tab2 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("� ������", $Tab2));

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