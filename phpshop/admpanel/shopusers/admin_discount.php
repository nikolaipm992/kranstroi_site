<?php

$TitlePage = __("���������� - ������");
PHPShopObj::loadClass('user');

function actionStart() {
    global $PHPShopInterface,$PHPShopSystem;

    $PHPShopInterface->action_button['�������� ������'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="�������� ������"'
    );
    
        // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = ' '.$PHPShopSystem->getDefaultValutaCode();
    
        $PHPShopInterface->action_button['������ �� �������'] = array(
        'name' => '������ �� �������',
        'action' => 'shopusers.status',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-user'
    );


    $PHPShopInterface->action_select['������ �� �������'] = array(
        'name' => '������ �� �������',
        'url' => '?path=shopusers.status'
    );


    $PHPShopInterface->setActionPanel(__("������ ����������� �� ������"), array('������� ���������','������ �� �������'), array('�������� ������'));
    $PHPShopInterface->setCaption(array(null, "2%"), array("����� ������", "50%"), array("������ %", "20%"), array("", "10%"), array("������", "10%", array('align' => 'right')));

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['discount']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {
        
            $PHPShopInterface->setRow(
                    $row['id'], array('name' => $row['sum'].$currency, 'link' => '?path=shopusers.discount&id=' . $row['id'], 'align' => 'left'), $row['discount'],  array('action' => array('edit','|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
    $PHPShopInterface->Compile();
}

?>