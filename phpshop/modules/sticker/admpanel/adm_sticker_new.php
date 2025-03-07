<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_forms"));

// ����� ������� �������
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    $value[] = array(__('�� �������'), '', '');

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

// ������� ������
function actionInsert() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopModules;

    $PHPShopOrmOption = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_system"));
    $option = $PHPShopOrmOption->select();


    // �������
    $data['name'] = '����� ������';
    $data['enabled'] = 1;
    $PHPShopGUI->field_col = 3;

    // �������� 
    if (empty($option['editor']))
        $editor = 'ace';
    else
        $editor = $PHPShopSystem->getSerilizeParam("admoption.editor");

    $PHPShopGUI->setEditor($editor, true);
    $oFCKeditor = new Editor('content_new', true);
    $oFCKeditor->Height = '320';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['content'];

    $Tab1 = $PHPShopGUI->setCollapse('����������', $oFCKeditor->AddGUI());

    $Tab_info = $PHPShopGUI->setField('��������:', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], '100%'));
    $Tab_info .= $PHPShopGUI->setField('������:', $PHPShopGUI->setInputText('@sticker_', 'path_new', $data['path'], '100%', '@'));
    $Tab_info .= $PHPShopGUI->setField('�����:', $PHPShopGUI->setCheckbox('enabled_new', 1, '����� �� �����', $data['enabled']));
    $Tab_info .= $PHPShopGUI->setField('�������� � ���������:', $PHPShopGUI->setInputText(false, 'dir_new', $data['dir']) . $PHPShopGUI->setHelp('������: /page/about.html,/page/company.html'));
    $Tab_info .= $PHPShopGUI->setField('������', GetSkinList($data['skin']));

    $Tab1 .= $PHPShopGUI->setCollapse('����������', $Tab_info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", false, false, false, "actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>