<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productday.productday_system"));

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
    global $PHPShopModules,$PHPShopOrm;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if($_POST['time_new']>24 or empty($_POST['time_new']))
        $_POST['time_new'] = 24;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}


function actionStart() {
    global $PHPShopGUI,$PHPShopOrm;
    
     //�������
    $data = $PHPShopOrm->select();
    
    $action_value[] = array('������� ����� �� ����� ����� ��������� �����', 1, $data['status']);
    $action_value[] = array('��������� ����� � ����� ����� ��������� �����', 2, $data['status']);
    $action_value[] = array('�������� ����� �� ��������������� �� ���� ����������', 3, $data['status']);
    
    
    $Tab1 =$PHPShopGUI->setField("����� � �����", $PHPShopGUI->setSelect('status_new', $action_value, 400,true));
    $Tab1 .= $PHPShopGUI->setField('��� ��������� �����', $PHPShopGUI->setInputText(false, 'time_new', $data['time'],50),2,'��� � ������� 1-24');
    
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true),array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], true)));

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