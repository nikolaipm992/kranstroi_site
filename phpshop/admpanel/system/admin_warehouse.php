<?php

$TitlePage = __("������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

// ��������� ���
function actionStart() {
    global $PHPShopInterface, $PHPShopOrm, $TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'), array('��������'));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'num'), array('limit' => 1000));
    if (is_array($data)) {

        $PHPShopInterface->setCaption(array(null, "3%"), array("��������", "30%"), array("���", "30%"), array("", "10%"), array("������", "10%", array('align' => 'right')));

        foreach ($data as $row) {
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=system.warehouse&id=' . $row['id'], 'align' => 'left'), array('name' => $row['uid']), array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
    } else {
        $PHPShopInterface->sort_action = false;
        $PHPShopInterface->_CODE.= $PHPShopInterface->setAlert('�������������� ������ �� ������, ������������ ����� �������� ����� ��� ����� �������� �������. �������������� ������ ��������� �� ������ <span class="glyphicon glyphicon-plus"></span>. �������� ���� ������� �� ������ ������� ��� ������������� � 1�/�������� ��� �� �������������� ��������.', 'info',true);
    }

    $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './system/'));
    $PHPShopInterface->setSidebarLeft($sidebarleft, 2);
    $PHPShopInterface->Compile(2);
}

?>