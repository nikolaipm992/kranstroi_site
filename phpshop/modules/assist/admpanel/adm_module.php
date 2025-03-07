<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.assist.assist_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
     // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������������ ���� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('ID ��������', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 210), 'left');
    $Tab1.=$PHPShopGUI->setField('��������� �����', $PHPShopGUI->setInputText(false, 'merchant_sig_new', $data['merchant_sig'], 210), 'left');
    $Tab1.=$PHPShopGUI->setField('URL', $PHPShopGUI->setInputText(false, 'assist_url_new', $data['assist_url'], 210), 'left');

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab1.= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 210), 'left');

    $Tab1.=$PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));


    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    $info = '��������� ��������� "ID ��������", "��������� �����", "URL" - ����� ��������, ���������� �� Assist-�. <p>������� �������-��� � ���������� �������� ������: <kbd>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/assist/payment/result.php</kbd></p>
';

    $Tab2 = $PHPShopGUI->setInfo($info, 200, '96%');

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

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