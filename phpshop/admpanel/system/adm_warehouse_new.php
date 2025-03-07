<?php

$TitlePage = __('�������� ������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules;

    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));

    // �������
    $data['name'] = __('����� �����');
    $data['enabled'] = 1;
    $data['num'] = 1;
    $data = $PHPShopGUI->valid($data, 'uid', 'description', 'servers');

    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("������� ���", $PHPShopGUI->setInputText(null, "uid_new", $data['uid']), 2, '��� ������ � 1�');
    $Tab1 .= $PHPShopGUI->setField("�������� �� �����", $PHPShopGUI->setInputText(null, "description_new", $data['description']));
    $Tab1.=$PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("����������", $PHPShopGUI->setInputText('�', "num_new", $data['num'], 100));

    // �������
    $Tab1.=$PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));
    
    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true,false,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.servers.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and !strstr($v, ','))
                $_POST['servers_new'].="i" . $v . "i";
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    // ������� ���� �������� ������
    $PHPShopOrm->query('ALTER TABLE `phpshop_products` ADD `items' . $action . '` int(11) default "0"');

    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>
