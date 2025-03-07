<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortselection.sortselection_system"));

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

    $_POST['sort_new'] = serialize($_POST['sort_new']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

/**
 * ����� ��������������
 */
function getSortValue($category, $sort) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = false;

    $sort = unserialize($sort);

    $data = $PHPShopOrm->select(array('*'), array('category' => "='" . $category . "'"), array('order' => 'num'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if (@in_array($row['id'], $sort))
                $sel = 'selected';
            else
                $sel = false;

            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('sort_new[]', $value, 300, false, false, true, false, 1, true);
}

/**
 * ����� ������ ��������������
 */
function getSort($n) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('category' => "='0'"), array('order' => 'num'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if ($n == $row['id'])
                $sel = 'selected';
            else
                $sel = false;

            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('sort_categories_new', $value, 300);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $value[] = array('����������� ����', 1, $data['enabled']);
    $value[] = array('����', 2, $data['enabled']);
    
    $f_value[] = array('������ �� ������� ��������', 1, $data['flag']);
    $f_value[] = array('�����', 2, $data['flag']);


    $Tab1 = $PHPShopGUI->setField('���������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], '100%'));
    $Tab1 .= $PHPShopGUI->setField('������ �������������', getSort($data['sort_categories']));
    
    if(!empty($data['sort_categories']))
    $Tab1 .= $PHPShopGUI->setField('��������������', getSortValue($data['sort_categories'], $data['sort']));
    
    $Tab1 .= $PHPShopGUI->setField('������ ������', $PHPShopGUI->setSelect('enabled_new', $value, 300, true));
    $Tab1.=$PHPShopGUI->setField('����� ������', $PHPShopGUI->setSelect('flag_new', $f_value, 300,true));

    $info = '��� ������� �������� ������� � ������ ������ �������� ����������
        <kbd>@sortselection@</kbd> � ���� ������� �������� <code>phpshop/templates/���_�������/main/index.tpl</code> ������ �������.
        <p>��� �������������� ����� ������ �������������� ������� <code>phpshop/modules/sortselection/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

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