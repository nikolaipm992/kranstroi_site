<?php

$TitlePage = __("������ �� ��������� ���������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopInterface, $PHPShopSystem;

    PHPShopObj::loadClass(array('valuta', 'user', 'order', 'category', 'product'));


    // �����
    $where = $clean = null;
    $where = ' utm!="" and ';

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB')
        $currency = ' <span class="rubznak">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $time = time();

    // ����
    if (!empty($_GET['date_start']) and ! empty($_GET['date_end'])) {
        $clean = true;
        $where .= ' datas between ' . (PHPShopDate::GetUnixTime($_GET['date_start']) - 1) . ' and ' . (PHPShopDate::GetUnixTime($_GET['date_end']) + 259200 / 2) . '  ';
    } else {
        $where .= ' datas between ' . ($time - 2592000) . ' and ' . ($time + 259200 / 2) . '  ';
    }

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get(time() - 2592000);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get(time() - 1);

    $TitlePage .= ' ' . __('�') . ' ' . $date_start . ' ' . __('��') . ' ' . $date_end;

    // ��������
    $PHPShopOrm�ampaign = new PHPShopOrm($PHPShopModules->getParam("base.adanalyzer.adanalyzer_campaign"));
    $data_campaign = $PHPShopOrm�ampaign->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 100));

    if (is_array($data_campaign))
        foreach ($data_campaign as $row){
            $Campaign[$row['utm']]['name'] = $row['name'];
            $Campaign[$row['utm']]['id'] = $row['id'];
        }


    // ������ �������� ����
    $PHPShopInterface->field_col = 3;
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js','../modules/adanalyzer/admpanel/gui/adanalyzer.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, array('Export'), false, false);
    $PHPShopInterface->setCaption(array('�', '5%'), array("��������� ��������", "65%"), array("���-��", "15%", array('align' => 'center')), array("�������", "15%", array('align' => 'center', 'view' => intval($_GET['where']['margin']))), array("�����", "15%", array('align' => 'right')));

    // ������� �� �������
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT id,sum,utm FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' where ' . $where . ' order by id ';
    $data = $PHPShopOrm->select();
    $catCount = array();
    $total=0;

    if (is_array($data))
        foreach ($data as $row) {
        
            $total+=$row['sum'];
        
            if (key_exists($row['utm'], $catCount)) {
                $catCount[$row['utm']]['count'] ++;
                $catCount[$row['utm']]['sum'] += $row['sum'];
            } else {
                $catCount[$row['utm']]['count'] = 1;
                $catCount[$row['utm']]['sum'] = $row['sum'];
            }
            
            $catCount[$row['utm']]['export'][] = $row['id'];
        }

    $max = 0;
    foreach ($catCount as $key => $val) {
        $max += $val['count'];
    }

    $export = null;
    $i = 1;
    
    if (is_array($catCount))
        foreach ($catCount as $key => $row) {

            $export.='"' . @implode(',', $row['export']) . '",';
            $value = round(($row['count'] * 100) / $max);

            if (!empty($_GET['where']['margin']))
                $margin = round(($row['sum'] * intval($_GET['where']['margin'])) / 100);

            $progress = '
<a href="?path=modules.dir.adanalyzer&id='.$Campaign[$key]['id'].'">' . $Campaign[$key]['name'] . ' - '.$key.'</a>
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $value . '%;">
      ' . $value . '%
  </div>
</div>';

            $PHPShopInterface->setRow($i, $progress, array('name' => $row['count'], 'align' => 'center'), array('name' => $margin . $currency, 'view' => intval($_GET['where']['margin']), 'align' => 'center'), array('name' => $row['sum'] . $currency, 'align' => 'right', 'order' => $row['sum']));
            $i++;
        }
        
     $PHPShopInterface->_CODE.='<span id="export" data-export=\'[' . substr($export, 0, strlen($export) - 1) . ']\' data-path="exchange.export.order&return=' . $_GET['path'] . '"></spam>';

    // ����
    $PHPShopInterface->field_col = 1;
    $searchforma .= $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, '���� ������ ������');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, '���� ����� ������');
    //$searchforma .= '<p>' . $PHPShopInterface->setInputArg(array('type' => 'text', 'caption' => $currency, 'name' => 'where[margin]', 'placeholder' => '��������� ������', 'value' => $_GET['where']['margin'])) . '</p>';
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setButton('��������', 'search', 'btn-order-search pull-right');

    if ($clean)
        $searchforma .= $PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left');
    
    if ($total > 0) {
        $stat = '<div class="order-stat-container">' . __('�����:') . ' <b>' . number_format($total, 2, ',', ' ') . '</b> ' . $currency . '<br>' . __('����������:') . ' <b>' . count($data) . '</b> ' . __('��.');
        $sidebarright[] = array('title' => '����������', 'content' => $stat);
    }

    $sidebarright[] = array('title' => '��������', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // �����
    $PHPShopInterface->Compile(2);
    return true;
}

// ��������� �������
$PHPShopInterface->getAction();
?>