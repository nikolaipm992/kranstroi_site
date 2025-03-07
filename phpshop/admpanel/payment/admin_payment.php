<?php

$TitlePage = __("������� �����");

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'), array('��������'), false);
    $PHPShopInterface->setCaption(array(null, "3%"), array("��������", "40%"), array("����", "10%", array('sort' => 'none')), array("��� �����������", "20%"), array("���������", "10%", array('align' => 'center')), array("", "10%"), array("������", "10%", array('align' => 'right')));
    
    $path_name = array(
        "message" => __("���������"),
        "bank" => __("���� � ����"),
        "modules" => __("������ ��������� �������")
    );

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'num'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {
            $color = '<span class="glyphicon glyphicon-text-background" style="color:' . $row['color'] . '"></span>';
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=payment&id=' . $row['id'], 'align' => 'left'), $color, $path_name[$row['path']], array('name' => $row['num'], 'align' => 'center'), array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
    $PHPShopInterface->Compile();
}

?>