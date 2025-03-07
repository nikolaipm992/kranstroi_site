<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.robokassa.robokassa_system"));

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
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������ �� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('������������� ��������', $PHPShopGUI->setInputText(false, 'merchant_login_new', $data['merchant_login'], 300));
    $Tab1 .= $PHPShopGUI->setField('������ #1', $PHPShopGUI->setInputText(false, 'merchant_key_new', $data['merchant_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('������ #2', $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 300));
    $Tab1 .= $PHPShopGUI->setField('����� ����������', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "�������� ������ �� �������� �����", $data["dev_mode"]));

    $merchant_country_value[] = array('������', '������', $data['merchant_country']);
    $merchant_country_value[] = array('���������', '���������', $data['merchant_country']);
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setSelect('merchant_country_new', $merchant_country_value, 300,true));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab1 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1 .= $PHPShopGUI->setField('��������� ��������������� ��������', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $info = '<h4>��������� Robokassa</h4>
       <ol>
        <li>������������������ � <a href="https://partner.robokassa.ru/Reg/Register?PromoCode=01phpshop&culture=ru" target="_blank">Robokassa.ru</a>
        <li>Result Url: <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/robokassa/payment/result.php</code>
        <li>����� ������� ������ �� Result Url: POST
        <li>Success Url: <code>http://' . $_SERVER['SERVER_NAME'] . '/success/</code>
        <li>����� ������� ������ �� Success Url: POST
        <li>Fail Url: <code>http://' . $_SERVER['SERVER_NAME'] . '/fail/</code>
        <li>����� ������� ������ �� Fail Url: POST
        </ol>
        
       <h4>��������� ������</h4>
       <ol>
        <li>"������������� ��������", "������ #1" � "������ #2" (�������� ��� ����������� � Robokassa) ����������� � ����������� ���� �������� ������.
        <li>������ ��� ������� "����� �����" ����� ����������� ����� ����� �������� ������. ��� ��������� ������� ���� ����������� �������� ������ ��� ������ ���������� ������� ��������������� � ������ �������� ����������.
        </ol>
        
        <h4>��������� ��������</h4>
        <ol>
        <li>�������� ������ ��� ��� �������� � ������-����� ����� ��������� � �������� �������������� ��������.
        </ol>
        
        <h4>��������� ������-�����</h4>
        <ol>
        <li>Robokassa ��������� <a href="https://fiscal.robokassa.ru/" target="_blank">������������� ������-����</a> ����������� ����� ������ �� ������ 54-�� �� email. ��� ����� � ������ �������� ����� ������� ������� ������ �� "54-�� - ��������", ����� ���� ������ �������� �������������� ��������� �� �����.
        <li>Robokassa ��������� �������� ��� ������������� ������-�����. ������ ������� � �������� ����� ������������� ��������� ��� �������� ����������� ����� (�.�. ����������� ������������� �������� ������� �� ��� ��������� ����), �� ������������ ����������.
        </ol>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], true)));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>