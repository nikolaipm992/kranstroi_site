<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_jurnal"));
$TitlePage = __('�������������� ������ ') . ' #' . $_GET['id'];
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");

// ���������
$PHPShopOrmOption = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_system"));
$option = $PHPShopOrmOption->getOne(array('status'));

/**
 * ��������� ������ ������
 */
function setNum() {
    global $PHPShopBase;

    // ���-�� ������ � ��������� ������ �_XX, �� ��������� 2
    $format = $PHPShopBase->getParam('my.order_prefix_format');
    if (empty($format))
        $format = 2;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $row = $PHPShopOrm->select(array('uid'), false, array('order' => 'id desc'), array('limit' => 1));
    $last = $row['uid'];
    $all_num = explode("-", $last);
    $ferst_num = $all_num[0];
    $order_num = $ferst_num + 1;
    $order_num = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, $format);
    return $order_num;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $option, $select_name;

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

    $PHPShopGUI->setActionPanel(__("������") . ' &#8470;' . $data['id'], $select_name, array('��������� � �������'), false);

    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("date_new", PHPShopDate::dataV($data['date'], false)));
    $Tab1 .= $PHPShopGUI->setField('���: ', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 600), false, 'IP: ' . $data['ip']);
    $Tab1 .= $PHPShopGUI->setField('�������:', $PHPShopGUI->setInputText(false, 'tel_new', $data['tel'], 300));
    $Tab1 .= $PHPShopGUI->setField('����� ������:', $PHPShopGUI->setInputText(null, 'time_start_new', $data['time_start'] . ' ' . $data['time_end'], 300));
    $Tab1 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('message_new', $data['message'], false, 600));


    if (!empty($option['status']))
        $help = $PHPShopGUI->setHelp('������ "' . $OrderStatusArray[$option['status']]['name'] . '" ������� ������ ����� � ������� �������');

    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300) . $help);
    $Tab1 .= $PHPShopGUI->setInput("hidden", "status", $data['status']);


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

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
    global $PHPShopModules, $option;

    if (!empty($_POST['date_new']) and empty($_POST['ajax']))
        $_POST['date_new'] = PHPShopDate::GetUnixTime($_POST['date_new']);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_jurnal"));
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    // ����� �����
    if (!empty($_POST['status_new']) and $_POST['status_new'] == $option['status'] and $_POST['status'] != $option) {

        // �������
        $data_call = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));

        // ������ ������� ������ ��� ��������� �������������� ������
        $PHPShopOrmOrder = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data['fio_new'] = $data_call['name'];
        $data['tel_new'] = $data_call['tel'];
        $data['datas_new'] = time();
        $data['uid_new'] = setNum();
        $data['dop_info_new'] = '������ � ����� �' . $data_call['id'] . ' ' . $data_call['message_new'] . ' ' . $data_call['time_start_new'];
        $id = $PHPShopOrmOrder->insert($data);

        // �������� ������
        $PHPShopOrm->delete(array('id' => '=' . $data_call['id']));

        if (!empty($_POST['ajax']))
            return array('success' => $action);
        else
            header('Location: ?path=order&id=' . $id);
    } elseif (!empty($_POST['ajax']))
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