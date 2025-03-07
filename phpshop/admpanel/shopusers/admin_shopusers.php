<?php

$TitlePage = __("����������");
PHPShopObj::loadClass('user');

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $PHPShopInterface->action_button['�������� ������������'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('�������� ������������') . '"'
    );

    $PHPShopInterface->action_title['order'] = '����� �����';

    // ��������� ������
    if (PHPShopString::is_mobile()) {
        $memory['shopusers.option']['name'] = 1;
        $memory['shopusers.option']['mail'] = 0;
        $memory['shopusers.option']['menu'] = 0;
        $memory['shopusers.option']['status'] =1;
        $memory['shopusers.option']['action'] =0;
        $memory['shopusers.option']['discount'] = 1;
        $memory['shopusers.option']['date'] = 0;
        $PHPShopInterface->mobile=true;
    }
    else {
        $memory['shopusers.option']['name'] = 1;
        $memory['shopusers.option']['mail'] = 1;
        $memory['shopusers.option']['menu'] = 1;
        $memory['shopusers.option']['status'] = 1;
        $memory['shopusers.option']['discount'] = 1;
        $memory['shopusers.option']['date'] = 1;
        $memory['shopusers.option']['action'] =1;
        
    }

    $PHPShopInterface->addJSFiles('./shopusers/gui/shopusers.gui.js', './shopusers/gui/shopusers.ajax.js');
    $PHPShopInterface->setActionPanel($TitlePage, array('CSV', '|', '������� ���������'), array('�������� ������������'));
    $PHPShopInterface->setCaption(array(null, "2%"), array("���", "25%",array('view' => intval($memory['shopusers.option']['name']))), array("E-mail", "20%",array('view' => intval($memory['shopusers.option']['mail']))), array("������", "20%",array('view' => intval($memory['shopusers.option']['status']))), array("������ %", "10%",array('view' => intval($memory['shopusers.option']['discount']))), array("����", "10%",array('view' => intval($memory['shopusers.option']['date']))), array("", "7%",array('view' => intval($memory['shopusers.option']['menu']))), array("������", "7%", array('align' => 'right','view' => intval($memory['shopusers.option']['action']))));
    $PHPShopInterface->Compile();
}

/**
 * ������ �������������
 */
function actionOrderSearch() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), array('login' => " LIKE '%" . $_POST['words'] . "%'", 'name' => " LIKE '" . ucfirst($_POST['words']) . "%'"), array('order' => 'id DESC'), array('limit' => 20));
    if (is_array($data)) {
        foreach ($data as $row) {

            $result .= '<a href=\'#\' class=\'select-search\' data-id=\'' . $row['id'] . '\' data-name=\'' . $row['name'] . '\' data-tel=\'' . $row['tel'] . '\' data-mail=\'' . $row['login'] . '\'>' . $row['name'] . ',  ' . $row['login'] . ', ' . $row['tel'] . '</a><br>';
        }
        $result .= '<button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        exit($result);
    } else
        exit();
}

// ��������� �������
$PHPShopInterface->getAction();
?>