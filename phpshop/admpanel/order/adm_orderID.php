<?php

PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("bonus");
PHPShopObj::loadClass("string");

$TitlePage = __('�������������� ������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$PHPShopDelivery = new PHPShopDelivery();
$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

/**
 * �������� �������
 */
function updateBonus($data) {
    global $PHPShopOrm, $PHPShopOrderStatusArray;

    // ������� �������
    $GetOrderStatusArray = $PHPShopOrderStatusArray->getArray();

    // ������
    $PHPShopBonus = new PHPShopBonus($data['user']);

    // ���������� � ���������� �������
    if ($data['statusi'] != $_POST['statusi_new'] and ! empty($GetOrderStatusArray[$_POST['statusi_new']]['cumulative_action'])) {
        $PHPShopBonus->updateUserBonus(0, $data['bonus_plus']);
        $PHPShopBonus->updateBonusLog($data['id'], $data['uid'], 0, $data['bonus_plus']);
    }
    // ���� ����� ������ �����������
    else if ($_POST['statusi_new'] == 1) {
        $PHPShopOrm->update(array('bonus_plus_new' => 0, 'bonus_minus_new' => 0), array('id' => '=' . intval($_POST['rowID'])));
    }
    /*
      // ���� ����� ������ �����������, � ��� ������ �� ����� �����, �� �� �� ���������, � ��������� �������
      else if ($data['statusi'] != 0 && $_POST['statusi_new'] == 1) {
      $PHPShopBonus->updateUserBonus($data['bonus_plus'],0);
      $PHPShopBonus->updateBonusLog($data['uid'], $data['bonus_plus'],0);
      } */
}

/**
 * ���������� ������
 */
function updateDiscount($data) {
    global $link_db, $PHPShopOrderStatusArray;

    // ������� �������
    $GetOrderStatusArray = $PHPShopOrderStatusArray->getArray();

    if ($GetOrderStatusArray[$_POST['statusi_new']]['cumulative_action'] == 1) {

        // ������ ������� ������������
        $sql_st = "SELECT * FROM `" . $GLOBALS['SysValue']['base']['shopusers'] . "` WHERE `id` =" . intval($data['user']) . " ";
        $query_st = mysqli_query($link_db, $sql_st);
        $row_st = mysqli_fetch_array($query_st);
        $status_user = $row_st['status'];

        // ������ ��������� ������� ������������ ������
        $sql_d = "SELECT * FROM `" . $GLOBALS['SysValue']['base']['shopusers_status'] . "` WHERE `id` =" . intval($status_user) . " ";
        $query_d = mysqli_query($link_db, $sql_d);
        $row_d = mysqli_fetch_array($query_d);
        $cumulative_array = unserialize(@$row_d['cumulative_discount']);
        $cumulative_array_check = @$row_d['cumulative_discount_check'];
        if ($cumulative_array_check == 1) {

            // ������ �������
            $sql_order = "SELECT " . $GLOBALS['SysValue']['base']['orders'] . ".* FROM `" . $GLOBALS['SysValue']['base']['orders'] . "`
            LEFT JOIN `" . $GLOBALS['SysValue']['base']['order_status'] . "` ON " . $GLOBALS['SysValue']['base']['orders'] . ".statusi=" . $GLOBALS['SysValue']['base']['order_status'] . ".id
            WHERE " . $GLOBALS['SysValue']['base']['orders'] . ".user =  " . $data['user'] . "
            AND " . $GLOBALS['SysValue']['base']['order_status'] . ".cumulative_action='1' ";
            $query_order = mysqli_query($link_db, $sql_order);
            $row_order = mysqli_fetch_array($query_order);
            $sum = '0'; // ������� �����
            do {
                $orders = unserialize($row_order['orders']);
                $sum += $orders['Cart']['sum'];
            } while ($row_order = mysqli_fetch_array($query_order));

            // ������ ������
            $q_cumulative_discount = '0'; // ������� ������
            foreach ($cumulative_array as $key => $value) {
                if ($sum >= $value['cumulative_sum_ot'] and $sum <= $value['cumulative_sum_do']) {
                    $q_cumulative_discount = $value['cumulative_discount'];
                    break;
                }
            }
            // ��������� ������
            mysqli_query($link_db, "UPDATE  `" . $GLOBALS['SysValue']['base']['shopusers'] . "` SET `cumulative_discount` =  '" . $q_cumulative_discount . "' WHERE `id` =" . intval($data['user']));
        } else {
            mysqli_query($link_db, "UPDATE  `" . $GLOBALS['SysValue']['base']['shopusers'] . "` SET `cumulative_discount` =  '0' WHERE `id` =" . intval($data['user']));
        }
    }
}

/**
 * ����� �������� ���� ��������������
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem, $PHPShopBase, $PHPShopOrderStatusArray;

    // �������
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])), false, array('limit' => 1));

    // ���������
    if ($PHPShopSystem->ifSerilizeParam('admoption.dadata_enabled')) {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $PHPShopGUI->addJSFiles('./js/jquery.suggestions_utf.min.js', './order/gui/dadata.gui.js');
        else
            $PHPShopGUI->addJSFiles('./js/jquery.suggestions.min.js', './order/gui/dadata.gui.js');

        $PHPShopGUI->addCSSFiles('./css/suggestions.min.css');
    }

    $PHPShopGUI->addJSFiles('./order/gui/order.gui.js');

    // ������.�����
    $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
    if (empty($yandex_apikey))
        $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

    if (strlen($data['street']) > 5)
        $PHPShopGUI->addJSFiles('//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU&apikey=' . $yandex_apikey);


    $PHPShopGUI->action_select['��� ������ ������������'] = array(
        'name' => '��� ������ ������������',
        'action' => 'order-list',
        'url' => '?path=' . $_GET['path'] . '&where[a.user]=' . $data['user']
    );

    $PHPShopGUI->action_select['����� �� �������'] = array(
        'name' => '����� �� �������',
        'action' => 'order-list',
        'url' => '?path=report.statorder&where[a.user]=' . $data['user'] . '&date_start=01-01-2010&date_end=' . PHPShopDate::get()
    );

    $PHPShopGUI->action_select['�����������'] = array(
        'name' => '����������� �� ������',
        'action' => 'order-reminder',
    );

    $PHPShopGUI->action_select['����� �����'] = array(
        'name' => '����� ����� ������������',
        'locale' => true,
        'url' => '?path=' . $_GET['path'] . '&action=new&cart=false&id=' . $_GET['id'],
        'action' => $GLOBALS['isFrame']
    );


    // ���������� ������
    $PHPShopOrder = new PHPShopOrderFunction($data['id'], $data);

    $update_date = $PHPShopOrder->getStatusTime();
    if (!empty($update_date))
        $update_date = ' / ' . __('�������') . ': ' . $update_date;

    // ���� �����
    if ($PHPShopOrder->default_valuta_iso == 'RUB' or $PHPShopOrder->default_valuta_iso == 'RUR')
        $currency = ' <span class=rubznak>p</span>';
    else
        $currency = $PHPShopOrder->default_valuta_iso;

    $PHPShopGUI->setActionPanel(__("�����") . ' &#8470; ' . $data['uid'] . ' <span class="hidden-xs hidden-md">/ ' . PHPShopDate::dataV($data['datas']) . $update_date . ' / ' . __("�����") . ': ' . $PHPShopOrder->getTotal(false, ' ') . $currency . '</span>', array('������� �����', '����� �����', '��� ������ ������������', '�����������', '|', '�������'), array('���������', '��������� � �������'), false);

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $order = unserialize($data['orders']);
    $status = unserialize($data['status']);

    $PHPShopUser = new PHPShopUser($data['user']);

    $house = $porch = $flat = null;
    if (!empty($data['house']))
        $house = ', ' . __('�.') . ' ' . $data['house'];

    if (!empty($data['porch']))
        $porch = ', ' . __('���.') . ' ' . $data['porch'];

    if (!empty($data['flat']))
        $flat = ', ' . __('��.') . ' ' . $data['flat'];

    if (empty($data['fio']) and ! empty($order['Person']['name_person']))
        $data['fio'] = $order['Person']['name_person'];
    elseif (empty($data['fio']))
        $data['fio'] = $PHPShopUser->getParam('name');
    if (empty($data['tel']))
        $data['tel'] = $PHPShopUser->getParam('tel');

    $mail = $PHPShopUser->getParam('login');
    if (empty($mail))
        $mail = $order['Person']['mail'];

    // ���������� � ����������
    $sidebarleft[] = array('id' => 'user-data-1', 'title' => '���������� � ����������', 'name' => array('caption' => $data['fio'], 'link' => '?path=shopusers&return=order.' . $data['id'] . '&id=' . $data['user']), 'content' => array(array('caption' => $mail, 'link' => 'mailto:' . $order['Person']['mail']), $data['tel']));

    // ����� ��������
    $sidebarleft[] = array('id' => 'user-data-2', 'title' => '����� ��������', 'name' => PHPShopSecurity::TotalClean($data['fio']), 'content' => array(PHPShopSecurity::TotalClean($data['tel'], 6), PHPShopSecurity::TotalClean($data['street'] . $house . $porch . $flat)));

    // �����
    if ($PHPShopSystem->ifSerilizeParam('admoption.yandexmap_enabled')) {
        if (strlen($data['street']) > 5) {
            $map = '<div id="map" class="visible-lg" data-geocode="' . PHPShopSecurity::TotalClean($data['city'] . ', ' . $data['street'] . ' ' . $data['house']) . '" data-title="' . __('�����') . ' &#8470;' . $data['uid'] . '"></div><div class="data-row"><a href="http://maps.yandex.ru/?&source=wizgeo&text=' . urlencode(PHPShopString::win_utf8(PHPShopSecurity::TotalClean($data['city'] . ', ' . $data['street'] . ' ' . $data['house']))) . '" target="_blank" class="text-muted"><span class="glyphicon glyphicon-map-marker"></span>' . __('��������� �����') . '</a></div>';
            $sidebarleft[] = array('title' => '����� �������� �� �����', 'content' => array($map));
        }
    }

    // �������
    if (!empty($data['servers'])) {
        $PHPShopServerOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
        $data_server = $PHPShopServerOrm->select(array('*'), array('enabled' => "='1'", 'id' => '=' . $data['servers']), false, array('limit' => 1));
        $server = PHPShopString::check_idna($data_server['host'], true);
        $sidebarleft[] = array('id' => 'user-data-1', 'title' => '����� �������', 'name' => null, 'content' => array(array('caption' => $server, 'link' => 'http://' . $data_server['host'])));
    }

    // ����� �������
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2, true);

    // ������� �������
    PHPShopObj::loadClass('order');

    // �������� ������� �������
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['statusi'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:#35A6E8\'></span> ' . __('����� �����') . '"');
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['statusi'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $order_status['color'] . '\'></span> ' . $order_status['name'] . '"');
        }

    // ������ ������
    $status_dropdown = $PHPShopGUI->setSelect('statusi_new', $order_status_value, '100%');
    $sidebarright[] = array('title' => '������ ������', 'content' => $status_dropdown);

    // ����� ��������� ������
    if (empty($status['time']))
        $status['time'] = PHPShopDate::dataV($data['datas'], true, false, ' ', true);

    // �������� ���� �����
    $PHPShopPaymentArray = new PHPShopPaymentArray();
    $PaymentArray = $PHPShopPaymentArray->getArray();

    if (is_array($PaymentArray))
        foreach ($PaymentArray as $payment) {

            // ������� ������������
            if (strpos($payment['name'], '.')) {
                $name = explode(".", $payment['name']);
                $payment['name'] = $name[0];
            }

            $payment = $PHPShopGUI->valid($payment, 'color');

            $payment_value[] = array($payment['name'], $payment['id'], $order['Person']['order_metod'], 'data-content="<span class=\'glyphicon glyphicon-text-background\' style=\'color:' . $payment['color'] . '\'></span> ' . $payment['name'] . '"');
        }

    // ��� ������
    $payment_dropdown = $PHPShopGUI->setSelect('person[order_metod]', $payment_value, '100%');

    // ���������� �� ������
    $sidebarright[] = array('title' => '���������� �� ������', 'content' => $payment_dropdown);

    // �������� ������
    $Tab_print = $PHPShopGUI->loadLib('tab_print', $data);

    // ��������
    $PHPShopDeliveryArray = new PHPShopDeliveryArray();

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray))
        foreach ($DeliveryArray as $delivery) {

            // ������� ������������
            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            if ($delivery['is_folder'] != 1)
                $delivery_value[] = array($delivery['city'], $delivery['id'], $order['Person']['dostavka_metod'], 'data-subtext="' . $delivery['price'] . ' ' . $currency . '"');
        }

    $delivery_value[] = array(null, 'div', 'ider', 'data-divider="true"');
    $delivery_value[] = array(__('�������� ��������� ��������'), 0, 1);

    $delivery_content[] = $PHPShopGUI->setSelect('person[dostavka_metod]', $delivery_value, '100%');

    $sidebarright[] = array('title' => '���������� � ��������', 'content' => $delivery_content);

    // �����
    if ($PHPShopBase->Rule->CheckedRules('order', 'rule')) {
        $PHPShopOrmAdmin = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data_admin = $PHPShopOrmAdmin->select(array('*'), array('enabled' => "='1'"), array('order' => 'name'), array('limit' => 300));

        $admin_value[] = array(__('�� �������'), 0, $data['admin']);
        if (is_array($data_admin))
            foreach ($data_admin as $row) {
                if (empty($row['name']))
                    $row['name'] = $row['login'];
                $admin_value[] = array($row['name'], $row['id'], $data['admin']);
            }

        $sidebarright[] = array('title' => '����������', 'content' => $PHPShopGUI->setSelect('admin_new', $admin_value, '100%'));
    }

    // ����������� ����
    $PHPShopCompany = new PHPShopCompanyArray();
    $PHPShopCompanyArray = $PHPShopCompany->getArray();
    $company_value[] = array($PHPShopSystem->getSerilizeParam("bank.org_name"), 0, $data['company']);
    if (is_array($PHPShopCompanyArray))
        foreach ($PHPShopCompanyArray as $company)
            $company_value[] = array($company['name'], $company['id'], $data['company']);

    if (is_array($PHPShopCompanyArray))
        $sidebarright[] = array('title' => '����������� ����', 'content' => $PHPShopGUI->setSelect('company_new', $company_value, '100%'));


    $sidebarright[] = array('title' => '�������� ������', 'content' => $Tab_print, 'idelement' => 'letterheads');

    // �������
    $Tab2 = $PHPShopGUI->loadLib('tab_cart', $data);

    // ������ ����������
    $Tab3 = $PHPShopGUI->loadLib('tab_userdata', $data, false, $order);

    // ��� ������ ������������
    if (!empty($data['user']))
        $Tab4 = $PHPShopGUI->loadLib('tab_userorders', $data, false, array('status' => $OrderStatusArray, 'currency' => $currency, 'color' => $OrderStatusArray));

    // �����
    $Tab5 = $PHPShopGUI->loadLib('tab_files', $data, false, $order);

    // ������
    $Tab6 = $PHPShopGUI->loadLib('tab_bonus', $data);

    // ������
    $Tab7 = $PHPShopGUI->loadLib('tab_dialog', $data);

    // ������ �������
    $PHPShopGUI->setSidebarRight($sidebarright);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ������
    if (!empty($data['bonus_minus']) or ! empty($data['bonus_plus']))
        $PHPShopGUI->addTabSeparate(array("������ <span class=badge>+" . $data['bonus_plus'] . "</span>", $Tab6, true));

    // ������
    $dialog = $PHPShopBase->getNumRows('dialog', "where isview='0' and staffid='1' and user_id='" . $data['user'] . "' group by chat_id");

    if ($dialog > 99)
        $dialog = 99;

    if (empty($dialog))
        $dialog_enabled = $PHPShopBase->getNumRows('dialog', "where user_id='" . $data['user'] . "' group by chat_id");
    else
        $dialog_enabled = true;

    if (!empty($dialog_enabled))
        $PHPShopGUI->addTabSeparate(array("������ <span class=badge>" . $dialog . "</span>", $Tab7, true, 'dialog'));


    // ����� ����� ��������
    if (!empty($data['user']))
    $PHPShopGUI->setTab(array("�������", $PHPShopGUI->setCollapse(null, $Tab2)), array("������ ����������", $PHPShopGUI->setCollapse(null, $Tab3)), array("������ ������������", $PHPShopGUI->setCollapse(null, $Tab4)), array("���������", $PHPShopGUI->setCollapse(null, $Tab5)));
    else $PHPShopGUI->setTab(array("�������", $PHPShopGUI->setCollapse(null, $Tab2)), array("������ ����������", $PHPShopGUI->setCollapse(null, $Tab3)));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.order.remove") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.order.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.order.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    if (!empty($_GET['return']))
        header('Location: ?path=' . $_GET['return']);
    else
        header('Location: ?path=' . $_GET['path']);
}

/**
 * ����� ����������
 * @return bool
 */
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    // ������ �� ������
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));
    $order = unserialize($data['orders']);

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ��������� �� ����� ������
    if (!empty($_POST['person']) and is_array($_POST['person'])) {

        // ����� ������
        if (is_array($_POST['person']))
            foreach ($_POST['person'] as $k => $v)
                $order['Person'][$k] = $v;

        // ��������
        $PHPShopCart = new PHPShopCart($order['Cart']['cart']);

        if (empty($order['Cart']['delivery_free'])) {
            $PHPShopDelivery = new PHPShopDelivery($_POST['person']['dostavka_metod']);
            $PHPShopDelivery->checkMod($order['Cart']['dostavka']);
            $order['Cart']['dostavka'] = $PHPShopDelivery->getPrice($PHPShopCart->getSum(false), $PHPShopCart->getWeight());
        }

        // ���������� ������
        $PHPShopOrder = new PHPShopOrderFunction(false, $order['Cart']['cart']);

        // ����������� � ����� ���������
        $_POST['status']['time'] = PHPShopDate::dataV();
        $_POST['status_new'] = serialize($_POST['status']);

        // �������� csv �� ������������
        if (!empty($_FILES['file']['name'])) {
            $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
            if ($_FILES['file']['ext'] == "csv") {
                if (@move_uploaded_file($_FILES['file']['tmp_name'], "csv/" . $_FILES['file']['name'])) {
                    $csv_file = "csv/" . $_FILES['file']['name'];

                    PHPShopObj::loadClass('readcsv');
                    $PHPShopReadCsvNative = new PHPShopReadCsvNative($csv_file);

                    if (is_array($PHPShopReadCsvNative->CsvToArray)) {
                        $PHPShopCart->clean();
                        foreach ($PHPShopReadCsvNative->CsvToArray as $product) {
                            $PHPShopProduct = new PHPShopProduct($product[0], 'uid');
                            $id = $PHPShopProduct->getParam('id');
                            $PHPShopCart->add($id, $product[2]);

                            // ����
                            $PHPShopCart->_CART[$id]['price'] = $product[1];
                        }
                        $order['Cart']['cart'] = $PHPShopCart->_CART;
                    }
                }
            }
        }

        // ���������� ������ � ����������
        $sum = $sum_promo = 0;
        if (is_array($PHPShopCart->_CART))
            foreach ($PHPShopCart->_CART as $val) {

                // ����� ������� � �������
                if (!empty($val['promo_price'])) {
                    $sum_promo += $val['num'] * $val['price'];
                }
                // ����� ������� ��� �����
                else
                    $sum += $val['num'] * $val['price'];
            }

        // ������
        if (!$PHPShopSystem->ifSerilizeParam('admoption.auto_discount_disabled')) {
            $discount = $PHPShopOrder->ChekDiscount($sum, null, true);
            if ($order['Person']['discount'] > $discount)
                $discount = $order['Person']['discount'];
        }

        $order['Person']['discount'] = $discount;

        // ����� ������ �� �����
        $order['Cart']['sum'] = $PHPShopOrder->returnSumma($sum_promo);

        // ����� ������ ��� �����
        $order['Cart']['sum'] += $PHPShopOrder->returnSumma($sum, $order['Person']['discount']);

        // ����� � ������ �������
        $order['Cart']['sum'] -= $data['bonus_minus'];

        // ������������ ������ ������
        $_POST['orders_new'] = serialize($order);

        // �����
        if (isset($_POST['editID'])) {
            if (is_array($_POST['files_new'])) {
                foreach ($_POST['files_new'] as $k => $files)
                    $files_new[$k] = @array_map("urldecode", $files);

                $_POST['files_new'] = serialize($files_new);
            } else
                $_POST['files_new'] = [];
        } else
            $_POST['files_new'] = serialize($_POST['files_new']);

        // �����
        $_POST['sum_new'] = $order['Cart']['sum'] + $order['Cart']['dostavka'];
    }
    // ������ ����� �������
    else {

        // ����������� � ����� ���������
        $status = unserialize($data['status']);
        $status['time'] = PHPShopDate::dataV();
        $_POST['status_new'] = serialize($status);
    }

    $_POST['date_new'] = time();
    $PHPShopOrm->clean();

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . intval($_POST['rowID'])));

    // ���������� ������������ � ����� ������� � �������� �� ������
    if ($data['statusi'] != $_POST['statusi_new']) {
        $PHPShopOrderFunction = new PHPShopOrderFunction((int) $_POST['rowID']);
        $PHPShopOrderFunction->changeStatus((int) $_POST['statusi_new'], (int) $data['statusi']);
    }

    // ������������ ������
    updateDiscount($data);

    // ������
    updateBonus($data);

    return array('success' => $action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopBase;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // �������� ���� �� ��������
    if ($PHPShopBase->Rule->CheckedRules('order', 'remove')) {

        $PHPShopOrm->debug = false;
        $action = $PHPShopOrm->delete(array('id' => '=' . intval($_POST['rowID'])));
    } else
        $action = false;

    return array('success' => $action);
}

