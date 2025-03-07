<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortproduct.sortproduct_forms"));

function checkName($name) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $data = $PHPShopOrm->select(array('*'), array('name' => '="' . $name . '"'), false, array('limit' => 1));
    if (!empty($data['id']))
        return $data['id'];
}

function checkId($id) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $id), false, array('limit' => 1));
    if (!empty($data['name']))
        return $data['name'];
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    // �������� ������� ��������������
    if (is_numeric($_POST['value_name_new'])) {
        $_POST['value_id_new'] = $_POST['value_name_new'];
        $_POST['value_name_new'] = checkId($_POST['value_name_new']);
    } else {
        $_POST['value_id_new'] = checkName($_POST['value_name_new']);
    }


    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success'=>$action);
}

/**
 * ����� ��������������
 */
function getSortValue($n) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('filtr' => "='1'", 'goodoption' => "!='1'"), array('order' => 'num'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if ($n == $row['id'])
                $sel = 'selected';
            else
                $sel = false;

            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('sort_new', $value, 300);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));


    $Tab1 = $PHPShopGUI->setField('�������:', $PHPShopGUI->setInputText(null, 'num_new', $data['num'], '100'));
    $Tab1.=$PHPShopGUI->setField('���������� ������:', $PHPShopGUI->setInputText(null, 'items_new', $data['items'], '100'));
    $Tab1.=$PHPShopGUI->setField('������:', $PHPShopGUI->setCheckbox('enabled_new', 1, '��������', $data['enabled']));
    $Tab1.=$PHPShopGUI->setField('��������������', getSortValue($data['sort']));
    $Tab1.=$PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'value_name_new', $data['value_name'], 300) . $PHPShopGUI->setHelp(__('������� �������� ��� ID ���������').' <a href="?path=sort"><span class="glyphicon glyphicon-share-alt"></span>'.__('��������������').'</a>',false,false));
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.modules.edit");

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

// ������� ��������
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();


// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>