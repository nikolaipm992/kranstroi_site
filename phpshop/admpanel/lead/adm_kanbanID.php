<?php

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");
// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
$TitlePage = __('�������������� ������� ') . ' #' . $_GET['id'];

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage;

    $PHPShopGUI->field_col = 2;

    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('�����'), 0, $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#35A6E8\'></span> ' . __('�����') . '"');
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $order_status['color'] . '\'></span> ' . $order_status['name'] . '"');
        }

    $PHPShopGUI->setActionPanel($TitlePage, array('�������'), array('��������� � �������'));

    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("date_new", PHPShopDate::dataV($data['date'], false)));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInput("text", "name_new", $data['name'],null, 300));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->setInput("text", "tel_new", $data['tel'], null,300));
    $Tab1 .= $PHPShopGUI->setField("Email", $PHPShopGUI->setInput("email", "mail_new", $data['mail'],null, 300));
    $Tab1 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('message_new', $data['message'], false, 600));
    $Tab1 .= $PHPShopGUI->setField('����������', $PHPShopGUI->setTextarea('content_new', $data['content'], false, 600,200));
    $Tab1 .= $PHPShopGUI->setInput("hidden", "status", $data['status']);


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.order.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.order.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.order.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {
    global $PHPShopOrm;

    if (!empty($_POST['date_new']) and empty($_POST['ajax']))
        $_POST['date_new'] = PHPShopDate::GetUnixTime($_POST['date_new']);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    if (!empty($_POST['ajax']))
        return array('success' => $action);
    elseif (!empty($_GET['return']))
        header('Location: ?path=' . $_GET['return']);
    else
        header('Location: ?path=' . $_GET['path']);
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