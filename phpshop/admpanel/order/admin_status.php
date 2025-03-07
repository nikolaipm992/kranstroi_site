<?php

$TitlePage = __("������� �������");


function actionStart() {
    global $PHPShopInterface,$TitlePage;
    
    $status=array('<span class="glyphicon glyphicon-remove text-muted"></span>','<span class="glyphicon glyphicon-ok"></span>');


    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'), array('��������'));
        $PHPShopInterface->setCaption(array(null, "3%"), array("��������", "40%"), array("����", "10%",array('sort'=>'none')), array("��������", "10%", array('align' => 'center','tooltip'=>'�������� �� ������','sort'=>'none')),array("����������", "10%", array('align' => 'center','tooltip'=>'E-mail ����� �������','sort'=>'none')), array("", "10%"), array("���� ������", "15%", array('align' => 'right','sort'=>'none')));

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'num'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {
             $color='<span class="glyphicon glyphicon-text-background" style="color:' . $row['color'] . '"></span>';
             $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=order.status&id=' . $row['id'], 'align' => 'left'), $color, array('name' => $status[intval($row['sklad_action'])], 'align' => 'center'), array('name' => $status[intval($row['mail_action'])], 'align' => 'center'), array('action' => array('edit','|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('name' => $status[intval($row['cumulative_action'])], 'align' => 'center'));
             
        }
    $PHPShopInterface->Compile();
}
?>