/**
 * ����� �������������� ������� �� ���������� ����
 */
function actionValueEdit() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    // �� ������
    $orderID = intval($_REQUEST['id']);

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $orderID), false, array('limit' => 1));

    $order = unserialize($data['orders']);

    // �� ������
    $productID = urldecode($_REQUEST['selectID']);

    if (empty($order['Cart']['cart'][$productID])) {
        foreach ($order['Cart']['cart'] as $key => $val)
            if ($val['id'] == $productID) {
                $productID = $key;
            }
    }

    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('��������', $PHPShopGUI->setInputArg(array('name' => 'name_value', 'type' => 'text.required', 'value' => $order['Cart']['cart'][$productID]['name'])));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('����������', $PHPShopGUI->setInputArg(array('name' => 'num_value', 'type' => 'text', 'value' => $order['Cart']['cart'][$productID]['num'], 'size' => 100)));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('����', $PHPShopGUI->setInputArg(array('name' => 'price_value', 'type' => 'text', 'value' => $order['Cart']['cart'][$productID]['price'], 'size' => 150, 'description' => $PHPShopSystem->getDefaultValutaCode())));

    $PHPShopGUI->_CODE .= $PHPShopGUI->setInputArg(array('name' => 'rowID', 'type' => 'hidden', 'value' => $productID));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setInputArg(array('name' => 'orderID', 'type' => 'hidden', 'value' => $orderID));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setInputArg(array('name' => 'parentID', 'type' => 'hidden', 'value' => $_REQUEST['parentID']));

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

