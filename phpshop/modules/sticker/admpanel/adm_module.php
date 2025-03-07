<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_system"));
$PHPShopOrm->debug = false;

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['editor_new']))
        $_POST['editor_new'] = 0;


    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('���������� ��������', $PHPShopGUI->setCheckbox('editor_new', 1, null, $data['editor']));

    $Info = '<p>��� ������ ������� � ������� ����������� ���������� <kbd>@sticker_������@</kbd>. 
        ������ ����������� � ����������� ���� �������� �������������� �������. 
        ��� ������� ����������� ������ ���� �� ��������� �����.
        </p> 
         <p>
         ��� ���������� ������� � ������ ������ �������� ��������� ��� � ���������� �������� ��� ���������� �����:
        <p>
        <pre>@php
$PHPShopStickerElement = new PHPShopStickerElement();
echo $PHPShopStickerElement->forma("������ �������");
php@</pre>
         </p>';

    $Tab2 = $PHPShopGUI->setInfo($Info);


    // ���������� �������� 2
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3), array("�������", null, '?path=modules.dir.sticker'));

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