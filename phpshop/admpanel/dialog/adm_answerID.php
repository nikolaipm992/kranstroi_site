<?php

$TitlePage = __('�������������� ������').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog_answer']);

// ��������� �����
function setSelectChek($n) {
    $i = 1;
    while ($i <= 10) {
        if ($n == $i)
            $s = "selected";
        else
            $s = "";
        $select[] = array($i, $i, $s);
        $i++;
    }
    return $select;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    $PHPShopGUI->setActionPanel(__("�������������� ������") .": ". $data['name'], array('�������'), array('���������', '��������� � �������'));
    $PHPShopGUI->field_col = 2;

    // �������� 1
    $PHPShopGUI->setEditor('none');
    $oFCKeditor = new Editor('message_new');
    $oFCKeditor->Height = '150';
    $oFCKeditor->Value = $data['message'];

    $Select1 = setSelectChek($data['num']);

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "name_new", $data['name'], "none", 500)) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "��������", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "���������", $data['enabled'])) .
            $PHPShopGUI->setField("��������� � ����", $PHPShopGUI->setRadio("view_new", 1, "��������", $data['view']) . $PHPShopGUI->setRadio("view_new", 2, "���������", $data['view'])) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setSelect("num_new", $Select1, 50));

    $Tab1.=$PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1.= $PHPShopGUI->setField("����������", $oFCKeditor->AddGUI());

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true,false,true));


    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.shopusers.edit");

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

    header('Location: ?path=dialog');
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ����������
    if (is_array($_POST['servers'])){
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and !strstr($v, ','))
                $_POST['servers_new'].="i" . $v . "i";
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>