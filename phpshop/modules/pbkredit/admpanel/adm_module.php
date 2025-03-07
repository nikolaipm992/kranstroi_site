<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbkredit.pbkredit_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 3;

    $Tab1 = $PHPShopGUI->setField('������������� �������� �����', $PHPShopGUI->setInputText(false, 'tt_code_new', $data['tt_code'],300));
    $Tab1.=$PHPShopGUI->setField('����� ������ ������ ������', $PHPShopGUI->setInputText(false, 'tt_name_new', $data['tt_name'], 300));

    $info = '<h4>��������� ������</h4>
       <ol>
        <li>��������� ������� � <a href="https://www.pochtabank.ru" target="_blank">����� ����</a>.
        <li>������ ������������� �������� ����� ���������� �� �����.</li>
        <li>��������� ���� "����� ������ ������ ������".</li>
        <li>��� ������ ������ ������� � ������ �������� ���������� <kbd>@pbkreditUid@</kbd> � ���� ������ ������� <mark>phpshop/templates/���_�������/product/main_product_forma_full.tpl</mark>.</li>
        <li>������ ������ ������ � ������ ������������� � ����� <mark>phpshop/modules/pbkredit/templates/template.tpl</mark></li>
        </ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], false)));

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