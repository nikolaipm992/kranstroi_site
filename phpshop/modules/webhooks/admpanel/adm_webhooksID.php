<?php

PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.webhooks.webhooks_forms"));

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$_classPath;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['create_order_status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['create_order_status']);


    // �����
    $send_value[] = array('POST', 0, $data['send']);
    $send_value[] = array('GET', 1, $data['send']);

    // ��������
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks();
    $type_array=$PHPShopWebhooks->getType();
    foreach($type_array as $k=>$type)
         $type_value[]=array($type,$k,$data['type']);


    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText('', 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setRadio("enabled_new", 1, "����������", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "������", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("URL WebHook", $PHPShopGUI->setInputText(false, 'url_new', $data['url'], 400));
    //$Tab1 .= $PHPShopGUI->setField('�������� ��� �������:', $PHPShopGUI->setSelect('create_order_status_new', $order_status_value, 400));
    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('type_new', $type_value, 400));
    $Tab1 .= $PHPShopGUI->setField('����� ��������', $PHPShopGUI->setSelect('send_new', $send_value, 100));

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true),array("������ ����������", null, '?path=modules.dir.webhooks.log&uid='.$data['id']));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success'=>$action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>