/**
 * ����� ���������� ������� �� ���������� ����
 */
function actionCartUpdate() {
    global $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    // �� ������
    $orderID = intval($_REQUEST['id']);

    // �� ������
    $productID = PHPShopString::utf8_win1251($_REQUEST['selectID']);

    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $orderID), false, array('limit' => 1));
    if (is_array($data)) {
        $order = unserialize($data['orders']);
        $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

        // ���������� �������
        $PHPShopCart = new PHPShopCart($order['Cart']['cart']);

        // �������� � ��������
        switch ($_POST['selectAction']) {

            // ���������� ������
            case "discount":

                $order['Person']['discount'] = floatval($_REQUEST['selectID']);
                break;

            case "changeDeliveryCost":
                $PHPShopDelivery->setMod(2);
                $deliveryCost = (float) $_REQUEST['selectID'];
                break;

            // �������� ������ �� �������
            case "delete":
                unset($order['Cart']['cart'][$productID]);
                break;

            // ���������� ������
            case "add":

                // ���������� ������ �� ID
                if (!empty($productID)) {


                    // ����������
                    if (empty($_SESSION['selectCart'][$productID])) {

                        // ��������� ����� ����� 1 �� �� ID
                        if ($PHPShopCart->add($productID, abs($_REQUEST['selectNum']))) {

                            // ���������� ������ ���������� �������
                            $order['Cart']['cart'] = $PHPShopCart->getArray();
                            $order['Cart']['num'] = $PHPShopCart->getNum();
                            $order['Cart']['sum'] = $PHPShopCart->getSum(false);
                        }
                    }

                    // �������������� ���-��
                    else {

                        if ($_SESSION['selectCart'][$productID]['num'] != abs($_REQUEST['selectNum'])) {

                            $PHPShopCart->edit($productID, abs($_REQUEST['selectNum']));

                            // ���������� ������ ���������� �������
                            $order['Cart']['cart'] = $PHPShopCart->getArray();
                            $order['Cart']['num'] = $PHPShopCart->getNum();
                            $order['Cart']['sum'] = $PHPShopCart->getSum(false);
                        }
                    }
                }
                break;

            // ���������� ���� � ���-��
            default:

                $_POST['selectAction'] = 'productUpdate';

                // ��� ������
                if (!empty($_POST['name_value']))
                    $order['Cart']['cart'][$productID]['name'] = $_POST['name_value'];

                // ����������
                if (!empty($_POST['num_value']))
                    $order['Cart']['cart'][$productID]['num'] = $_POST['num_value'];

                // ����
                if (!empty($_POST['price_value']))
                    $order['Cart']['cart'][$productID]['price'] = $_POST['price_value'];
        }

        $PHPShopOrder = new PHPShopOrderFunction(false, $order['Cart']['cart']);


        // ���������� �������
        $PHPShopCart = new PHPShopCart($order['Cart']['cart']);

        // ���������� ������ � ����������
        $sum = $sum_promo = 0;
        if (is_array($PHPShopCart->_CART))
            foreach ($PHPShopCart->_CART as $val) {

                // ����� ������� � �������
                if (!empty($val['promo_price'])) {
                    $sum_promo += $val['num'] * $val['price'];
                }
                // ����� ������� ��� �����
                else
                    $sum += $val['num'] * $val['price'];
            }

        // ������
        if (!$PHPShopSystem->ifSerilizeParam('admoption.auto_discount_disabled')) {
            $discount = $PHPShopOrder->ChekDiscount($sum, null, true);
            if ($order['Person']['discount'] > $discount)
                $discount = $order['Person']['discount'];

            $order['Person']['discount'] = $discount;
        }

        // ����� ������ �� �����
        $order['Cart']['sum'] = $PHPShopOrder->returnSumma($sum_promo);

        // ����� ������ ��� �����
        $order['Cart']['sum'] += $PHPShopOrder->returnSumma($sum, $order['Person']['discount']);

        // ����� � ������ �������
        $order['Cart']['sum'] -= $data['bonus_minus'];

        $order['Cart']['num'] = $PHPShopCart->getNum();
        $order['Cart']['weight'] = $PHPShopCart->getWeight();

        if (empty($order['Cart']['num']))
            $order['Person']['discount'] = 0;

        if (empty($order['Cart']['delivery_free'])) {
            if (isset($deliveryCost)) {
                $order['Cart']['dostavka'] = $deliveryCost;
            } else {
                $PHPShopDelivery->checkMod($order['Cart']['dostavka']);
                $order['Cart']['dostavka'] = $PHPShopDelivery->getPrice($PHPShopCart->getSum(false), $PHPShopCart->getWeight());
            }
        }

        // ������������ ������ ������
        $update['orders_new'] = serialize($order);
        $update['sum_new'] = $order['Cart']['sum'] + $order['Cart']['dostavka'];
        $PHPShopOrm->clean();
        $PHPShopCart->clean();

        $action = $PHPShopOrm->update($update, array('id' => '=' . $orderID));

        // �������� ������
        $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, array('old' => $data, 'new' => $update, 'title' => $_POST['selectAction']));

        return array('success' => $action);
    }
}

