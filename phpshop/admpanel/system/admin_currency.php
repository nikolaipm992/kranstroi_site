<?php

$TitlePage = __("������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);

// ��������� ���
function actionStart() {
    global $PHPShopInterface, $PHPShopOrm, $TitlePage;


    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'), array('��������'));
    $PHPShopInterface->setCaption(array(null, "3%"), array("��������", "30%"), array("�����������", "15%"), array("����", "15%"), array("ISO", "10%"), array("���������", "15%", array('align' => 'center')), array("", "10%"), array("������", "10%", array('align' => 'right')));

    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=system.currency&id=' . $row['id'], 'align' => 'left'), $row['code'], $row['kurs'], $row['iso'], array('name' => $row['num'], 'align' => 'center'), array('action' => array('edit', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
        
        $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './system/'));
    $PHPShopInterface->setSidebarLeft($sidebarleft, 2);
    $PHPShopInterface->Compile(2);
}

?>