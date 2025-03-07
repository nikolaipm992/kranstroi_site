<?php
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");
// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
$TitlePage = __('�������� �������');

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $TitlePage;

    $PHPShopGUI->field_col = 2;

    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $data['message']=__('�������');

    // ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('�����'), 0, $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#35A6E8\'></span> ' . __('�����') . '"');
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $order_status['color'] . '\'></span> ' . $order_status['name'] . '"');
        }

    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));

    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("date_new", PHPShopDate::dataV($data['date'], false)));
    $Tab1 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('message_new', $data['message'], false, 600));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300) . $help);


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

     // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.order.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;
    
    $_POST['date_new']=time();

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    if(empty($_POST['ajax']))
      header('Location: ?path=' . $_GET['return']);
    else return array('success' =>true,'id'=>$action,'date'=>PHPShopDate::dataV($_POST['date_new'],true));
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>