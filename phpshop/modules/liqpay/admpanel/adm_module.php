<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.liqpay.liqpay_system"));
PHPShopObj::loadClass('order');

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

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������������ ���� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('Merchant ID', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1.=$PHPShopGUI->setField('Merchant SIG', $PHPShopGUI->setInputText(false, 'merchant_sig_new', $data['merchant_sig'], 300));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab1.= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 210));

    $Tab1.=$PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));


    // ����� �����������
    $Tab2 = $PHPShopGUI->setPay(null, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("� ������", $Tab2));

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