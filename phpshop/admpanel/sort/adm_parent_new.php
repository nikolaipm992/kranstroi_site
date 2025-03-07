<?php

$TitlePage = __('�������� �������� �������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['parent_name']);

function actionStart() {
    global $PHPShopGUI, $PHPShopModules,$TitlePage;

    // �������
    $data['start_date'] = time();
    $data['end_date'] = time() + 10000000;
    $data['enabled'] = 1;
    $data['day_num'] = 1;
    $data['news_num'] = 3;
    
    $data = $PHPShopGUI->valid($data,'name','color');

    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));

    $Tab1 = $PHPShopGUI->setField("������������ �������", $PHPShopGUI->setInputArg(array('type' => 'text.required', 'name' => "name_new", 'value' => $data['name'], 'placeholder' => '������'))) .
            $PHPShopGUI->setField("������������ �����", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => "color_new", 'value' => $data['color'], 'placeholder' => '����'))) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']));


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true,false,'block-grid'));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.sort.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>
