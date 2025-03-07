<?php

$TitlePage = __('�������������� ������').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    $PHPShopGUI->field_col = 2;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->setActionPanel(__("�������������� ������").": " . $data['name'], array('�������'), array('���������', '��������� � �������'));

    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, "name_new", $data['name'], 300));
    $Tab1 .= $PHPShopGUI->setField("�����������", $PHPShopGUI->setInputText(null, "code_new", $data['code'], 300));
    $Tab1 .= $PHPShopGUI->setField("ISO", $PHPShopGUI->setInputText(null, "iso_new", $data['iso'], 300),1,'��� ������ �� ��������� ISO (USD,RUB,UAH). ���� ������� RUR ��� RUB - �� ����� ���������� �� ������ �����. ���� ���� ������, �� ������ ��������� �� ���� �����������');
    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setInputText(null, "kurs_new", $data['kurs'], 300),1,'�������� ���� ������������ ����� ($ = 0.015)');
    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(null, "num_new", $data['num'], 50));
    $Tab1.=$PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled']));
    
    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.currency.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.currency.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.currency.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    
    $sidebarright[] = array('title' => '����� ����� ������', 'content' => $PHPShopGUI->loadLib('tab_currency', $data, './system/'));
    $PHPShopGUI->setSidebarRight($sidebarright, 2);
    
    return true;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" =>  $action);
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

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" =>  $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
