<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';
$TitlePage = __('������ �� ���������');

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', '../modules/vkseller/admpanel/gui/vkseller.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("&#8470; ������", "15%"),array("������", "15%"), array("����", "15%"), array("����������", "20%"), array("�������", "15%"),  array("�����", "10%", array('align' => 'right')));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get((time() - 2592000), false, true);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get((time() - 1), false, true);

    if (empty($_GET['status']))
        $_GET['status'] = 'new';

    $VkSeller = new VkSeller();

    // ������
    if ($VkSeller->model == 'API') {
    $orders = $VkSeller->getOrderList($date_start, $date_end)['response']['items'];
    }

    $total = 0;
    
    $status_array=['����� �����','���������������','����������','������������','��������','�������','�������'];

    if (is_array($orders))
        foreach ($orders as $row) {
        
            // ������ ����� ������
            if($_GET['status'] == 'new' and $row['status'] != 0)
                continue;

            // ����� ��� ��������
            if ($VkSeller->checkOrderBase($row['id']))
               continue;


            $PHPShopInterface->setRow(['name' => $row['id'], 'link' => '?path=modules.dir.vkseller.order&id=' . $row['id'] . '&user=' . $row['user_id']], __($status_array[$row['status']]),PHPShopDate::get($row['date'],true), PHPShopString::utf8_win1251($row['recipient']['name']),PHPShopString::utf8_win1251($row['recipient']['phone'],true),  ['name'=>number_format(round($row['total_price']['amount'] / 100), 0, '', ' ') . $currency, 'link' => '?path=modules.dir.vkseller.order&id=' . $row['id'] . '&user=' . $row['user_id'],'align'=>'right']);
        }

    $order_status_value[] = array(__('����� ������'), 'new', $_GET['status']);
    $order_status_value[] = array(__('��� ������'), 'all', $_GET['status']);


    $searchforma = $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, '���� ������ ������');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, '���� ����� ������');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setSelect('status', $order_status_value, '100%');
    $searchforma .= $PHPShopInterface->setButton('��������', 'search', 'btn-order-search pull-right');

    if (isset($_GET['date_start']))
        $searchforma .= $PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left');
    else
        $searchforma .= $PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel hide pull-left');


    // ������ �������
    if ($total > 0) {
        $stat = '<div class="order-stat-container">' . __('�����:') . ' <b>' . number_format($total, 2, ',', ' ') . '</b> ' . $currency . '<br>' . __('����������:') . ' <b>' . count($orders) . '</b> ' . __('��.');
        $sidebarright[] = array('title' => '����������', 'content' => $stat);
    }

    $sidebarright[] = array('title' => '��������', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
