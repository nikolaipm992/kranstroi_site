<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));

}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    if (empty($_POST['memory_new']))
        $_POST['memory_new'] = 0;
    
    if (empty($_POST['nowbuy_new']))
        $_POST['nowbuy_new'] = 0;

    if (empty($_POST['referal_new']))
        $_POST['referal_new'] = 0;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    // ����� ������
    $e_value[] = array('�������', 0, $data['enabled']);
    $e_value[] = array('�����', 1, $data['enabled']);
    $e_value[] = array('������', 2, $data['enabled']);
    

    $Tab1 = $PHPShopGUI->setField('��������� �����', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300),1,'��� ������ ����� ��� ������');
    $Tab1.= $PHPShopGUI->setField('������ �������', $PHPShopGUI->setInputText(false, 'day_new', $data['day'],100,__('����')).$PHPShopGUI->setCheckbox('memory_new', 1, '������� ������������� ������� � ����', $data['memory']));
    
    $Tab1.=$PHPShopGUI->setField('������ ��������', $PHPShopGUI->setCheckbox('nowbuy_new', 1, '����� ���������� ������ �� ��������� �������', $data['nowbuy']));
    $Tab1.=$PHPShopGUI->setField('��������', $PHPShopGUI->setCheckbox('referal_new', 1, '��������� �������� �������� � ����������� ���������', $data['referal']));
    $Tab1.=$PHPShopGUI->setField('����� ������', $PHPShopGUI->setSelect('enabled_new', $e_value, 150,true));
    $Tab1.= $PHPShopGUI->setField('�������� �����������', $PHPShopGUI->setInputText(false, 'sendmail_new', $data['sendmail'],100,__('�����')),1,'���������� ����� ��� �������� �� ���');
   

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("��������� �������", null, '?path=modules.dir.visualcart'), array("������ ���������� � �������", null, '?path=modules.dir.visualcart.log'), array("� ������", $Tab3));

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