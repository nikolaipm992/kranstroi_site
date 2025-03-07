<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.wbseller.wbseller_system"));
$WbSeller = new WbSeller();

// ���������� ���
function actionUpdatePrice() {

    // ������������
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/wbseller/cron/products.php?s=" . $cron_secure ;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('link_new', 'create_products_new', 'log_new','discount_new');

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.wbseller.wbseller_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);


    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $WbSeller,$TitlePage, $select_name;

    $PHPShopGUI->field_col = 4;

    // �������
    $data = $PHPShopOrm->select();
    
    if ($data['token'] !== '') {
        $PHPShopGUI->action_button['��������� ����'] = [
            'name' => __('��������� ����'),
            'class' => 'btn btn-default btn-sm navbar-btn ',
            'type' => 'submit',
            'action' => 'exportID',
            'icon' => 'glyphicon glyphicon-export'
        ];
        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['��������� ����', '��������� � �������']);
    }

    // ������
    $status[] = [__('����� �����'), 0, $data['status']];
    $statusArray = (new PHPShopOrm('phpshop_order_status'))->getList(['id', 'name']);
    foreach ($statusArray as $statusParam) {
        $status[] = [$statusParam['name'], $statusParam['id'], $data['status']];
    }

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);


    $Tab1 = $PHPShopGUI->setField('API key', $PHPShopGUI->setTextarea('token_new', $data['token'], false, '100%', '100'));
    $Tab1 .= $PHPShopGUI->setField('������ ������ ������', $PHPShopGUI->setSelect('status_new', $order_status_value, '100%'));


    // ������� �������������� ��������
    $order_status_import_value[] = array(__('������ �� �������'), 0, $data['status_import']);
    foreach ($WbSeller->status_list as $k => $status_val) {
        $order_status_import_value[] = array(__($status_val), $k, $data['status_import']);
    }
    $Tab1 .= $PHPShopGUI->setField('������ ������ � WB ��� �������������� ��������', $PHPShopGUI->setSelect('status_import_new', $order_status_import_value, '100%'));

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

            $delivery_value[] = array($delivery['city'], $delivery['id'], $data['delivery'], 'data-subtext="' . $delivery['price'] . '"');
        }

    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('delivery_new', $delivery_value, '100%'));


    $Tab1 .= $PHPShopGUI->setField('���� ����������', $PHPShopGUI->setRadio("type_new", 1, "ID ������", $data['type']) . $PHPShopGUI->setRadio("type_new", 2, "������� ������", $data['type']));
    $Tab1 .= $PHPShopGUI->setField('������ �� �����', $PHPShopGUI->setCheckbox('link_new', 1, '�������� ������ �� ����� � Wildberries', $data['link']));
    $Tab1 .= $PHPShopGUI->setField('��������� �����', $PHPShopGUI->setCheckbox('create_products_new', 1, '��������� ������������� ����� �� ������', $data['create_products']));
    $Tab1 .= $PHPShopGUI->setField('������ ��������', $PHPShopGUI->setCheckbox('log_new', 1, null, $data['log']));

    $Tab1 = $PHPShopGUI->setCollapse('���������', $Tab1);


    if ($data['fee_type'] == 1) {
        $status_pre = '-';
    } else {
        $status_pre = '+';
    }

    $getWarehouse = $WbSeller->getWarehouse();
    if (is_array($getWarehouse))
        foreach ($getWarehouse as $warehouse)
            $warehouse_value[] = array(PHPShopString::utf8_win1251($warehouse['name']), $warehouse['id'], $data['warehouse_id']);

    $Tab3 = $PHPShopGUI->setCollapse('����', $PHPShopGUI->setField('������� ��� WB', $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText($status_pre, 'fee_new', $data['fee'], 100, '%')) .
            $PHPShopGUI->setField('��������', $PHPShopGUI->setRadio("fee_type_new", 1, "���������", $data['fee_type']) . $PHPShopGUI->setRadio("fee_type_new", 2, "���������", $data['fee_type'])) .
            $PHPShopGUI->setField("����� WB", $PHPShopGUI->setSelect('warehouse_id_new', $warehouse_value, '100%')).
            $PHPShopGUI->setField('������ WB', $PHPShopGUI->setCheckbox('discount_new', 1, '������ ���� ������ � WB', $data['discount']))
    );

    // ����������
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1 . $Tab3, true, false, true), array("����������", $Tab2), array("� ������", $Tab4));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit").
            $PHPShopGUI->setInput("submit", "exportID", "���������", "right", 80, "", "but", "actionUpdatePrice.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ������ ���������
 */
function actionCategorySearch() {
    global $WbSeller;

    $data = $WbSeller->getTree(PHPShopString::win_utf8($_POST['words']))['data'];

    if (is_array($data)) {
        foreach ($data as $row) {

            $result .= '<a href=\'#\' class=\'select-search-wb\'  data-id=\'' . (int) $row['subjectID'] . '\' data-name=\'' . PHPShopString::utf8_win1251($row['subjectName']) . '\'>' . PHPShopString::utf8_win1251($row['parentName']) . ' &rarr; ' . PHPShopString::utf8_win1251($row['subjectName']) . '</a><br>';
        }
        $result .= '<button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        exit($result);
    } else
        exit();
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
