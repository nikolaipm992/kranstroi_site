<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';
$TitlePage = __('������ �� WB');

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', '../modules/wbseller/admpanel/gui/order.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("&#8470; �������", "7%"), array("�������", "15%"), array("������", "7%"), array("������������", "40%"), array("�����", "10%", array('align' => 'right')));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get((time() - 2592000 / 30), false, true);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get((time() - 1), false, true);

    if (empty($_GET['status']))
        $_GET['status'] = 'new';

    $status = $_GET['status'];

    $WbSeller = new WbSeller();

    // ������
    $orders = $WbSeller->getOrderList($date_start, $date_end, $_GET['status'])['orders'];

    $total = 0;

    if (is_array($orders)) {
        $orders = array_reverse($orders);
        foreach ($orders as $row) {

            // ����� ��� ��������
            if ($WbSeller->checkOrderBase($row['id']))
                continue;

            if ($WbSeller->type == 2) {
                $type_name = __('���');
                $type = 'uid';
            } else {
                $type_name = 'ID';
                $type = 'id';
            }

            // ������ �� ������ � ��
            $prod = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) $row['article'] . '"']);
            // ������ �� ������ �� WB
            if (empty($prod)) {
                $product_info = $WbSeller->getProductList($row['skus'][0],1)['cards'][0];
                $prod['pic_small'] = $product_info['photos'][0]['tm'];
                $prod['uid']=PHPShopString::utf8_win1251($row['vendorCode']);
                $prod['name']=PHPShopString::utf8_win1251($product_info['title']);

                $link='https://www.wildberries.ru/catalog/' . $product_info['nmID'] . '/detail.aspx';
            }
            else {
                $link = '?path=product&id=' . $prod['id'];
            }


            if (!empty($prod['pic_small']))
                $icon = '<img src="' . $prod['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            // �������
            if (!empty($prod['uid']))
                $uid = '<div class="text-muted">' . __('���') . ' ' . $prod['uid'] . '</div>';
            else
                $uid = null;


            $PHPShopInterface->setRow(['name' => $row['id'], 'link' => '?path=modules.dir.wbseller.order&id=' . $row['id'] . '&date_start=' . $date_start . '&date_end=' . $date_end . '&status=' . $status], $WbSeller->getTime($row['createdAt']), $icon, array('name' => $prod['name'], 'addon' => $uid, 'link' => $link,'target'=>'_blank'), round($row['price'] / 100) . $currency);
        }
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
