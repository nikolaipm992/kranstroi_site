<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productcomponents.productcomponents_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    if (empty($_POST['product_search_new']))
        $_POST['product_search_new'] = 0;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������ � ������� ������', $PHPShopGUI->setCheckbox('product_search_new', 1, null, $data['product_search']));

    $Info = '<p>������ ��������� ������������ ���� � ���������� �������� ������ �� ������ ��� �������������.</p>
        <h4>��������� ������</h4>
        <p>��� �������������� ������ �� ������� <kbd>������</kbd> - <kbd>�������������</kbd> ���� ����������� ��������� ������ ������������� ������� � ������.</p>
    <p>��� ��������������� ������� �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/productcomponents/cron/products.php</code>. ������� � ���� �������������� ��� �� ��� �������������� �������� ������ � ��������.';

    // ���������� �������� 2
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Info), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>