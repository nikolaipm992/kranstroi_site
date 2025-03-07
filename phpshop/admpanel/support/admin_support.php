<?php

$TitlePage = __("������������");

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
    $License = parse_ini_file_true("../../license/" . $licFile, 1);

    if ($License['License']['SupportExpires'] < time() or $License['License']['DomenLocked'] == 'No')
        $action = 'noSupport';
    else
        $action = 'addNew';

    $PHPShopInterface->action_button['����� ������'] = array(
        'name' => __('����� ������ � ������������'),
        'action' => $action,
        'class' => 'btn btn-primary btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('����� ������ � ������������') . '"'
    );

    $PHPShopInterface->setActionPanel($TitlePage, false, array('����� ������'));
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./support/gui/support.gui.js');
    $PHPShopInterface->setCaption(array("���������", "60%"), array("�", "10%", array('align' => 'left')), array("����", "15%", array('align' => 'center')), array("������", "15%", array('align' => 'right')));


    PHPShopObj::loadClass('xml');
    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'];

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArray = readDatabase($data, 'row', false);
    } else {
        $dataArray = readDatabase($path, "row");
    }


    $status_array = array(
        0 => '<span>'.__('����� ������').'</span>',
        1 => '<span class="text-warning">'.__('�������� ������').'</span>',
        2 => '<span class="text-success">'.__('���� �����').'</span>',
        3 => '<span class="text-muted">'.__('���������').'</span>',
    );

    if (is_array($dataArray))
        foreach ($dataArray as $row) {

            $PHPShopInterface->setRow(array('name' => __($row['subject']), 'link' => '?path=' . $_GET['path'] . '&id=' . $row['id'] . '#m', 'align' => 'left'), array('name' => $row['id'], 'align' => 'left'), array('name' => $row['lastchange'], 'align' => 'center'), $status_array[$row['status']]);
        }

    $PHPShopInterface->Compile();
}

?>