<?php

$TitlePage = __('�������� ������ �������');
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
    global $PHPShopGUI, $PHPShopSystem, $TitlePage, $PHPShopModules;

    // �������
    $data['enabled']=$data['view']=1;
    $data['name'] = __('���������');
    
    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));
    $PHPShopGUI->field_col = 2;

   // �������� 1
    $PHPShopGUI->setEditor('none');
    $oFCKeditor = new Editor('message_new');
    $oFCKeditor->Height = '150';
    $oFCKeditor->Value = $data['message'];

    $Select1 = setSelectChek($data['num']);

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
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
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.shopusers.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // ����������
    $_POST['servers_new'] = "";
    if (is_array($_POST['servers']))
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and !strstr($v, ','))
                $_POST['servers_new'].="i" . $v . "i";

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=dialog');
}

// ��������� ������� 
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>