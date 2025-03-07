<?php

$TitlePage = __("������ �����");
unset($_SESSION['jsort']);

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $PHPShopBase;

    // ������� �������
    PHPShopObj::loadClass('order');
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $status_array = $PHPShopOrderStatusArray->getArray();
    $status[] = __('����� �����');
    
    if(empty($_GET['where']['statusi']))
        $_GET['where']['statusi']=null;
    
    $order_status_value[] = array(__('����� �����'), 0, $_GET['where']['statusi']);
    if (is_array($status_array))
        foreach ($status_array as $status_val) {

            $status[$status_val['id']] = substr($status_val['name'], 0, 22);
            $order_status_value[] = array($status_val['name'], $status_val['id'], $_GET['where']['statusi']);
        }

    if (!isset($_GET['where']['statusi']))
        $_GET['where']['statusi'] = 'none';
    $order_status_value[] = array(__('��� ������'), 'none', $_GET['where']['statusi']);

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopInterface->action_button['������'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn active',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-align-justify',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="' . __('������') . '" '
    );
    
    $PHPShopInterface->action_button['������'] = array(
        'name' => '',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'action' => 'lead.kanban',
        'icon' => 'glyphicon glyphicon-th-large',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('������') . '" '
    );
    
        $PHPShopInterface->action_button['�������� �������'] = array(
        'name' => '',
        'action' => 'lead.kanban&action=new&return=lead',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="'.__('�������� �������').'" '
    );

    $PHPShopInterface->setActionPanel($TitlePage, false, array('������','������','�������� �������'));


    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption( 
            array("�", "10%", array('align' => 'left')),
            array("������", "15%"),
            array("����", "10%"), 
            array("���", "25%"), 
            array("�������", "15%",array('align'=>'right')));

    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './lead/gui/lead.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopInterface->Compile();
}


// ��������� �������
$PHPShopInterface->getAction();
?>