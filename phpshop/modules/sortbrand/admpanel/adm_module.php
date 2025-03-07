<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortbrand.sortbrand_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
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

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $e_value[] = array('�� ��������', 0, $data['enabled']);
    $e_value[] = array('�����', 1, $data['enabled']);
    $e_value[] = array('������', 2, $data['enabled']);

    $f_value[] = array('���������� ������', 1, $data['flag']);
    $f_value[] = array('������', 2, $data['flag']);


    $Tab1 = $PHPShopGUI->setField('���������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300));
    $Tab1.=$PHPShopGUI->setField('��������������', getSortValue($data['sort']));
    $Tab1.=$PHPShopGUI->setField('����� ������', $PHPShopGUI->setSelect('enabled_new', $e_value, 300,true));
    $Tab1.=$PHPShopGUI->setField('������ ������', $PHPShopGUI->setSelect('flag_new', $f_value, 300,true));

    $info = '��� ������������ ������� �������� ������� ������� �������� ������ "�� ��������" � � ������ ������ �������� ����������
        <kbd>@brand@</kbd> � ���� ������.
        <p>��� �������������� ����� ������ �������������� ������� <code>phpshop/modules/sortbrand/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>