/**
 * ����� ����������� �� ������
 */
function actionReminder() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;

    // ������ �� ������
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $order = unserialize($data['orders']);

    // ������ �� ������������
    $PHPShopUser = new PHPShopUser($data['user']);

    // ������ �� �������
    $GLOBALS['PHPShopOrder'] = new PHPShopOrderFunction($data['id'], $data);
    $PHPShopCart = new PHPShopCart($order['Cart']['cart']);

    $currency = $PHPShopSystem->getDefaultValutaCode(true);
    $rate = 1;

    PHPShopParser::set('sum', $order['Cart']['sum']);
    PHPShopParser::set('serverShop', PHPShopString::check_idna($_SERVER['SERVER_NAME'], true));
    PHPShopParser::set('serverPath', PHPShopString::check_idna($_SERVER['SERVER_NAME'], true));
    PHPShopParser::set('cart', $PHPShopCart->display('mailcartforma', array('currency' => $currency, 'rate' => $rate)));
    PHPShopParser::set('currency', $currency);
    PHPShopParser::set('deliveryPrice', $order['Cart']['dostavka']);
    PHPShopParser::set('total', $data['sum']);
    PHPShopParser::set('shop_name', $PHPShopSystem->getName());
    PHPShopParser::set('ouid', $data['uid']);
    PHPShopParser::set('date', PHPShopDate::get($data['datas']));
    PHPShopParser::set('mail', $PHPShopUser->getParam("mail"));
    PHPShopParser::set('company', $PHPShopSystem->getParam('name'));
    PHPShopParser::set('user_name', $PHPShopUser->getParam("name"));

    PHPShopParser::set('shopName', $PHPShopSystem->getValue('company'));
    PHPShopParser::set('adminMail', $PHPShopSystem->getEmail());
    PHPShopParser::set('telNum', $PHPShopSystem->getValue('tel'));
    PHPShopParser::set('logo', $PHPShopSystem->getLogo());

    // ��������� ������ ����������
    $title = __('����������� �� ������������ ������') . ' �' . $data['uid'];

    (new PHPShopMail($PHPShopUser->getParam("mail"), $PHPShopSystem->getEmail(), $title, '', true, true))->sendMailNow(PHPShopParser::file('tpl/reminder.mail.tpl', true, false));

    return array('success' => true);
}

