<?php

$TitlePage = __("������");
unset($_SESSION['jsort']);

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $PHPShopBase;

    // ������� �������
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $status_array = $PHPShopOrderStatusArray->getArray();
    $status[] = __('����� �����');

    if (empty($_GET['where']['statusi']))
        $_GET['where']['statusi'] = null;

    $order_status_value[] = array(__('����� �����'), 0, $_GET['where']['statusi']);
    if (is_array($status_array))
        foreach ($status_array as $status_val) {

            $status[$status_val['id']] = substr($status_val['name'], 0, 22);
            $order_status_value[] = array($status_val['name'], $status_val['id'], $_GET['where']['statusi']);
        }

    if (!isset($_GET['where']['statusi']))
        $_GET['where']['statusi'] = 'none';
    $order_status_value[] = array(__('��� ������'), 'none', $_GET['where']['statusi']);

    // �����
    $where = null;
    $limit = 100;

    if (is_array($_GET['where'])) {
        foreach ($_GET['where'] as $k => $v) {
            if ($v != '' and $v != 'none')
                if ($k == 'a.user' || $k == 'statusi')
                    $where .= ' ' . PHPShopSecurity::TotalClean($k) . ' = "' . PHPShopSecurity::TotalClean($v) . '" or';
                else
                    $where .= ' ' . PHPShopSecurity::TotalClean($k) . ' like "%' . PHPShopSecurity::TotalClean($v) . '%" or';
        }

        if ($where)
            $where = 'where' . substr($where, 0, strlen($where) - 2);

        // ����
        if (!empty($_GET['date_start']) and ! empty($_GET['date_end'])) {
            if ($where)
                $where .= ' and ';
            else
                $where = ' where ';
            $where .= ' a.datas between ' . (PHPShopDate::GetUnixTime($_GET['date_start']) - 1) . ' and ' . (PHPShopDate::GetUnixTime($_GET['date_end']) + 259200 / 2) . '  ';
            $TitlePage .= ' � ' . $_GET['date_start'] . ' �� ' . $_GET['date_end'];
        }

        $limit = 300;
    }

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    $PHPShopInterface->action_select['������������� ���������'] = array(
        'name' => '������������� ���������',
        'action' => 'edit-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['���������'] = array(
        'name' => '��������� �����',
        'action' => 'option enabled'
    );

    $PHPShopInterface->action_button['�������� �����'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="' . __('�������� �����') . '" '
    );

    $PHPShopInterface->action_button['���������'] = array(
        'name' => '',
        'action' => '',
        'class' => 'btn btn-default btn-sm navbar-btn visible-xs option',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-cog',
        'tooltip' => ' title="' . __('���������') . '" '
    );

    $PHPShopInterface->action_button['������'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel hidden-xs',
        'type' => 'button',
        'action' => 'lead.kanban',
        'icon' => 'glyphicon glyphicon-th-large',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('������ �����') . '" '
    );

    $PHPShopInterface->setActionPanel($TitlePage, array('���������', '������������� ���������', 'CSV', '|', '������� ���������'), array('���������', '�������� �����', '������'));

    // ��������� �����
    if (!empty($_COOKIE['check_memory'])) {
        $memory = json_decode($_COOKIE['check_memory'], true);
    }

    if (empty($memory) or ! is_array($memory['order.option'])) {

        // ��������� ������
        if (PHPShopString::is_mobile()) {
            $memory['order.option']['uid'] = 1;
            $memory['order.option']['statusi'] = 1;
            $memory['order.option']['datas'] = 1;
            $memory['order.option']['fio'] = 0;
            $memory['order.option']['menu'] = 0;
            $memory['order.option']['tel'] = 0;
            $memory['order.option']['sum'] = 1;
            $memory['order.option']['city'] = 0;
            $memory['order.option']['adres'] = 0;
            $memory['order.option']['org'] = 0;
            $memory['order.option']['comment'] = 0;
            $memory['order.option']['cart'] = 0;
            $memory['order.option']['tracking'] = 0;
            $memory['order.option']['admin'] = 0;
            $memory['order.option']['discount'] = 0;
            $memory['order.option']['company'] = 0;
            $memory['order.option']['id'] = 0;
            $PHPShopInterface->mobile = true;
        } else {
            $memory['order.option']['uid'] = 1;
            $memory['order.option']['statusi'] = 1;
            $memory['order.option']['datas'] = 1;
            $memory['order.option']['fio'] = 1;
            $memory['order.option']['menu'] = 1;
            $memory['order.option']['tel'] = 1;
            $memory['order.option']['sum'] = 1;
            $memory['order.option']['city'] = 0;
            $memory['order.option']['adres'] = 0;
            $memory['order.option']['org'] = 0;
            $memory['order.option']['comment'] = 0;
            $memory['order.option']['cart'] = 0;
            $memory['order.option']['tracking'] = 0;
            $memory['order.option']['admin'] = 0;
            $memory['order.option']['discount'] = 0;
            $memory['order.option']['company'] = 0;
            $memory['order.option']['id'] = 0;
        }
    }
    else if(PHPShopString::is_mobile()){
        $PHPShopInterface->mobile = true;
    }


    $PHPShopInterface->setCaption(array(null, "2%"), array("�", "12%", array('align' => 'left', 'view' => intval($memory['order.option']['uid']))), array("ID", "10%", array('view' => intval($memory['order.option']['id']))), array("������", "20%", array('view' => intval($memory['order.option']['statusi']))), array("�������", "20%", array('view' => intval($memory['order.option']['cart']))), array("����", "15%", array('view' => intval($memory['order.option']['datas']))), array("����������", "20%", array('view' => intval($memory['order.option']['fio']))), array("�������", "15%", array('view' => intval($memory['order.option']['tel']))), array("", "7%", array('view' => intval($memory['order.option']['menu']))), array("������", "10%", array('view' => intval($memory['order.option']['discount']))), array("�����", "15%", array('view' => intval($memory['order.option']['city']))), array("�����", "25%", array('view' => intval($memory['order.option']['adres']))), array("��������", "15%", array('view' => intval($memory['order.option']['org']))), array("�����������", "15%", array('view' => intval($memory['order.option']['comment']))), array("Tracking", "15%", array('view' => intval($memory['order.option']['tracking']))), array("��������", "15%", array('view' => intval($memory['order.option']['admin']))), array("��. ����", "15%", array('view' => intval($memory['order.option']['company']))), array("�����", "17%", array('align' => 'right', 'view' => intval($memory['order.option']['sum']))));
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './order/gui/order.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');


    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get(time() - 2592000);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get(time() - 1);

    // ������� �������������
    PHPShopObj::loadClass('user');
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();

    if (empty($_GET['where']['b.status']))
        $_GET['where']['b.status'] = null;

    $user_status_value[] = array(__('��� ������������'), '', $_GET['where']['b.status']);
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $_GET['where']['b.status']);

    // ���������
    if ($PHPShopBase->Rule->CheckedRules('order', 'rule')) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data_manager = $PHPShopOrm->select(array('*'), array('enabled' => "='1'"), array('order' => 'id DESC'), array('limit' => 100));
        $manager_status_value[] = array(__('��� ���������'), '', '');
        if (is_array($data_manager))
            foreach ($data_manager as $manager_status)
                $manager_status_value[] = array($manager_status['name'], $manager_status['id'], $_GET['where']['b.status']);
    }

    // ����������� ����
    $PHPShopCompany = new PHPShopCompanyArray();
    $PHPShopCompanyArray = $PHPShopCompany->getArray();

    if (is_array($PHPShopCompanyArray)) {
        $company_value[] = array(__('��� ����������� ����'), '', '');
        $company_value[] = array($PHPShopSystem->getSerilizeParam("bank.org_name"), 0, $_GET['where']['a.company']);
        foreach ($PHPShopCompanyArray as $company)
            $company_value[] = array($company['name'], $company['id'], $_GET['where']['a.company']);
    }

    // �������
    $PHPShopServerOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
    $data_server = $PHPShopServerOrm->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 1000));

    if (is_array($data_server)) {
        $server_value[] = array(__('��� �������'), 'none', 'none');
        foreach ($data_server as $row) {
            $server_value[] = array(PHPShopString::check_idna($row['host'], true), $row['id'], 0);
        }
    }

    if (empty($_GET['where']['a.uid']))
        $_GET['where']['a.uid'] = null;

    if (empty($_GET['where']['a.fio']))
        $_GET['where']['a.fio'] = null;

    if (empty($_GET['where']['a.org_name']))
        $_GET['where']['a.org_name'] = null;

    // ������ ������
    $PHPShopInterface->field_col = 1;
    $searchforma = $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, '���� ������ ������');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, '���� ����� ������');
    $searchforma .= $PHPShopInterface->setSelect('where[statusi]', $order_status_value, '100%');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.uid]', 'placeholder' => '� ������', 'value' => $_GET['where']['a.uid']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.fio]', 'placeholder' => '��� ����������', 'value' => $_GET['where']['a.fio']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.org_name]', 'placeholder' => '��������', 'value' => $_GET['where']['a.org_name']));

    if ($PHPShopBase->Rule->CheckedRules('order', 'rule'))
        $searchforma .= $PHPShopInterface->setSelect('where[a.admin]', $manager_status_value, '100%');

    if (!empty($server_value) and is_array($server_value))
        $searchforma .= $PHPShopInterface->setSelect('where[a.servers]', $server_value, '100%');

    if (!empty($company_value) and is_array($company_value))
        $searchforma .= $PHPShopInterface->setSelect('where[a.company]', $company_value, '100%');

    if (empty($_GET['where']['b.mail']))
        $_GET['where']['b.mail'] = null;

    if (empty($_GET['where']['a.tel']))
        $_GET['where']['a.tel'] = null;

    if (empty($_GET['where']['a.city']))
        $_GET['where']['a.city'] = null;

    if (empty($_GET['where']['a.city']))
        $_GET['where']['a.street'] = null;
    
    if (empty($_GET['search']['name']))
       $_GET['search']['name'] = null;


    $searchforma .= $PHPShopInterface->setSelect('where[b.status]', $user_status_value, '100%');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[b.mail]', 'placeholder' => 'E-mail', 'value' => $_GET['where']['b.mail']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.tel]', 'placeholder' => '�������', 'value' => $_GET['where']['a.tel']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.city]', 'placeholder' => '�����', 'value' => $_GET['where']['a.city']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.street]', 'placeholder' => '�����', 'value' => $_GET['where']['a.street']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'search[name]', 'placeholder' => '�����', 'value' => $_GET['search']['name']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setButton('�����', 'search', 'btn-order-search pull-right');

    if (isset($_GET['search']['value']))
        $searchforma .= $PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left');
    else
        $searchforma .= $PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel hide pull-left');

    // ����������
    $stat = '<div class="order-stat-container">' . __('�����:') . ' <b id="stat_sum">0</b> ' . $currency . '<br>' . __('����������:') . ' <b id="stat_num">0</b> ' . __('��.');
    $sidebarright[] = array('title' => '����������', 'content' => $stat);


    // ������ �������
    $sidebarright[] = array('title' => '����������� �����', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}

/**
 * ������� ����� �������
 */
function actionGetNew() {
    global $PHPShopBase;
    header("Content-Type: application/json");
    exit(json_encode(array('success' => 1, 'num' => $PHPShopBase->getNumRows('orders', "where statusi='0'"))));
}

// ��������� �������
$PHPShopInterface->getAction();
?>