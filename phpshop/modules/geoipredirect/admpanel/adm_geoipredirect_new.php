<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.geoipredirect.geoipredirect_city"));


// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage;
    
    $TitlePage=__("�������� ������ ���������������");

    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->setActionPanel(__("�������� ������ ���������������"), false, array('��������� � �������'));
$data['enabled']=1;

    $Tab1= $PHPShopGUI->setField('�����: ', $PHPShopGUI->setInputText(null, 'name_new', null,400,null,null,null,'������'));
    $Tab1.= $PHPShopGUI->setField('����� ���������������: ', $PHPShopGUI->setInputText('http://', 'host_new', null,400,null,null,null,'show'.$_SERVER['SERVER_NAME']));
     $Tab1.=$PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled']));
   
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.servers.create");
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