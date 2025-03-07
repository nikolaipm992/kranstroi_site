<?php

$TitlePage = __("������ �� �������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopInterface, $PHPShopSystem;

    PHPShopObj::loadClass('valuta');
    PHPShopObj::loadClass('user');
    PHPShopObj::loadClass('order');

    $PHPShopGUI->action_select['�������� ���������'] = array(
        'name' => '�������� ���������',
        'action' => 'canvas-line',
        'class' => 'disabled'
    );

    $PHPShopGUI->action_select['�����������'] = array(
        'name' => '�����������',
        'action' => 'canvas-bar'
    );

    $PHPShopGUI->action_select['����� ���������'] = array(
        'name' => '����� ���������',
        'action' => 'canvas-radar'
    );


    $Months = array("01" => "������", "02" => "�������", "03" => "�����",
        "04" => "������", "05" => "���", "06" => "����", "07" => "����",
        "08" => "�������", "09" => "��������", "10" => "�������",
        "11" => "������", "12" => "�������");

    // ������� �������
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $status_array = $PHPShopOrderStatusArray->getArray();
    $status[] = __('����� �����');

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();
    
    if(empty($_GET['where']['statusi']))
        $_GET['where']['statusi']=null;

    $order_status_value[] = array(__('��� ������'), 0, $_GET['where']['statusi']);
    if (is_array($status_array))
        foreach ($status_array as $status_val) {
            $status[] = $status_val['name'];
            $order_status_value[] = array($status_val['name'], $status_val['id'], $_GET['where']['statusi']);
        }

    // �����
    $where = $clean = null;

    if (is_array($_GET['where'])) {
        foreach ($_GET['where'] as $k => $v) {
            if (!empty($v))
                $where.= ' ' . $k . ' = "' . $v . '" or';
        }

        if ($where) {
            $where = 'where' . substr($where, 0, strlen($where) - 2);
            $clean = true;
        }
    }


    $time = time();

    // ����
    if (!empty($_GET['date_start']) and !empty($_GET['date_end'])) {

        $clean = true;

        if ($where)
            $where.=' and ';
        else
            $where = ' where ';
        $where.=' a.datas between ' . (PHPShopDate::GetUnixTime($_GET['date_start']) - 1) . ' and ' . (PHPShopDate::GetUnixTime($_GET['date_end']) + 259200 / 2) . '  ';
    }
    else {

        $where.=' and a.datas between ' . ($time - 2592000) . ' and ' . ($time + 259200 / 2) . '  ';
    }

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get(time() - 2592000);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get(time() - 1);

    $TitlePage.=' '.__('�').' ' . $date_start . ' '.__('��').' ' . $date_end;


    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './js/bootstrap-datetimepicker.ru.js', 'js/chart.min.js', 'report/gui/report.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopGUI->setActionPanel($TitlePage, array('�������� ���������', '�����������', '����� ���������', '|', 'Export'), false, false);

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT a.*, b.mail FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS b ON a.user = b.id  ' . $where . ' 
            order by a.id ';
    $canvas_value = $canvas_label = $canvas_export = $alert = null;
    $data = $PHPShopOrm->select();

    $total = 0;
    if (is_array($data))
        foreach ($data as $row) {

            // ���������� ������
            if (empty($row['sum'])) {
                $PHPShopOrder = new PHPShopOrderFunction($row['id'], $row);
                $row['sum'] = $PHPShopOrder->getTotal(false);
            }

            $total += $row['sum'];

            if (empty($row['fio'])) {
                $row['fio'] = $row['mail'];
            }

            $canvas_export.='"' . $row['id'] . '",';

            $d_array = array(
                'm' => date("m", $row['datas']),
                'd' => date("d", $row['datas']),
                'h' => date("H", $row['datas']),
            );

            if (empty($array_order_date[$d_array['d'] . '.' . $d_array['m']])) {
                @$array_order_date[$d_array['d'] . ' ' . $Months[$d_array['m']]] += $row['sum'];
            }
            else
                @$array_order_date[$d_array['d'] . ' ' . $Months[$d_array['m']]]+=$row['sum'];
        }

    if (!empty($array_order_date) and is_array($array_order_date))
        foreach ($array_order_date as $date => $sum) {
            $canvas_value.='"' . $sum . '",';
            $canvas_label.='"' . __($date) . '",';
        }

    if (empty($canvas_value))
        $alert = '<p class="text-warning">' . __('��� ������...') . '</p>';


    $PHPShopGUI->_CODE.=' 
         <div class="panel panel-default">
                <div class="panel-body">' . $alert . '
                            <!-- Progress -->
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    ' . __('��������') . '...
                                </div>
                            </div>   
                            <!--/ Progress -->
                 <div>
                     <canvas id="canvas" data-currency="' . $PHPShopSystem->getDefaultValutaCode() . '"  data-value=\'[' . substr($canvas_value, 0, strlen($canvas_value) - 1) . ']\' data-label=\'[' . substr($canvas_label, 0, strlen($canvas_label) - 1) . ']\'></canvas>
                 </div>
          </div>
       </div>
       <span id="export" data-export=\'[' . substr($canvas_export, 0, strlen($canvas_export) - 1) . ']\' data-path="exchange.export.order&return=' . $_GET['path'] . '"></spam>';

    // ������� �������������
    PHPShopObj::loadClass('user');
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
    $user_status_value[] = array(__('��� ������������'), '', @$data['status']);
    
    if(empty($_GET['where']['b.status']))
        $_GET['where']['b.status']=null;
    
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $_GET['where']['b.status']);


    // ������ ������
    $PHPShopInterface->field_col = 1;
    $searchforma=$PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, '���� ������ ������');
    $searchforma.=$PHPShopInterface->setInputDate("date_end", $date_end, false, null, '���� ����� ������');

    $searchforma.= $PHPShopInterface->setSelect('where[statusi]', $order_status_value, '100%');
    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.fio]', 'placeholder' => '��� ����������', 'value' => @$_GET['where']['a.fio']));

    $searchforma.=$PHPShopInterface->setSelect('where[b.status]', $user_status_value, '100%');

    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[b.mail]', 'placeholder' => 'E-mail', 'value' => @$_GET['where']['b.mail']));
    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.tel]', 'placeholder' => '�������', 'value' => @$_GET['where']['a.tel']));
    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.city]', 'placeholder' => '�����', 'value' => @$_GET['where']['a.city']));
    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[a.street]', 'placeholder' => '�����', 'value' => @$_GET['where']['a.street']));

    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma.=$PHPShopInterface->setButton('��������', 'search', 'btn-order-search pull-right');

    if ($clean)
        $searchforma.=$PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left');

    if ($total > 0) {
        $stat = '<div class="order-stat-container">' . __('�����:') . ' <b>' . number_format($total, 2, ',', ' ') . '</b> ' . $currency . '<br>' . __('����������:') . ' <b>' . count($data) . '</b> ' . __('��.');
        $sidebarright[] = array('title' => '����������', 'content' => $stat);
    }


    $sidebarright[] = array('title' => '������', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './report/'));
    $sidebarright[] = array('title' => '��������', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));
    $PHPShopGUI->setSidebarRight($sidebarright, 2);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // �����
    $PHPShopGUI->Compile($form = false);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();
?>