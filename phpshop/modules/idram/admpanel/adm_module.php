<?php

include_once dirname(__DIR__) . '/class/Idram.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.idram.idram_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(['version_new' => $new_version]);
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
    
    PHPShopObj::loadClass('order');

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('IdramID', $PHPShopGUI->setInputText(false, 'idram_id_new', $data['idram_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setInputText(false, 'secret_key_new', $data['secret_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('���� ��������� �����', $PHPShopGUI->setSelect('language_new', Idram::getAvailableLanguages($data['language']) , 300));
    $Tab1 .= $PHPShopGUI->setField('������ �� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', Idram::getOrderStatuses($data['status']) , 300));
    $Tab1 .= $PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('payment_description_new', $data['payment_description'], true, 300, 100));
    $Tab1 .= $PHPShopGUI->setField('��������� ��������������� �������� ������', $PHPShopGUI->setTextarea('payment_status_new', $data['payment_status'], true, 300, 100));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version']);

    $info = '<h4>��� ������������ � Idram?</h4>
        <ol>
<li>������������������, ��������� ������� � <a href="https://web.idram.am/new/am" target="_blank">Idram</a>.</li>
<li>���������� <kbd>IdramID</kbd> ������� � ���� <code>IdramID</code> � ���������� ������.</li>
<li>���������� <kbd>Secret Key</kbd> ������� � ���� <code>��������� ����</code> � ���������� ������.</li>
<li>� ������� Idram ������� SUCCESS_URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/success/?status=success</code></li>
<li>� ������� Idram ������� FAIL_URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/success/?status=fail</code></li>
<li>� ������� Idram ������� RESULT_URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/idram/payment/check.php</code></li>
<li>������� ����, �� ������� ����� ������������ ��������� ����� ����������.</li>
<li>��������� ������ ������, ��� ������� ����� �������� ������.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(["��������", $Tab1,true], ["����������", $Tab2], ["� ������", $Tab3]);

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