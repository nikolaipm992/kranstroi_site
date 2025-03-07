<?php

$TitlePage = __("������ ������������");

// ���������� ������������ �� �����
function mailNotice($type, $until_day, $promo = null) {
    global $PHPShopSystem;

    $admoption = $PHPShopSystem->getParam('admoption');
    $option = unserialize($admoption);

    if (empty($option[$type . '_notice'])) {

        PHPShopParser::set('url', $_SERVER['SERVER_NAME']);
        PHPShopParser::set('day', abs(round($until_day)));
        switch ($type) {
            case "license":

                $userContent = PHPShopParser::file("tpl/license.mail.tpl", true, false);
                new PHPShopMail($PHPShopSystem->getEmail(), $PHPShopSystem->getEmail(), __('������������� �������� ��� �����') . ' ' . $_SERVER['SERVER_NAME'], $userContent, "text/html",false, false, true);

                break;
            case "support":
                $userContent = PHPShopParser::file("tpl/support.mail.tpl", true, false);
                new PHPShopMail($PHPShopSystem->getEmail(), $PHPShopSystem->getEmail(), __('������������� ����������� ��������� ��� �����') . ' ' . $_SERVER['SERVER_NAME'], $userContent, "text/html",false, false, true);

                break;
        }

        $option[$type . '_notice'] = true;
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);
        $PHPShopOrm->update(array('admoption_new' => serialize($option)));
    }
}

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $PHPShopGUI, $TitlePage, $PHPShopBase,$hideCatalog,$hideSite;


    // ��������� �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['search_jurnal']);
    $data = $PHPShopOrm->select(array('name'), array('num' => '>0'), array('order' => 'id desc'), array('limit' => 10));
    $search_jurnal = null;
    $search_jurnal_title = __('����� ��������� �������') . ' <a href="#" class="search pull-right">' . __('����������� �����') . '</a>';
    $search_jairnal_icon = 'search';
    $search_jurnal_class = null;
    if (is_array($data)) {
        foreach ($data as $row) {
            if (strlen($row['name']) > 5)
                $search_jurnal .= '<a href="?path=report.searchjurnal" class="btn btn-default btn-xs search_var">' . PHPShopSecurity::TotalClean(substr($row['name'], 0, 30)) . '</a> ';
        }
    }

    // ������� �������
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $status_array = $PHPShopOrderStatusArray->getArray();
    $status[0] = __('����� �����');
    $order_status_value[] = array(__('����� �����'), 0, 0);
    if (is_array($status_array))
        foreach ($status_array as $k => $status_val) {
            $status[$k] = $status_val['name'];
            $order_status_value[] = array($status_val['name'], $status_val['id'], 0);
        }

    // �����
    $where = null;
    if (array_key_exists('where', $_GET) and is_array($_GET['where'])) {
        foreach ($_GET['where'] as $k => $v) {
            if (!empty($v))
                $where .= ' ' . $k . ' = "' . $v . '" or';
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
    }

    $PHPShopGUI->action_button['�����'] = array(
        'name' => '<span class=clock-tmp>' . date("H:i:s", time()) . '</span>',
        'locale' => false,
        'class' => 'btn btn-default btn-sm clock navbar-btn hidden-xs',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-time'
    );

    $License = parse_ini_file_true("../../license/" . PHPShopFile::searchFile('../../license/', 'getLicense'), 1);


    // �������� ����������
    if ($PHPShopBase->Rule->CheckedRules('update', 'view'))
        if (!isset($_SESSION['update_check'])) {
            define("UPDATE_PATH", "http://www.phpshop.ru/update/update5.php?from=" . $License['License']['DomenLocked'] . "&version=" . $GLOBALS['SysValue']['upload']['version'] . "&support=" . $License['License']['SupportExpires'] . '&serial=' . $License['License']['Serial'] . '&path=intro');

            $update_enable = @xml2array(UPDATE_PATH, "update", true);
            if (is_array($update_enable) and $update_enable['status'] != 'no_update') {
                $_SESSION['update_check'] = intval($update_enable['name'] - $update_enable['num']);
                
            } else
                $_SESSION['update_check'] = 0;
        }


    if ($License['License']['Pro'] == 'Start') {
        $_SESSION['mod_limit'] = 5;
    } else
        $_SESSION['mod_limit'] = 50;

    // ������������� ���������
    if ($License['License']['RegisteredTo'] != 'Trial NoName') {
        $LicenseUntilUnixTime = $License['License']['SupportExpires'];
        $_SESSION['support'] = $LicenseUntilUnixTime;
        $until = $LicenseUntilUnixTime - date("U");
        $until_day = round($until / (24 * 60 * 60));
        if (is_numeric($LicenseUntilUnixTime))
            if ($until_day < 8 and $until_day > 0) {
                mailNotice('support', $until_day);
                $_SESSION['update'] = 3;
                $search_jurnal = __('�� ����� ������, ��� ��� ��������� �������� ��������� ������������: ��� ������ ��������, �� ��������� ����� ��� ������������.');
                $search_jurnal_title = __('����������� ��������� ������������� �����') . ' <span class="label label-warning">' . abs(round($until_day)) . '  ' . __('��.') . '</span><a class="pull-right btn btn-xs btn-default" href="https://www.phpshop.ru/order/?from=' . $_SERVER['SERVER_NAME'].'" target="_blank"><span class="glyphicon glyphicon-ruble"></span> ' . __('������') . '</a>';
                $search_jurnal_class = 'panel-success';
                $search_jairnal_icon = 'exclamation-sign';
            }
    }
    
    // ��������� �� ����������
    if($License['License']['SupportExpires'] > date("U") and !empty($_SESSION['update_check']))
        $_SESSION['update_check_message'] = true;
    else $_SESSION['update_check_message'] = false;

    // ������������� ��������
    $LicenseUntilUnixTime = intval($License['License']['Expires']);
    $until = $LicenseUntilUnixTime - time();
    $until_day = round($until / (24 * 60 * 60));

    $until_promo = $until - 15 * 24 * 60 * 60;
    $hour = floor($until_promo / 3600);
    $day = floor($hour / 24);
    $min = ($until_promo / 60) % 60;
    if (is_numeric($LicenseUntilUnixTime)) {
        $until_promo_str = $LicenseUntilUnixTime - 15 * 24 * 60 * 60;

        // ���������
        if ($until_day < 8 and $until_day > 0) {
            mailNotice('license', $until_day);
            $search_jurnal = __('��� �������� �� ������ ������ ���������� ���������� ��������. <b>��� ���������, ������������� �� ����-������ �����, ����������</b>.');
            $search_jurnal_title = __('�������� ������������� �����') . ' <span class="label label-primary">' . abs(round($until_day)) . '  ' . __('��.') . '</span><a class="pull-right btn btn-xs btn-primary" href="https://www.phpshop.ru/order/?from=' . $_SERVER['SERVER_NAME'] . '" target="_blank"><span class="glyphicon glyphicon-ruble"></span> ' . __('������') . '</a>';
            $search_jurnal_class = 'panel-danger';
            $search_jairnal_icon = 'exclamation-sign';
        }
    }

    $PHPShopGUI->setActionPanel($TitlePage, false, array('�����'));
    $PHPShopGUI->addJSFiles('js/chart.min.js', 'intro/gui/intro.gui.js');
    $PHPShopInterface->checkbox_action = false;

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB')
        $currency = '<span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    // ������� � ������� �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;

    // �����
    if (!$PHPShopBase->Rule->CheckedRules('order', 'remove')) {
        $where .= 'where a.admin=' . $_SESSION['idPHPSHOP'];
    }


    $PHPShopOrm->sql = 'SELECT a.*, b.mail, b.name FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' AS a 
        LEFT JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS b ON a.user = b.id  ' . $where . ' 
            order by a.id desc limit 8';
    $data = $PHPShopOrm->select();
    $canvas_data = $data;
    $array_order_date = [];

    if (is_array($data))
        foreach ($data as $row) {

            // ���������� ������
            $PHPShopOrder = new PHPShopOrderFunction($row['id'], $row);

            if (empty($row['fio']) and ! empty($row['name']))
                $row['fio'] = $row['name'];
            elseif (empty($row['fio']) and empty($row['name']))
                $row['fio'] = $row['mail'];

            $datas = PHPShopDate::get($row['datas']);

            // ������
            if (!empty($status[$row['statusi']]))
                $status_name = $status[$row['statusi']];
            else
                $status_name = __('�� ���������');

            if ($row['id'] < 100)
                $uid = '<span class="hidden-xs hidden-md">' . __('�����') . '</span> ' . $row['uid'];
            else
                $uid = $row['uid'];

            if (empty($row['fio']) and ! empty($row['name']))
                $row['fio'] = $row['name'];

            if (!empty($row['user']))
                $user_link = '?path=shopusers&id=' . $row['user'];
            else
                $user_link = null;

            $PHPShopInterface->setRow(array('name' => '<span class="hidden-xs hidden-md label label-info" title="' . $status_name . '" style="background-color:' . $PHPShopOrder->getStatusColor() . '"><span class="hidden-xs hidden-md">' . mb_substr($status_name, 0, 20) . '</span></span>', 'link' => '?path=order&return=intro&id=' . $row['id'], 'class' => 'label-link'), array('name' => $uid, 'link' => '?path=order&return=intro&id=' . $row['id']), array('name' => mb_substr($row['fio'], 0, 20), 'link' => $user_link, 'title' => $row['fio']), array('name' => $datas, 'class' => 'text-muted hidden-xs'), array('name' => $PHPShopOrder->getTotal(false, ' ') . ' ' . $currency, 'align' => 'right', 'class' => 'strong'));
        }

    $order_list = $PHPShopInterface->getContent();

    // ������ �������
    $PHPShopOrm->sql = 'SELECT sum,datas FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' order by id desc limit 30';
    $canvas_value = $canvas_label = null;
    $data = $PHPShopOrm->select();

    if (is_array($data)) {
        foreach ($data as $row) {
            $d = date("d", $row['datas']) . '.' . date("m", $row['datas']);
            $array_order_date[$d] += (int)$row['sum'];
        }

        if (is_array($array_order_date)) {
            $array_order_date = array_reverse($array_order_date);
            foreach ($array_order_date as $date => $sum) {
                $canvas_value .= '"' . $sum . '",';
                $canvas_label .= '"' . $date . '",';
            }
        }
    }

    // �����������
    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->checkbox_action = false;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['jurnal']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id desc'), array('limit' => 5));
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['flag'])) {
                $status = '<span class="glyphicon glyphicon-ok"></span>';
                $link = '?path=users&id=' . $row['id'];
            } else {
                $status = '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
                $link = '?path=users.stoplist&action=new&ip=' . $row['ip'];
            }

            $PHPShopInterface->setRow($status, array('name' => $row['user'], 'link' => $link, 'align' => 'left'), array('name' => $row['ip'], 'align' => 'right'), array('name' => PHPShopDate::get($row['datas'], true), 'align' => 'right'));
        }
    $user_list = $PHPShopInterface->getContent();


    // ����� ����������
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('user' => "=" . intval($_SESSION['idPHPSHOP']));
    }


    // ������� �������
    $where['parent_enabled'] = "='0'";

    // ����� ������
    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->checkbox_action = false;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->select(array('id,name,items,datas'), $where, array('order' => 'datas desc'), array('limit' => 5));
    if (is_array($data))
        foreach ($data as $row) {


            $PHPShopInterface->setRow(
                    array('name' => $row['name'], 'link' => '?path=product&return=catalog&id=' . $row['id'], 'align' => 'left'), array('name' => PHPShopDate::get($row['datas'], false), 'align' => 'right', 'class' => 'text-nowrap'));
        }
    $product_list = $PHPShopInterface->getContent();

    $new_order = $PHPShopBase->getNumRows('orders', 'where statusi=0');
    if (!empty($new_order))
        $new_order = '+' . $new_order;

    $new_dialog = $PHPShopBase->getNumRows('dialog', "where isview='0'");
    if (!empty($new_dialog))
        $new_dialog = '+' . $new_dialog;

    $new_comment = $PHPShopBase->getNumRows('comment', "where enabled != '1'");
    if (!empty($new_comment))
        $new_comment = '+' . $new_comment;

    $PHPShopGUI->_CODE .= '
     <div class="row intro-row">
       <div class="col-md-2 col-xs-6 ">
          <div class="panel panel-default ">
             <div class="panel-heading"><span class="glyphicon glyphicon-flag"></span> ' . __('����� �������') . '</div>
                <div class="panel-body text-right panel-intro ">
                <a href="?path=order&where[statusi]=0" class="'.$hideCatalog.'">' . $new_order . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-comment"></span> ' . __('��������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=dialog">' . $new_dialog . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 hidden-xs hidden-sm col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-list-alt"></span> ' . __('������������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=shopusers.comment">' . $new_comment . '</a>
               </div>
          </div>
       </div>

       <div class="col-md-6 col-xs-12 hidden-xs col-panel">
          <div class="panel panel-default ' . $search_jurnal_class . '">
             <div class="panel-heading"><span class="glyphicon glyphicon-' . $search_jairnal_icon . '"></span> ' . $search_jurnal_title . '</div>
                <div class="panel-body">
                 ' . $search_jurnal . '
               </div>
          </div>
       </div>
   </div>   

   <div class="row intro-row '.$hideCatalog.'">
       <div class="col-md-6 ">
           <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-shopping-cart"></span> ' . __('������') . ' <a class="pull-right" href="?path=order">' . __('�������� ������') . '</a></div>
                   <table class="table table-hover intro-list">' . $order_list . '</table>
          </div>
       </div>
       <div class="hidden-xs hidden-sm col-md-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-stats"></span> ' . __('���������� �������') . ' 
             <span class="pull-right hidden-xs">
             
<div class="dropdown">
  <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    <span class="glyphicon glyphicon-cog"></span>
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right canvas-select">
    <li class="disabled"><a href="#" class="canvas-line">' . __('�������� ���������') . '</a></li>
    <li><a href="#" class="canvas-bar">' . __('�����������') . '</a></li>
    <li><a href="#" class="canvas-radar">' . __('����� ���������') . '</a></li>
    <li class="divider"></li>
    <li><a href="?path=report.statorder">' . __('�������� ������') . '</a></li>
  </ul>
</div>

             
                </span>
              </div>
                <div class="panel-body" style="padding:7px">
                 <div class="intro-canvas">
                     <canvas id="canvas" data-currency="' . $PHPShopSystem->getDefaultValutaCode() . '"  data-value=\'[' . substr($canvas_value, 0, strlen($canvas_value) - 1) . ']\' data-label=\'[' . substr($canvas_label, 0, strlen($canvas_label) - 1) . ']\'></canvas>
                 </div>
               </div>
          </div>
       </div>
   </div>
';

    // ������
    $metrica_id = $PHPShopSystem->getSerilizeParam('admoption.metrica_id');
    $metrica_token = $PHPShopSystem->getSerilizeParam('admoption.metrica_token');

    if (PHPShopSecurity::true_param($metrica_id, $metrica_token, $PHPShopSystem->getSerilizeParam('admoption.metrica_widget'))) {

        $PHPShopInterface = new PHPShopInterface();
        $PHPShopInterface->checkbox_action = false;
        $PHPShopInterface->setCaption(array("����", "10%"), array("�����", "10%", array('align' => 'center')), array("����������", "10%", array('align' => 'center')), array("���������", "10%", array('align' => 'center')), array("����� ", "10%", array('align' => 'right')));

        $ctx = stream_context_create(array('http' =>
            array(
                'timeout' => 5
            )
        ));

        $array_url_data = array(
            'preset' => 'traffic',
            'metrics' => 'ym:s:visits,ym:s:users,ym:s:pageviews,ym:s:percentNewVisitors,ym:s:bounceRate,ym:s:pageDepth,ym:s:avgVisitDurationSeconds',
            'group' => 'day',
            'date1' => date('Y-m-d', strtotime("-7 day")),
            'date2' => date('Y-m-d'),
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

        if (is_array($json_data)) {

            $canvas_data = $json_data = $json_data['data'];
            $canvas_value = $canvas_label = null;
            foreach ($json_data as $value) {
                $date = $value['dimensions'][0]['id'];
                $visits = $value['metrics'][0];
                $users = $value['metrics'][1];
                $pageviews = $value['metrics'][2];
                $avgVisitDurationSeconds = $value['metrics'][6] / 60;

                $PHPShopInterface->setRow(array('name' => date('d.m.Y', strtotime($date)), 'align' => 'left'), array('name' => $visits, 'align' => 'center'), array('name' => $users, 'align' => 'center'), array('name' => $pageviews, 'align' => 'center'), array('name' => round($avgVisitDurationSeconds, 2), 'align' => 'right'));
            }


            // ������
            if (is_array($canvas_data)) {
                krsort($canvas_data);
                foreach ($canvas_data as $value) {

                    $canvas_value .= '"' . $value['metrics'][0] . '",';
                    $canvas_label .= '"' . date('d.m', strtotime($value['dimensions'][0]['id'])) . '",';
                }
            }

            $traffic_list = $PHPShopInterface->getContent();


            $PHPShopGUI->_CODE .= ' 
    <div class="row intro-row">
       <div class="col-md-6 hidden-xs hidden-sm">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-equalizer"></span> ' . __('������������') . ' 
             <span class="pull-right hidden-xs">
             
<div class="dropdown">
  <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    <span class="glyphicon glyphicon-cog"></span>
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right canvas-select">
    <li class="disabled"><a href="#" class="canvas-line" data-canvas="2">' . __('�������� ���������') . '</a></li>
    <li><a href="#" class="canvas-bar" data-canvas="2">' . __('�����������') . '</a></li>
    <li><a href="#" class="canvas-radar" data-canvas="2">' . __('����� ���������') . '</a></li>
    <li class="divider"></li>
    <li><a href="?path=metrica">' . __('�������� ������') . '</a></li>
  </ul>
</div>

                </span>
              </div>
                <div class="panel-body" style="">
                 <div class="intro-canvas">
                     <canvas id="canvas2" data-title="' . __('����������') . '"  data-value=\'[' . substr($canvas_value, 0, strlen($canvas_value) - 1) . ']\' data-label=\'[' . substr($canvas_label, 0, strlen($canvas_label) - 1) . ']\'></canvas>
                 </div>
               </div>
          </div>
       </div>
           <div class="col-md-6 col-panel">
       
           <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-dashboard"></span> ' . __('������������') . ' <a class="pull-right" href="?path=metrica.traffic">' . __('�������� ������') . '</a></div>
                   <table class="table table-hover ">' . $traffic_list . '</table>
          </div>

       </div>
     </div>';
        }
    }

    // ���������� ������
    $PHPShopGUI->_CODE .= '   
    <div class="row intro-row">
       <div class="col-md-2 col-xs-6">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-eye-open"></span> ' . __('�� �������') . '</div>
                <div class="panel-body text-right panel-intro">
                <a href="?path=catalog&where[enabled]=1">' . $PHPShopBase->getNumRows('products', "where enabled='1' and parent_enabled='0' and category != 1000004") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-eye-close"></span> ' . __('������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=catalog&where[enabled]=0&where[parent_enabled]=0">' . $PHPShopBase->getNumRows('products', "where (enabled='0' and parent_enabled='0') or category=1000004") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 col-xs-6 col-panel-not col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-bell"></span> ' . __('��� � �������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=catalog&where[sklad]=1">' . $PHPShopBase->getNumRows('products', "where sklad = '1'") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-folder-close"></span> ' . __('���������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=catalog">' . $PHPShopBase->getNumRows('categories', "") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 hidden-xs hidden-sm col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> ' . __('������������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=shopusers">' . $PHPShopBase->getNumRows('shopusers', "where enabled = '1'") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-2 hidden-xs hidden-sm col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-tasks"></span> ' . __('������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=modules&install=check">' . $PHPShopBase->getNumRows('modules', "") . '</a>
               </div>
          </div>
       </div>
   </div>';

    // ������ �����������
    $PHPShopGUI->_CODE .= '<div class="row intro-row hidden-xs">
       <div class="col-md-6 col-xs-12">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> ' . __('������ �����������') . ' <a class="pull-right" href="?path=users.jurnal">' . __('�������� ������') . '</a></div>
                <table class="table table-hover intro-list">' . $user_list . '</table>
          </div>
       </div>
       <div class="col-md-6 hidden-xs hidden-sm col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-refresh"></span> ' . __('���������� �������') . ' <a class="pull-right" href="?path=catalog">' . __('�������� ������') . '</a></div>
                <table class="table table-hover intro-list">' . $product_list . '</table>
          </div>
       </div>
   </div>   
';
    
    // �������� ���������� ���������
    $PHPShopGUI->_CODE .='<div id="update_check" data-update="'.$_SESSION['update_check_message'].'"></div>';

    $PHPShopGUI->Compile();
}
?>