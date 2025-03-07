<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.example.example_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select();

    // ���������� �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $editor = new Editor('example_new');
    $editor->Height = 300;
    $editor->Value = $data['example'];
    $Tab1 = $editor->AddGUI();
    
    $Tab1.=$PHPShopGUI->setHelp('��������� ���������� �������� � <a href="/example/" target="_blank">/example/</a>');

    $Tab1.=$PHPShopGUI->setCollapse('������������',$PHPShopGUI->setLink('http://doc.phpshop.ru', 'PhpDoc', _blank, false, false, 'btn btn-default btn-sm') . ' '.$PHPShopGUI->setLink('https://docs.phpshop.ru', __('�������'), _blank, false, false, 'btn btn-default btn-sm'). ' '.$PHPShopGUI->setLink('http://getbootstrap.com', 'Bootstrap', '_blank', false, false, 'btn btn-default btn-sm'). ' '.$PHPShopGUI->setLink('http://jquery.com', 'jQuery', '_blank', false, false, 'btn btn-default btn-sm'));

    // ���������� �������� 2
    $Tab2 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1), array("� ������", $Tab2));

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>