/**
 * ������ ������ ������� �������
 */
function mailcartforma($val, $option) {
    global $PHPShopModules, $PHPShopOrder;

    if (empty($val['name']))
        return true;

    // �������� ������
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, array(&$val), $option);
    if ($hook)
        return $hook;

    // �������
    if (!empty($val['parent_uid']))
        $val['uid'] = $val['parent_uid'];

    if (empty($val['ed_izm']))
        $val['ed_izm'] = __('��.');

    $val['price'] *= $option['rate'];

    $price = number_format($val['price'], $PHPShopOrder->format, '.', ' ');
    $price_n = number_format($val['price_n'], $PHPShopOrder->format, '.', ' ');
    $sum = number_format($val['price'] * $val['num'], $PHPShopOrder->format, '.', ' ');


    PHPShopParser::set('product_mail_price', $price);
    PHPShopParser::set('product_mail_price_n', $price_n);
    PHPShopParser::set('product_mail_pic', $val['pic_small']);
    PHPShopParser::set('product_mail_uid', $val['uid']);
    PHPShopParser::set('product_mail_name', $val['name']);
    PHPShopParser::set('product_mail_num', $val['num']);
    PHPShopParser::set('product_mail_sum', $sum);
    PHPShopParser::set('product_mail_ed_izm', $val['ed_izm']);
    PHPShopParser::set('product_mail_currency', $option['currency']);
    PHPShopParser::set('product_mail_id', $val['id']);

    return PHPShopParser::file('../lib/templates/order/product_mail.tpl', true, true, true);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>