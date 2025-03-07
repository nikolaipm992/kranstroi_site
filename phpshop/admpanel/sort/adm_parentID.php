<?php

$TitlePage = __('�������������� �������� �������').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['parent_name']);

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    $PHPShopGUI->field_col = 4;
    
    $PHPShopGUI->setActionPanel(__("�������� ��������") . '<span class="hidden-xs"> / ' . $data['name'] . '</span>', array('�������'), array('���������', '��������� � �������'));

    $Tab1 = $PHPShopGUI->setField("������������ �������", $PHPShopGUI->setInputArg(array('type' => 'text.required','locale'=>false, 'name' => "name_new", 'value' => $data['name'], 'placeholder' => '������'))) .
            $PHPShopGUI->setField("������������ �����", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => "color_new", 'value' => $data['color'], 'placeholder' => '����'))) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']));


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true,false,'block-grid'));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.sort.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.sort.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.sort.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('enabled_new');

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    
    return array("success" =>  $action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" =>  $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>