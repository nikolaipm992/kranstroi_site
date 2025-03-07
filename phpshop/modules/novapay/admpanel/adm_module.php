<?php

include_once dirname(__DIR__) . '/class/NovaPay.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.novapay.novapay_system"));

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

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
    PHPShopObj::loadClass('order');

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Merchant Id', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setTextarea('public_key_new', $data['public_key'], true, 300, 200));
    $Tab1 .= $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setTextarea('private_key_new', $data['private_key'], true, 300, 200));
    $Tab1 .= $PHPShopGUI->setField('������ �� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', NovaPay::getOrderStatuses($data['status']) , 300));
    $Tab1 .= $PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end'], true, 300));
    $Tab1 .= $PHPShopGUI->setField('����� ����������', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "�������� ������ �� �������� ����� NovaPay", $data["dev_mode"]));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version']);

    $info = '<h4>��� ������������ � NovaPay?</h4>
        <ol>
<li>������������������, ��������� ������� � <a href="https://novapay.ua/" target="_blank">NovaPay</a>.</li>
<li>���������� �� NovaPay "Merchant Id", "��������� ����", "��������� ����" ������ � ��������������� ���� � ���������� ������.</li>
<li>��� ������ ���������� ���������� ������������ Merchant Id � ����� ��������� � ������������ NovaPay.</li>
<li>� ������������ �������� ��������, �������� <kbd>������ ������������</kbd> �������� <kbd>���</kbd> "���." � "������������"</li>
<li>� ������������ �������� ��������, �������� <kbd>������ ������������</kbd> �������� <kbd>�������</kbd> "���." � "������������"</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("����������", $Tab2), array("� ������", $Tab3));

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