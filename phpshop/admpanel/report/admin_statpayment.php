<?php

$TitlePage = __("������ �� �������� �������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopInterface;

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


    // �����
    $where = $clean = null;

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
    $PHPShopGUI->setActionPanel($TitlePage, false, false,false);

    // �����
    $color = array('#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360', '#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360', '#F7464A', '#46BFBD', '#FDB45C', '#949FB1',);

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT count(a.statusi) as num, b.name FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['order_status'] . ' AS b ON a.statusi = b.id  ' . $where . ' 
            group by a.statusi limit 10';
    $canvas_export = null;
    $data = $PHPShopOrm->select();
    $i = 0;
    if (is_array($data))
        foreach ($data as $row) {

            $canvas_export.='<span data-value=\'pieData[' . $i . ']={value: ' . $row['num'] . ',color:"' . $color[$i] . '",label: "' . $row['name'] . '"}\'></span>';
            $i++;
        }

    if (empty($canvas_export))
        $alert = '<p class="text-warning">' . __('��� ������...') . '</p>';
    else $alert = null;


    $PHPShopGUI->_CODE.=' 
         <div class="panel panel-default">
                <div class="panel-body">'.$alert.'
                            <!-- Progress -->
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    '.__('��������').'...
                                </div>
                            </div>   
                            <!--/ Progress -->
                 <span id="data-value" class="hide">
                     ' . $canvas_export . '
                 </span>
                 <div>
                     <canvas id="chart-area"></canvas>
                 </div>
          </div>
       </div>';


    // ������ ������
    $PHPShopInterface->field_col = 1;
    $searchforma=$PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, '���� ������ ������');
    $searchforma.=$PHPShopInterface->setInputDate("date_end", $date_end, false, null, '���� ����� ������');

    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma.=$PHPShopInterface->setButton('��������', 'search', 'btn-order-search pull-right');

    if ($clean)
        $searchforma.=$PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left');

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