<?php

$TitlePage = __("���������� - ����������");

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name, $PHPShopSystem;

    // ���������
    $metrica_id = $PHPShopSystem->getSerilizeParam('admoption.metrica_id');
    $metrica_token = $PHPShopSystem->getSerilizeParam('admoption.metrica_token');

    $PHPShopInterface->action_button['�������� � �������'] = array(
        'name' => __('����� �� ������.�������'),
        'action' => 'https://metrika.yandex.ru/stat/tech_devices?id=' . $metrica_id,
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel-blank',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-export'
    );

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './js/bootstrap-datetimepicker.ru.js', 'metrica/gui/metrica.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    if (empty($_GET['date_start']))
        $date_start = date('Y-m-d');
    else {
        $date_start = $_GET['date_start'];
        $clean = true;
    }

    if (empty($_GET['date_end']))
        $date_end = date('Y-m-d');
    else
        $date_end = $_GET['date_end'];


    // ��������
    if (!empty($_GET['group_date'])) {
        switch ($_GET['group_date']) {
            case "today":
                $date_start = date('Y-m-d');
                $date_end = date('Y-m-d');
                break;
            case "yesterday":
                $date_start = date('Y-m-d', strtotime("-1 day"));
                $date_end = date('Y-m-d');
                break;
            case "week":
                $date_start = date('Y-m-d', strtotime("-7 day"));
                $date_end = date('Y-m-d');
                break;
            case "month":
                $date_start = date('Y-m-d', strtotime("-1 month"));
                $date_end = date('Y-m-d');
                break;
            case "quart":
                $date_start = date('Y-m-d', strtotime("-3 month"));
                $date_end = date('Y-m-d');
                break;
            case "year":
                $date_start = date('Y-m-d', strtotime("-12 month"));
                $date_end = date('Y-m-d');
                break;
        }
    }else $_GET['group_date']=null;

    $TitlePage.=__(' � ') . $date_start . __(' �� ') . $date_end;

    if (empty($_GET['group'])) {
        $_GET['group'] = 'day';
    }

    $array_url_data = array(
        'preset' => 'tech_devices',
        'metrics' => 'ym:s:visits, ym:s:users, ym:s:bounceRate, ym:s:pageDepth, ym:s:avgVisitDurationSeconds',
        'group' => $_GET['group'],
        'date1' => $date_start,
        'date2' => $date_end,
        'id' => $metrica_id,
        'oauth_token' => $metrica_token,
    );

    $url = 'https://api-metrika.yandex.ru/stat/v1/data?' . http_build_query($array_url_data);
    $�url = curl_init();
    curl_setopt_array($�url, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Authorization: OAuth ' . $metrica_token),
        CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
    ));

    $json_data = json_decode(curl_exec($�url), true);
    curl_close($�url);

    if (empty($json_data))
        $json_data = json_decode(file_get_contents($url), true);

    $PHPShopInterface->setActionPanel($TitlePage, $select_name, array('�������� � �������'));
    $PHPShopInterface->setCaption(array("��� ����������, �������������, ������ ����������", "40%"), array("������", "10%"), array("����������", "10%"), array("������", "10%"), array("�������", "10%"), array("�����", "10%", array('align' => 'left')));

    if (!empty($json_data['data']) and is_array($json_data['data'])) {

        $PHPShopInterface->setRow(__('����� � �������'), $json_data['totals'][0], $json_data['totals'][1], round($json_data['totals'][2], 2) . '%', round($json_data['totals'][3], 2), round($json_data['totals'][4] / 60, 2));

        $json_data = $json_data['data'];


        foreach ($json_data as $value) {

            $name = PHPShopString::utf8_win1251($value['dimensions'][0]['name']);

            if (!empty($value['dimensions'][2]['name']))
                $name.=' &rarr; ' . PHPShopString::utf8_win1251($value['dimensions'][1]['name'] . ' ' . $value['dimensions'][2]['name']);

            $visits = $value['metrics'][0];
            $users = $value['metrics'][1];
            $bounceRate = $value['metrics'][2];
            $pageDepth = $value['metrics'][3];
            $avgVisitDurationSeconds = $value['metrics'][4] / 60;

            if ($value['dimensions'][0]['icon_id'] == 'mobile')
                $icon = '<span class="glyphicon glyphicon-phone"></span> ';
            else if ($value['dimensions'][0]['icon_id'] == 'tablet')
                $icon = '<span class="glyphicon glyphicon-unchecked"></span> ';
            else
                $icon = '<span class="glyphicon glyphicon-blackboard"></span> ';


            $PHPShopInterface->setRow(array('name' => $icon . $name), $visits, $users, round($bounceRate, 2) . '%', round($pageDepth, 2), round($avgVisitDurationSeconds, 2));
        }
    }

    $searchforma=$PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, '���� ������ ������');
    $searchforma.=$PHPShopInterface->setInputDate("date_end", $date_end, false, null, '���� ����� ������');
    $searchforma.= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));

    /*
      $group_value[] = array(__('�� ����'), 'day', $_GET['group']);
      $group_value[] = array(__('�� �������'), 'week', $_GET['group']);
      $group_value[] = array(__('�� �������'), 'month', $_GET['group']);
      $searchforma.= $PHPShopInterface->setSelect('group', $group_value, 180); */

    $group_date_value[] = array(__('��������'), 0, $_GET['group_date']);
    $group_date_value[] = array(__('�������'), 'today', $_GET['group_date']);
    $group_date_value[] = array(__('�����'), 'yesterday', $_GET['group_date']);
    $group_date_value[] = array(__('������'), 'week', $_GET['group_date']);
    $group_date_value[] = array(__('�����'), 'month', $_GET['group_date']);
    $group_date_value[] = array(__('�������'), 'quart', $_GET['group_date']);
    $group_date_value[] = array(__('���'), 'year', $_GET['group_date']);
    $searchforma.= $PHPShopInterface->setSelect('group_date', $group_date_value, 180);

    $searchforma.=$PHPShopInterface->setButton('��������', 'search', 'btn-order-search pull-right');

    if (!empty($clean))
        $searchforma.=$PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left visible-lg');


    $sidebarright[] = array('title' => '������', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './metrica/'));
    $sidebarright[] = array('title' => '��������', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2);

    $PHPShopInterface->Compile($form = false);
}

?>